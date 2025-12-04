/**
 * Classe DataGrid
 * 
 * Componente para gerenciar exibição de dados filtráveis com paginação infinita.
 * Busca dados JSON de um endpoint GET e renderiza no container especificado.
 */

class DataGrid {

    // Configurações do DataGrid
    endpoint = null;
    metodo = 'GET';
    seletorContainer = null;
    seletorTemplate = null;
    
    // Campos de filtragem
    camposFiltro = {};
    
    // Configurações de paginação
    itensPorPagina = 15;
    paginaAtual = 1;
    carregando = false;
    temMaisItens = true;
    
    // Dados carregados
    dados = [];
    
    // Controle de UI
    exibirLoader = true;
    seletorLoader = null;
    
    // Mensagens customizadas
    mensagens = {
        vazio: 'Nenhum registro encontrado.',
        erro: 'Erro ao carregar os dados. Tente novamente.'
    };
    
    // Seletores de elementos de mensagem
    seletorMensagemVazio = null;
    seletorMensagemErro = null;
    
    // Debounce timers
    timeoutFiltro = null;
    timeoutScroll = null;
    debounceDelay = 1000;
    
    // Callbacks
    callbacks = {
        beforeLoad: null,
        onComplete: null,
        onError: null,
        onItemRender: null,
        onLoadMore: null,
        onFilter: null
    };

    /**
     * Construtor do DataGrid
     * 
     * @param {Object} config - Configurações do DataGrid
     * @param {string} config.endpoint - URL do endpoint para buscar dados (obrigatório)
     * @param {string} config.container - Seletor CSS do container onde os dados serão renderizados (obrigatório)
     * @param {string} config.template - Seletor CSS do template HTML para renderização (obrigatório)
     * @param {Object} config.campos - Objeto com seletores dos campos de filtro (ex: { busca: '#input-busca', status: '#select-status' })
     * @param {string} config.metodo - Método HTTP para a requisição (padrão: GET)
     * @param {number} config.itensPorPagina - Quantidade de itens por página (padrão: 15)
     * @param {boolean} config.exibirLoader - Se deve exibir loader durante carregamento (padrão: true)
     * @param {string} config.seletorLoader - Seletor CSS do elemento loader customizado
     * @param {number} config.debounceDelay - Delay em ms para debounce de filtros (padrão: 1000)
     * @param {Object} config.mensagens - Mensagens customizadas (texto ou HTML inline)
     * @param {string} config.mensagens.vazio - Mensagem quando não há registros (padrão: 'Nenhum registro encontrado.')
     * @param {string} config.mensagens.erro - Mensagem de erro ao carregar (padrão: 'Erro ao carregar os dados. Tente novamente.')
     * @param {string} config.seletorMensagemVazio - Seletor CSS de elemento HTML existente para mensagem vazia (substitui mensagens.vazio)
     * @param {string} config.seletorMensagemErro - Seletor CSS de elemento HTML existente para mensagem de erro (substitui mensagens.erro)
     * @param {Object} config.callbacks - Callbacks para eventos do DataGrid
     * @param {Function} config.callbacks.beforeLoad - Executado antes de carregar dados
     * @param {Function} config.callbacks.onComplete - Executado após carregar dados com sucesso
     * @param {Function} config.callbacks.onError - Executado em caso de erro
     * @param {Function} config.callbacks.onItemRender - Executado para cada item renderizado
     * @param {Function} config.callbacks.onLoadMore - Executado ao carregar mais itens (scroll infinito)
     * @param {Function} config.callbacks.onFilter - Executado quando um filtro é aplicado
     */
    constructor(config = {}) {
        // Validações obrigatórias
        if (!config.endpoint) {
            throw new Error('[DataGrid] O parâmetro "endpoint" é obrigatório.');
        }
        if (!config.container) {
            throw new Error('[DataGrid] O parâmetro "container" é obrigatório.');
        }
        if (!config.template) {
            throw new Error('[DataGrid] O parâmetro "template" é obrigatório.');
        }

        // Configurações básicas
        this.endpoint = config.endpoint;
        this.seletorContainer = config.container;
        this.seletorTemplate = config.template;
        this.metodo = config.metodo || 'GET';
        
        // Configurações de paginação
        this.itensPorPagina = config.itensPorPagina || 15;
        
        // Configurações de UI
        this.exibirLoader = config.exibirLoader !== false;
        this.seletorLoader = config.seletorLoader || null;
        this.debounceDelay = config.debounceDelay || 1000;
        
        // Mensagens customizadas
        if (config.mensagens) {
            this.mensagens = { ...this.mensagens, ...config.mensagens };
        }
        
        // Seletores de elementos de mensagem customizados
        this.seletorMensagemVazio = config.seletorMensagemVazio || null;
        this.seletorMensagemErro = config.seletorMensagemErro || null;
        
        // Callbacks
        if (config.callbacks) {
            this.callbacks = { ...this.callbacks, ...config.callbacks };
        }
        
        // Configurar campos de filtro
        if (config.campos) {
            this._configurarCamposFiltro(config.campos);
        }
        
        // Validar elementos DOM
        this._validarElementos();
        
        // Inicializar
        this._configurarScrollInfinito();
    }

    /**
     * Valida se os elementos DOM necessários existem
     */
    _validarElementos() {
        const container = document.querySelector(this.seletorContainer);
        if (!container) {
            throw new Error(`[DataGrid] Container "${this.seletorContainer}" não encontrado no DOM.`);
        }

        const template = document.querySelector(this.seletorTemplate);
        if (!template) {
            throw new Error(`[DataGrid] Template "${this.seletorTemplate}" não encontrado no DOM.`);
        }

        if (template.tagName !== 'TEMPLATE') {
            console.warn(`[DataGrid] Elemento "${this.seletorTemplate}" não é uma tag <template>. Isso pode causar problemas.`);
        }
    }

    /**
     * Configura os campos de filtro e seus event listeners
     * 
     * @param {Object} campos - Objeto com pares chave-seletor
     */
    _configurarCamposFiltro(campos) {
        Object.entries(campos).forEach(([chave, seletor]) => {
            const elemento = document.querySelector(seletor);
            
            if (!elemento) {
                console.warn(`[DataGrid] Campo de filtro "${seletor}" não encontrado no DOM.`);
                return;
            }

            // Armazena a referência do campo
            this.camposFiltro[chave] = {
                seletor: seletor,
                elemento: elemento,
                tipo: this._detectarTipoCampo(elemento)
            };

            // Adiciona event listeners baseado no tipo de campo
            this._adicionarListenersCampo(chave, elemento);
        });
    }

    /**
     * Detecta o tipo de campo (input, select, etc)
     */
    _detectarTipoCampo(elemento) {
        const tagName = elemento.tagName.toLowerCase();
        
        if (tagName === 'select') return 'select';
        if (tagName === 'input') {
            const type = elemento.type.toLowerCase();
            if (type === 'checkbox') return 'checkbox';
            if (type === 'radio') return 'radio';
            return 'input';
        }
        if (tagName === 'textarea') return 'textarea';
        
        return 'custom';
    }

    /**
     * Adiciona event listeners apropriados para o campo
     */
    _adicionarListenersCampo(chave, elemento) {
        const tipo = this.camposFiltro[chave].tipo;

        switch (tipo) {
            case 'input':
            case 'textarea':
                // Input com debounce
                elemento.addEventListener('input', () => this._handleFiltroInput(chave));
                // Enter para busca imediata
                elemento.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.keyCode === 13) {
                        e.preventDefault();
                        clearTimeout(this.timeoutFiltro);
                        this._aplicarFiltros();
                    }
                });
                break;

            case 'select':
            case 'checkbox':
            case 'radio':
                // Change sem debounce (mudança imediata)
                elemento.addEventListener('change', () => this._aplicarFiltros());
                break;

            default:
                // Custom: adiciona listener genérico
                elemento.addEventListener('change', () => this._aplicarFiltros());
                break;
        }
    }

    /**
     * Manipula input de campos de texto com debounce
     */
    _handleFiltroInput(chave) {
        clearTimeout(this.timeoutFiltro);
        
        this.timeoutFiltro = setTimeout(() => {
            this._aplicarFiltros();
        }, this.debounceDelay);
    }

    /**
     * Aplica os filtros e recarrega os dados
     */
    async _aplicarFiltros() {
        console.log('[DataGrid] Aplicando filtros...');
        
        // Reseta paginação
        this._resetarPaginacao();
        
        // Obtém valores dos filtros
        const parametros = this._obterParametrosFiltro();
        
        // Executa callback onFilter
        if (this.callbacks.onFilter && typeof this.callbacks.onFilter === 'function') {
            try {
                await this.callbacks.onFilter(parametros);
            } catch (error) {
                console.error('[DataGrid] Erro no callback onFilter:', error);
            }
        }
        
        // Carrega dados
        await this._carregarDados(parametros);
    }

    /**
     * Obtém os valores atuais dos campos de filtro
     * 
     * @returns {Object} Objeto com os parâmetros de filtro
     */
    _obterParametrosFiltro() {
        const parametros = {
            pagina: this.paginaAtual,
            limite: this.itensPorPagina
        };

        // Adiciona valores dos campos configurados
        Object.entries(this.camposFiltro).forEach(([chave, config]) => {
            const valor = this._obterValorCampo(config.elemento, config.tipo);
            if (valor !== null && valor !== undefined && valor !== '') {
                parametros[chave] = valor;
            }
        });

        return this._sanitizarParametros(parametros);
    }

    /**
     * Sanitiza os parâmetros para envio seguro via URL
     * Remove caracteres perigosos e normaliza valores
     * 
     * @param {Object} parametros - Parâmetros a serem sanitizados
     * @returns {Object} Parâmetros sanitizados
     */
    _sanitizarParametros(parametros) {
        const sanitizados = {};

        Object.entries(parametros).forEach(([chave, valor]) => {
            // Sanitiza a chave do parâmetro
            const chaveSanitizada = this._sanitizarChave(chave);

            // Sanitiza o valor baseado no tipo
            const valorSanitizado = this._sanitizarValor(valor);

            // Adiciona apenas se o valor sanitizado for válido
            if (valorSanitizado !== null && valorSanitizado !== undefined && valorSanitizado !== '') {
                sanitizados[chaveSanitizada] = valorSanitizado;
            }
        });

        return sanitizados;
    }

    /**
     * Sanitiza a chave de um parâmetro
     * Remove caracteres especiais mantendo apenas letras, números, traço e underscore
     * 
     * @param {string} chave - Chave a ser sanitizada
     * @returns {string} Chave sanitizada
     */
    _sanitizarChave(chave) {
        return String(chave)
            .trim()
            .replace(/[^a-zA-Z0-9_\-]/g, '_') // Substitui caracteres especiais por underscore
            .replace(/_{2,}/g, '_') // Remove underscores duplicados
            .replace(/^_+|_+$/g, ''); // Remove underscores do início e fim
    }

    /**
     * Sanitiza o valor de um parâmetro baseado no tipo
     * 
     * @param {*} valor - Valor a ser sanitizado
     * @returns {*} Valor sanitizado
     */
    _sanitizarValor(valor) {
        // Null ou undefined
        if (valor === null || valor === undefined) {
            return null;
        }

        // Boolean
        if (typeof valor === 'boolean') {
            return valor ? '1' : '0';
        }

        // Number
        if (typeof valor === 'number') {
            // Verifica se é um número válido
            if (isNaN(valor) || !isFinite(valor)) {
                return null;
            }
            return valor;
        }

        // Array (converte para string separada por vírgulas)
        if (Array.isArray(valor)) {
            return valor
                .filter(item => item !== null && item !== undefined)
                .map(item => this._sanitizarValor(item))
                .filter(item => item !== null && item !== '')
                .join(',');
        }

        // Object (converte para JSON string)
        if (typeof valor === 'object') {
            try {
                return JSON.stringify(valor);
            } catch (error) {
                console.warn('[DataGrid] Não foi possível converter objeto para JSON:', error);
                return null;
            }
        }

        // String (caso padrão)
        const valorString = String(valor).trim();

        // String vazia
        if (valorString === '') {
            return null;
        }

        // Remove caracteres de controle e caracteres invisíveis
        let sanitizado = valorString.replace(/[\x00-\x1F\x7F]/g, '');

        // Remove múltiplos espaços
        sanitizado = sanitizado.replace(/\s+/g, ' ');

        // Previne injeção de HTML/Script (básico)
        // Nota: O servidor deve fazer validação mais rigorosa
        sanitizado = sanitizado
            .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
            .replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '');

        // Limita o tamanho máximo (previne ataques de buffer)
        const tamanhoMaximo = 1000;
        if (sanitizado.length > tamanhoMaximo) {
            console.warn(`[DataGrid] Valor muito longo truncado (${sanitizado.length} chars)`);
            sanitizado = sanitizado.substring(0, tamanhoMaximo);
        }

        return sanitizado;
    }

    /**
     * Obtém o valor de um campo baseado em seu tipo
     */
    _obterValorCampo(elemento, tipo) {
        switch (tipo) {
            case 'checkbox':
                return elemento.checked;
            case 'radio':
                // Para radio buttons, busca o selecionado no grupo
                const radioSelecionado = document.querySelector(`input[name="${elemento.name}"]:checked`);
                return radioSelecionado ? radioSelecionado.value : null;
            case 'select':
            case 'input':
            case 'textarea':
                return elemento.value;
            default:
                return elemento.value || elemento.textContent;
        }
    }

    /**
     * Configura o scroll infinito
     */
    _configurarScrollInfinito() {
        window.addEventListener('scroll', () => {
            clearTimeout(this.timeoutScroll);
            
            this.timeoutScroll = setTimeout(() => {
                this._handleScroll();
            }, 100);
        });
    }

    /**
     * Manipula o evento de scroll
     */
    _handleScroll() {
        // Verifica se pode carregar mais
        if (this.carregando || !this.temMaisItens) {
            return;
        }

        const container = document.querySelector(this.seletorContainer);
        if (!container || container.children.length === 0) {
            return;
        }

        // Calcula posição do scroll
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const margemFinal = 200;

        // Verifica se chegou perto do fim
        if (scrollTop + windowHeight >= documentHeight - margemFinal) {
            if (!this.carregando && this.temMaisItens) {
                console.log('[DataGrid] Scroll detectado - carregando mais itens');
                this._carregarMaisItens();
            }
        }
    }

    /**
     * Carrega mais itens (próxima página)
     */
    async _carregarMaisItens() {
        if (this.carregando || !this.temMaisItens) {
            return;
        }

        this.carregando = true;
        this.paginaAtual++;

        const loader = this.exibirLoader ? this._criarLoaderInline() : null;

        // Executa callback onLoadMore
        if (this.callbacks.onLoadMore && typeof this.callbacks.onLoadMore === 'function') {
            try {
                await this.callbacks.onLoadMore(this.paginaAtual);
            } catch (error) {
                console.error('[DataGrid] Erro no callback onLoadMore:', error);
            }
        }

        try {
            const parametros = this._obterParametrosFiltro();
            const resultado = await this._buscarDados(parametros);
            
            const novosItens = resultado.data || [];
            
            console.log('[DataGrid] Informações de paginação:', {
                current_page: resultado.current_page,
                last_page: resultado.last_page,
                total: resultado.total,
                items_carregados: novosItens.length
            });

            // Verifica se chegou na última página
            if (resultado.current_page >= resultado.last_page || novosItens.length === 0) {
                console.log('[DataGrid] Última página alcançada.');
                this.temMaisItens = false;
            }

            if (novosItens.length > 0) {
                this.dados.push(...novosItens);
                
                const indexInicial = this.dados.length - novosItens.length;
                novosItens.forEach((item, index) => {
                    this._renderizarItem(item, indexInicial + index);
                });
            }

            // Executa callback onComplete
            if (this.callbacks.onComplete && typeof this.callbacks.onComplete === 'function') {
                try {
                    await this.callbacks.onComplete(novosItens, parametros);
                } catch (error) {
                    console.error('[DataGrid] Erro no callback onComplete:', error);
                }
            }

            if (loader) {
                this._removerLoader(loader);
            }
        } catch (error) {
            console.error('[DataGrid] Erro ao carregar mais itens:', error);
            this.paginaAtual--;
            
            if (loader) {
                this._removerLoader(loader);
            }

            // Executa callback onError
            if (this.callbacks.onError && typeof this.callbacks.onError === 'function') {
                try {
                    await this.callbacks.onError(error, { pagina: this.paginaAtual });
                } catch (callbackError) {
                    console.error('[DataGrid] Erro no callback onError:', callbackError);
                }
            }
        } finally {
            this.carregando = false;
        }
    }

    /**
     * Carrega os dados (primeira carga ou recarga com filtros)
     */
    async _carregarDados(parametros = {}) {
        this._limparContainer();
        
        const loader = this.exibirLoader ? this._criarLoader() : null;
        this.carregando = true;

        // Executa callback beforeLoad
        if (this.callbacks.beforeLoad && typeof this.callbacks.beforeLoad === 'function') {
            try {
                await this.callbacks.beforeLoad(parametros);
            } catch (error) {
                console.error('[DataGrid] Erro no callback beforeLoad:', error);
            }
        }

        try {
            const resultado = await this._buscarDados(parametros);
            const itens = resultado.data || [];

            console.log('[DataGrid] Dados carregados:', {
                current_page: resultado.current_page,
                last_page: resultado.last_page,
                total: resultado.total,
                items_carregados: itens.length
            });

            if (itens.length === 0) {
                this.dados = [];
                this.temMaisItens = false;
                this._exibirMensagemVazio();
            } else {
                this.dados = itens;
                itens.forEach((item, index) => {
                    this._renderizarItem(item, index);
                });

                // Verifica se há mais páginas
                if (resultado.current_page >= resultado.last_page) {
                    this.temMaisItens = false;
                    console.log('[DataGrid] Não há mais páginas para carregar.');
                }
            }

            // Executa callback onComplete
            if (this.callbacks.onComplete && typeof this.callbacks.onComplete === 'function') {
                try {
                    await this.callbacks.onComplete(itens, parametros, resultado);
                } catch (error) {
                    console.error('[DataGrid] Erro no callback onComplete:', error);
                }
            }
        } catch (error) {
            console.error('[DataGrid] Erro ao carregar dados:', error);
            this._exibirMensagemErro();

            // Executa callback onError
            if (this.callbacks.onError && typeof this.callbacks.onError === 'function') {
                try {
                    await this.callbacks.onError(error, parametros);
                } catch (callbackError) {
                    console.error('[DataGrid] Erro no callback onError:', callbackError);
                }
            }
        } finally {
            this.carregando = false;
            if (loader) {
                this._removerLoader(loader);
            }
        }
    }

    /**
     * Busca dados do endpoint
     * 
     * @param {Object} parametros - Parâmetros da requisição
     * @returns {Promise<Object>} Resultado com dados e informações de paginação
     */
    async _buscarDados(parametros = {}) {
        try {
            const response = await $.ajax({
                url: this.endpoint,
                method: this.metodo,
                dataType: 'json',
                data: parametros,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                xhrFields: {
                    withCredentials: true
                }
            });

            console.log(`[DataGrid] Resposta recebida de "${this.endpoint}":`, response);

            // Suporta diferentes formatos de resposta
            const dados = response.data || response.items || response.results || [];

            return {
                data: Array.isArray(dados) ? dados : [],
                current_page: response.current_page || parametros.pagina || 1,
                last_page: response.last_page || 1,
                total: response.total || (Array.isArray(dados) ? dados.length : 0)
            };
        } catch (error) {
            console.error('[DataGrid] Erro ao buscar dados:', error);
            throw error;
        }
    }

    /**
     * Renderiza um item no container
     * 
     * @param {Object} item - Dados do item
     * @param {number} index - Índice do item
     */
    _renderizarItem(item, index = 0) {
        const container = document.querySelector(this.seletorContainer);
        if (!container) {
            console.error(`[DataGrid] Container "${this.seletorContainer}" não encontrado.`);
            return;
        }

        const elemento = this._criarElementoItem(item, index);
        container.appendChild(elemento);
    }

    /**
     * Cria um elemento a partir do template sem adicionar ao DOM
     * Útil para adicionar múltiplos itens em lote com DocumentFragment
     * 
     * @param {Object} item - Dados do item
     * @param {number} index - Índice do item
     * @returns {HTMLElement} Elemento criado
     */
    _criarElementoItem(item, index = 0) {
        const template = document.querySelector(this.seletorTemplate);
        if (!template) {
            console.error(`[DataGrid] Template "${this.seletorTemplate}" não encontrado.`);
            return null;
        }

        const clone = template.content.cloneNode(true);

        // Substitui placeholders {{chave}} pelos valores do item
        this._substituirPlaceholders(clone, item);

        // Cria um container temporário para extrair o elemento
        const tempContainer = document.createElement('div');
        tempContainer.appendChild(clone);
        const elementoRenderizado = tempContainer.firstElementChild;

        if (!elementoRenderizado) {
            console.error('[DataGrid] Erro ao criar elemento do template');
            return null;
        }

        // Executa callback onItemRender
        if (this.callbacks.onItemRender && typeof this.callbacks.onItemRender === 'function') {
            try {
                this.callbacks.onItemRender(item, elementoRenderizado, index);
            } catch (error) {
                console.error('[DataGrid] Erro no callback onItemRender:', error);
            }
        }

        // Animação de entrada
        this._animarEntrada(elementoRenderizado, index);

        return elementoRenderizado;
    }

    /**
     * Substitui placeholders {{chave}} no template pelos valores do item
     */
    _substituirPlaceholders(clone, item) {
        const regex = /\{\{([a-zA-Z0-9_.]+)\}\}/g;
        const todosElementos = clone.querySelectorAll('*');

        todosElementos.forEach(elemento => {
            // Processa atributos
            Array.from(elemento.attributes).forEach(attr => {
                const valorOriginal = attr.value;
                const valorDecodificado = this._decodificarChaves(valorOriginal);

                if (regex.test(valorDecodificado)) {
                    const novoValor = valorDecodificado.replace(regex, (match, caminho) => {
                        return this._obterValorPropriedade(item, caminho);
                    });

                    elemento.setAttribute(attr.name, novoValor);
                }
            });

            // Processa TextNodes
            Array.from(elemento.childNodes).forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const textoOriginal = node.textContent;
                    const textoDecodificado = this._decodificarChaves(textoOriginal);

                    if (regex.test(textoDecodificado)) {
                        node.textContent = textoDecodificado.replace(regex, (match, caminho) => {
                            return this._obterValorPropriedade(item, caminho);
                        });
                    }
                }
            });

            // Processa innerHTML para elementos sem filhos
            if (elemento.children.length === 0) {
                const htmlOriginal = elemento.innerHTML;
                const htmlDecodificado = this._decodificarChaves(htmlOriginal);

                if (regex.test(htmlDecodificado)) {
                    elemento.innerHTML = htmlDecodificado.replace(regex, (match, caminho) => {
                        return this._obterValorPropriedade(item, caminho);
                    });
                }
            }
        });
    }

    /**
     * Obtém valor de uma propriedade usando notação de ponto
     */
    _obterValorPropriedade(obj, caminho) {
        const caminhoNormalizado = caminho.trim();
        const partes = caminhoNormalizado.split('.');
        let valor = obj;

        for (const parte of partes) {
            if (valor == null || typeof valor !== 'object') {
                return '[não informado]';
            }

            if (parte in valor) {
                valor = valor[parte];
            } else {
                // Tenta converter de camelCase para snake_case
                const parteSnakeCase = parte.replace(/([A-Z])/g, '_$1').toLowerCase();
                if (parteSnakeCase in valor) {
                    valor = valor[parteSnakeCase];
                } else {
                    return '[não informado]';
                }
            }
        }

        return valor == null ? '[não informado]' : String(valor);
    }

    /**
     * Decodifica %7B e %7D para { e }
     */
    _decodificarChaves(str) {
        return str.replace(/%7B/gi, '{').replace(/%7D/gi, '}');
    }

    /**
     * Anima a entrada de um elemento
     */
    _animarEntrada(elemento, index) {
        if (!elemento) return;

        // Se está usando renderização progressiva, não anima aqui
        if (this._usandoRenderizacaoProgressiva) {
            return;
        }

        elemento.style.opacity = '0';
        elemento.style.transform = 'translateY(20px)';

        const delayIndex = index % this.itensPorPagina;

        setTimeout(() => {
            elemento.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            elemento.style.opacity = '1';
            elemento.style.transform = 'translateY(0)';

            setTimeout(() => {
                elemento.style.transition = '';
                elemento.style.transform = '';
            }, 400);
        }, delayIndex * 50);
    }

    /**
     * Limpa o container
     */
    _limparContainer() {
        const container = document.querySelector(this.seletorContainer);
        if (container) {
            container.innerHTML = '';
        }

        // Esconde elementos de mensagem customizados se existirem
        if (this.seletorMensagemVazio) {
            const elementoVazio = document.querySelector(this.seletorMensagemVazio);
            if (elementoVazio) {
                elementoVazio.classList.add('hidden');
                elementoVazio.style.display = 'none';
            }
        }

        if (this.seletorMensagemErro) {
            const elementoErro = document.querySelector(this.seletorMensagemErro);
            if (elementoErro) {
                elementoErro.classList.add('hidden');
                elementoErro.style.display = 'none';
            }
        }
    }

    /**
     * Reseta a paginação
     */
    _resetarPaginacao() {
        this.paginaAtual = 1;
        this.temMaisItens = true;
        this.carregando = false;
    }

    /**
     * Cria loader principal
     */
    _criarLoader() {
        const container = document.querySelector(this.seletorContainer);
        if (!container) return null;

        // Se há um loader customizado configurado, usa ele
        if (this.seletorLoader) {
            const loaderCustomizado = document.querySelector(this.seletorLoader);
            if (loaderCustomizado) {
                loaderCustomizado.classList.remove('hidden');
                loaderCustomizado.style.display = '';
                return loaderCustomizado;
            }
        }

        // Cria loader padrão
        const loader = document.createElement('div');
        loader.setAttribute('data-datagrid-loader', '');
        loader.className = 'text-center mt-8 py-4';
        loader.style.display = '';

        const icon = document.createElement('span');
        icon.className = 'material-icons-sharp text-5xl text-gray-400 animate-spin';
        icon.textContent = 'sync';

        loader.appendChild(icon);
        container.appendChild(loader);

        return loader;
    }

    /**
     * Cria loader inline (para scroll infinito)
     */
    _criarLoaderInline() {
        const container = document.querySelector(this.seletorContainer);
        if (!container) return null;

        const loader = document.createElement('div');
        loader.setAttribute('data-datagrid-loader-inline', '');
        loader.className = 'text-center py-4';
        loader.style.display = '';

        const icon = document.createElement('span');
        icon.className = 'material-icons-sharp text-3xl text-gray-400 animate-spin';
        icon.textContent = 'sync';

        const text = document.createElement('p');
        text.className = 'text-sm text-gray-500 mt-2';
        text.textContent = 'Carregando mais registros...';

        loader.appendChild(icon);
        loader.appendChild(text);
        container.appendChild(loader);

        return loader;
    }

    /**
     * Remove loader do DOM
     */
    _removerLoader(loader) {
        if (!loader) return;
        
        // Se for loader customizado, apenas oculta
        if (loader === document.querySelector(this.seletorLoader)) {
            loader.classList.add('hidden');
            loader.style.display = 'none';
        } else if (loader.parentNode) {
            loader.parentNode.removeChild(loader);
        }
    }

    /**
     * Exibe mensagem de container vazio
     */
    _exibirMensagemVazio() {
        const container = document.querySelector(this.seletorContainer);
        if (!container) return;

        // Se houver um seletor de elemento customizado, usa ele
        if (this.seletorMensagemVazio) {
            const elementoVazio = document.querySelector(this.seletorMensagemVazio);
            if (elementoVazio) {
                // Remove a classe 'hidden' se existir
                elementoVazio.classList.remove('hidden');
                elementoVazio.style.display = '';
                return;
            } else {
                console.warn(`[DataGrid] Elemento de mensagem vazia "${this.seletorMensagemVazio}" não encontrado.`);
            }
        }

        // Caso contrário, usa a mensagem inline
        container.innerHTML = `<p class="text-center text-gray-500 mt-8">${this.mensagens.vazio}</p>`;
    }

    /**
     * Exibe mensagem de erro
     */
    _exibirMensagemErro() {
        const container = document.querySelector(this.seletorContainer);
        if (!container) return;

        // Se houver um seletor de elemento customizado, usa ele
        if (this.seletorMensagemErro) {
            const elementoErro = document.querySelector(this.seletorMensagemErro);
            if (elementoErro) {
                // Remove a classe 'hidden' se existir
                elementoErro.classList.remove('hidden');
                elementoErro.style.display = '';
                return;
            } else {
                console.warn(`[DataGrid] Elemento de mensagem de erro "${this.seletorMensagemErro}" não encontrado.`);
            }
        }

        // Caso contrário, usa a mensagem inline
        container.innerHTML = `<p class="text-center text-red-500 mt-8">${this.mensagens.erro}</p>`;
    }

    /**
     * Recarrega os dados do início
     */
    async recarregar() {
        console.log('[DataGrid] Recarregando dados...');
        this._resetarPaginacao();
        const parametros = this._obterParametrosFiltro();
        await this._carregarDados(parametros);
    }

    /**
     * Carrega dados iniciais
     */
    async carregar() {
        const parametros = this._obterParametrosFiltro();
        await this._carregarDados(parametros);
    }

    /**
     * Ativa o modo de renderização progressiva em lotes
     * Busca todos os dados de uma vez e renderiza em lotes menores
     * 
     * @param {number} tamanhoDeLote - Quantidade de itens por lote (padrão: 20)
     * @param {number} delayEntreLotes - Delay em ms entre lotes (padrão: 50)
     */
    ativarRenderizacaoProgressiva(tamanhoDeLote = 20, delayEntreLotes = 50) {
        this._usandoRenderizacaoProgressiva = true;
        this._tamanhoDeLote = tamanhoDeLote;
        this._delayEntreLotes = delayEntreLotes;
        this._todosOsDados = [];
        this._itensRenderizados = 0;

        console.log('[DataGrid] Modo de renderização progressiva ativado', {
            tamanhoDeLote,
            delayEntreLotes
        });
    }

    /**
     * Carrega dados com renderização progressiva
     */
    async _carregarDadosProgressivamente(parametros = {}) {
        this._limparContainer();
        
        const loader = this.exibirLoader ? this._criarLoader() : null;
        this.carregando = true;

        // Executa callback beforeLoad
        if (this.callbacks.beforeLoad && typeof this.callbacks.beforeLoad === 'function') {
            try {
                await this.callbacks.beforeLoad(parametros);
            } catch (error) {
                console.error('[DataGrid] Erro no callback beforeLoad:', error);
            }
        }

        try {
            // Busca todos os dados de uma vez
            const resultado = await this._buscarDados(parametros);
            this._todosOsDados = resultado.data || [];

            console.log(`[DataGrid] ${this._todosOsDados.length} itens carregados para renderização progressiva`);

            // Esconde o loader
            if (loader) {
                this._removerLoader(loader);
            }

            if (this._todosOsDados.length === 0) {
                this.dados = [];
                this.temMaisItens = false;
                this._exibirMensagemVazio();
            } else {
                this.dados = this._todosOsDados;
                this._itensRenderizados = 0;
                
                // Inicia renderização progressiva
                this._renderizarProximoLote();
            }

            // Executa callback onComplete
            if (this.callbacks.onComplete && typeof this.callbacks.onComplete === 'function') {
                try {
                    await this.callbacks.onComplete(this._todosOsDados, parametros);
                } catch (error) {
                    console.error('[DataGrid] Erro no callback onComplete:', error);
                }
            }
        } catch (error) {
            console.error('[DataGrid] Erro ao carregar dados:', error);
            this._exibirMensagemErro();

            if (loader) {
                this._removerLoader(loader);
            }

            // Executa callback onError
            if (this.callbacks.onError && typeof this.callbacks.onError === 'function') {
                try {
                    await this.callbacks.onError(error, parametros);
                } catch (callbackError) {
                    console.error('[DataGrid] Erro no callback onError:', callbackError);
                }
            }
        } finally {
            this.carregando = false;
        }
    }

    /**
     * Renderiza o próximo lote de itens
     */
    _renderizarProximoLote() {
        if (!this._usandoRenderizacaoProgressiva) return;

        const inicio = this._itensRenderizados;
        const fim = Math.min(inicio + this._tamanhoDeLote, this._todosOsDados.length);

        // Renderiza o lote atual
        for (let i = inicio; i < fim; i++) {
            const item = this._todosOsDados[i];
            this._renderizarItem(item, i);
        }

        this._itensRenderizados = fim;

        // Se ainda há mais itens, agenda o próximo lote
        if (this._itensRenderizados < this._todosOsDados.length) {
            requestAnimationFrame(() => {
                setTimeout(() => {
                    this._renderizarProximoLote();
                }, this._delayEntreLotes);
            });
        } else {
            console.log(`[DataGrid] Renderização progressiva concluída: ${this._itensRenderizados} itens`);
        }
    }

    /**
     * Sobrescreve o método carregar quando usando renderização progressiva
     */
    async carregar() {
        if (this._usandoRenderizacaoProgressiva) {
            const parametros = this._obterParametrosFiltro();
            await this._carregarDadosProgressivamente(parametros);
        } else {
            const parametros = this._obterParametrosFiltro();
            await this._carregarDados(parametros);
        }
    }

    /**
     * Sobrescreve o método recarregar quando usando renderização progressiva
     */
    async recarregar() {
        console.log('[DataGrid] Recarregando dados...');
        this._resetarPaginacao();
        
        if (this._usandoRenderizacaoProgressiva) {
            const parametros = this._obterParametrosFiltro();
            await this._carregarDadosProgressivamente(parametros);
        } else {
            const parametros = this._obterParametrosFiltro();
            await this._carregarDados(parametros);
        }
    }

    /**
     * Obtém todos os dados carregados
     * 
     * @returns {Array} Array com os dados
     */
    obterDados() {
        return this.dados;
    }

    /**
     * Define um valor para um campo de filtro
     * 
     * @param {string} chave - Chave do campo
     * @param {*} valor - Valor a ser definido
     */
    definirFiltro(chave, valor) {
        if (!this.camposFiltro[chave]) {
            console.warn(`[DataGrid] Campo de filtro "${chave}" não encontrado.`);
            return;
        }

        const campo = this.camposFiltro[chave];
        
        switch (campo.tipo) {
            case 'checkbox':
                campo.elemento.checked = Boolean(valor);
                break;
            case 'radio':
                const radio = document.querySelector(`input[name="${campo.elemento.name}"][value="${valor}"]`);
                if (radio) radio.checked = true;
                break;
            default:
                campo.elemento.value = valor;
                break;
        }
    }

    /**
     * Limpa todos os filtros
     */
    limparFiltros() {
        Object.values(this.camposFiltro).forEach(campo => {
            switch (campo.tipo) {
                case 'checkbox':
                    campo.elemento.checked = false;
                    break;
                case 'radio':
                    const radios = document.querySelectorAll(`input[name="${campo.elemento.name}"]`);
                    radios.forEach(r => r.checked = false);
                    break;
                default:
                    campo.elemento.value = '';
                    break;
            }
        });
    }
}
