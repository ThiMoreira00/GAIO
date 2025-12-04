/**
 * Classe Tab2
 * 
 * Gerenciador de abas com suporte a scroll infinito e paginação automática
 */

class Tab2 {

    // Atributos
    prefixo = 'tab';
    seletor = null;
    seletorConteudo = null;
    seletorTemplateRegistros = null;
    url = null;
    metodo = 'GET';
    resultadosPorPagina = 15;
    loading = true;
    dados = [];
    timeoutBusca = null;
    timeoutScroll = null;

    // Controle de paginação
    paginaAtual = 1;
    carregandoMais = false;
    temMaisRegistros = true;

    // Eventos
    beforeLoad = null;
    onComplete = null;
    onError = null;
    onItemRender = null;
    onLoadMore = null;

    /**
     * Construtor da classe Tab2
     * 
     * @param {string} seletor - Seletor CSS da aba
     * @param {Object} opcoes - Configurações opcionais
     * @param {string} opcoes.prefixo - Prefixo para classes CSS
     * @param {string} opcoes.seletorConteudo - Seletor do container de conteúdo
     * @param {string} opcoes.seletorLoader - Seletor do loader
     * @param {string} opcoes.seletorTemplateRegistros - Seletor do template
     * @param {string} opcoes.url - URL para carregar dados
     * @param {string} opcoes.metodo - Método HTTP (GET, POST, etc)
     * @param {number} opcoes.resultadosPorPagina - Quantidade de resultados por página
     * @param {boolean} opcoes.loading - Exibir loader durante carregamento
     * @param {Function} opcoes.beforeLoad - Callback antes de carregar dados
     * @param {Function} opcoes.onComplete - Callback ao completar carregamento
     * @param {Function} opcoes.onError - Callback em caso de erro
     * @param {Function} opcoes.onItemRender - Callback executado para cada item renderizado
     * @param {Function} opcoes.onLoadMore - Callback ao carregar mais registros
     */
    constructor(seletor, opcoes = {}) {
        this.prefixo = opcoes.prefixo || 'tab';
        this.seletor = seletor;
        this.seletorConteudo = opcoes.seletorConteudo || `${seletor}-conteudo`;
        this.seletorLoader = (opcoes.loading !== false) ? (opcoes.seletorLoader || `${seletor}-loader`) : null;
        this.seletorTemplateRegistros = opcoes.seletorTemplateRegistros || `${seletor}-template-registros`;
        this.url = opcoes.url || null;
        this.metodo = opcoes.metodo || 'GET';
        this.resultadosPorPagina = opcoes.resultadosPorPagina || 15;
        this.loading = opcoes.loading !== false;

        // Eventos
        this.beforeLoad = opcoes.beforeLoad || null;
        this.onComplete = opcoes.onComplete || null;
        this.onError = opcoes.onError || null;
        this.onItemRender = opcoes.onItemRender || null;
        this.onLoadMore = opcoes.onLoadMore || null;

        this.iniciar();
        this.selecionarPrimeiraAba();
    }

    /**
     * Inicia a funcionalidade da aba
     */
    iniciar() {
        const aba = document.querySelector(this.seletor);
        if (!aba) {
            console.error(`[Tab2] Aba com seletor "${this.seletor}" não encontrada.`);
            return;
        }

        this._configurarEventosAbas();
        this._configurarBusca();
        this._configurarScrollInfinito();
    }

    /**
     * Configura eventos de clique nas abas
     */
    _configurarEventosAbas() {
        document.querySelectorAll(`${this.seletor} button`).forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                this._handleClickAba(button);
            });
        });
    }

    /**
     * Manipula o clique em uma aba
     */
    _handleClickAba(button) {
        if (button.classList.contains('active')) {
            return;
        }

        this._ativarAba(button);
        this._resetarPaginacao();
        const parametros = this._obterParametrosBusca(button);
        this._carregarEExibirConteudo(parametros);
    }

    /**
     * Ativa a aba clicada e desativa as outras
     */
    _ativarAba(button) {
        const abas = document.querySelectorAll(`${this.seletor} button`);
        abas.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    }

    /**
     * Obtém os parâmetros de busca atuais
     */
    _obterParametrosBusca(button = null) {
        const btn = button || document.querySelector(`${this.seletor} button.active`);
        const status = btn?.dataset.tabStatus || '';
        const busca = document.getElementById(`busca-${this.prefixo}`)?.value || '';
        return { status, busca, pagina: this.paginaAtual };
    }

    /**
     * Configura a funcionalidade de busca
     */
    _configurarBusca() {
        const inputBusca = document.querySelector(`[data-tab-search]`);
        if (!inputBusca) return;

        inputBusca.addEventListener('input', (event) => {
            this._handleInputBusca(event);
        });

        inputBusca.addEventListener('keydown', (event) => {
            this._handleKeydownBusca(event, inputBusca);
        });
    }

    /**
     * Manipula o evento de input na busca
     */
    _handleInputBusca(event) {
        clearTimeout(this.timeoutBusca);
        
        this.timeoutBusca = setTimeout(() => {
            const button = document.querySelector(`${this.seletor} button.active`);
            if (!button) {
                console.error(`[Tab2] Nenhuma aba ativa encontrada para busca.`);
                return;
            }

            this._resetarPaginacao();
            const parametros = this._obterParametrosBusca(button);
            this._carregarEExibirConteudo(parametros);
        }, 1000); // 1 segundo após parar de digitar
    }

    /**
     * Manipula o evento de keydown na busca (Enter)
     */
    _handleKeydownBusca(event, inputBusca) {
        if (event.key === 'Enter' || event.keyCode === 13) {
            event.preventDefault();
            clearTimeout(this.timeoutBusca);
            inputBusca.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    /**
     * Configura o scroll infinito
     */
    _configurarScrollInfinito() {
        window.addEventListener('scroll', () => {
            // Debounce: só executa após 100ms sem scroll
            clearTimeout(this.timeoutScroll);
            this.timeoutScroll = setTimeout(() => {
                this._handleScroll();
            }, 100);
        });
    }

    /**
     * Manipula o evento de scroll para carregar mais registros
     */
    _handleScroll() {
        // PRIMEIRA VERIFICAÇÃO: Se já está carregando ou não tem mais registros, ignora IMEDIATAMENTE
        if (this.carregandoMais || !this.temMaisRegistros) {
            return;
        }

        const container = document.querySelector(this.seletorConteudo);
        if (!container) return;

        // Verifica se o container tem conteúdo
        if (container.children.length === 0) return;

        // Calcula se o usuário chegou perto do fim da página
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        // Margem de 200px do final
        const margemFinal = 200;
        
        // SEGUNDA VERIFICAÇÃO: Só dispara se realmente chegou perto do final E não está carregando
        if (scrollTop + windowHeight >= documentHeight - margemFinal) {
            // TERCEIRA VERIFICAÇÃO: Verifica novamente antes de chamar
            if (!this.carregandoMais && this.temMaisRegistros) {
                console.log(`[Tab2] Scroll detectado - disparando carregamento de mais registros`);
                this._carregarMaisRegistros();
            }
        }
    }

    /**
     * Carrega mais registros (próxima página)
     */
    async _carregarMaisRegistros() {
        // Verificação dupla para evitar loops
        if (this.carregandoMais || !this.temMaisRegistros) {
            console.log(`[Tab2] Carregamento bloqueado - carregandoMais: ${this.carregandoMais}, temMaisRegistros: ${this.temMaisRegistros}`);
            return;
        }

        this.carregandoMais = true;
        this.paginaAtual++;

        const loader = this.loading ? this._criarEExibirLoaderInline() : null;

        // Executa callback onLoadMore
        if (this.onLoadMore && typeof this.onLoadMore === 'function') {
            try {
                await this.onLoadMore(this.paginaAtual);
            } catch (error) {
                console.error('[Tab2] Erro no callback onLoadMore:', error);
            }
        }

        try {
            const button = document.querySelector(`${this.seletor} button.active`);
            const parametros = this._obterParametrosBusca(button);
            
            console.log(`[Tab2] Carregando página ${this.paginaAtual}...`);
            
            const resultado = await this.carregarConteudo(parametros);
            const novosConteudos = resultado.data || [];
            
            console.log(`[Tab2] Informações de paginação:`, {
                current_page: resultado.current_page,
                last_page: resultado.last_page,
                total: resultado.total,
                items_carregados: novosConteudos.length
            });
            
            // Verifica se chegou na última página ANTES de processar
            if (resultado.current_page >= resultado.last_page || novosConteudos.length === 0) {
                console.log(`[Tab2] Última página alcançada - bloqueando novas requisições.`);
                this.temMaisRegistros = false;
                
                if (novosConteudos.length === 0) {
                    console.log(`[Tab2] Nenhum registro retornado - fim da paginação.`);
                    // Remove o loader imediatamente quando não há novos registros
                    if (loader) {
                        this._removerLoader(loader);
                    }
                }
            }
            
            if (novosConteudos.length > 0) {
                console.log(`[Tab2] ${novosConteudos.length} novos registros carregados.`);
                this.dados.push(...novosConteudos);
                
                // Renderiza os novos itens com animação
                const container = document.querySelector(this.seletorConteudo);
                const indexInicial = this.dados.length - novosConteudos.length;
                
                novosConteudos.forEach((conteudo, index) => {
                    this.renderizarConteudo(conteudo, indexInicial + index);
                });
                
                // Remove o loader após renderizar os itens
                if (loader) {
                    this._removerLoader(loader);
                }
            }
            
            // Executa callback onComplete
            if (this.onComplete && typeof this.onComplete === 'function') {
                try {
                    await this.onComplete(novosConteudos, parametros);
                } catch (error) {
                    console.error('[Tab2] Erro no callback onComplete:', error);
                }
            }
        } catch (error) {
            console.error(`[Tab2] Erro ao carregar mais registros:`, error);
            this.paginaAtual--; // Volta para a página anterior em caso de erro
            
            // Remove o loader em caso de erro
            if (loader) {
                this._removerLoader(loader);
            }
            
            // Executa callback onError
            if (this.onError && typeof this.onError === 'function') {
                try {
                    await this.onError(error, { pagina: this.paginaAtual });
                } catch (callbackError) {
                    console.error('[Tab2] Erro no callback onError:', callbackError);
                }
            }
        } finally {
            // CRÍTICO: resetar carregandoMais no finally para garantir que sempre seja executado
            this.carregandoMais = false;
            console.log(`[Tab2] Carregamento finalizado - carregandoMais resetado para false`);
        }
    }

    /**
     * Reseta a paginação para o estado inicial
     */
    _resetarPaginacao() {
        this.paginaAtual = 1;
        this.temMaisRegistros = true;
        this.carregandoMais = false;
    }

    /**
     * Carrega e exibe o conteúdo da aba
     */
    async _carregarEExibirConteudo(parametros) {
        this._limparConteudo();
        
        const loader = this.loading ? this._criarEExibirLoader() : null;

        // Executa callback beforeLoad
        if (this.beforeLoad && typeof this.beforeLoad === 'function') {
            try {
                await this.beforeLoad(parametros);
            } catch (error) {
                console.error('[Tab2] Erro no callback beforeLoad:', error);
            }
        }

        try {
            const resultado = await this.carregarConteudo(parametros);
            this._processarConteudos(resultado);
            
            // Executa callback onComplete
            if (this.onComplete && typeof this.onComplete === 'function') {
                try {
                    const conteudos = resultado.data || [];
                    await this.onComplete(conteudos, parametros);
                } catch (error) {
                    console.error('[Tab2] Erro no callback onComplete:', error);
                }
            }
        } catch (error) {
            console.error(`[Tab2] Erro ao carregar o conteúdo da aba:`, error);
            this._exibirMensagemErro();
            
            // Executa callback onError
            if (this.onError && typeof this.onError === 'function') {
                try {
                    await this.onError(error, parametros);
                } catch (callbackError) {
                    console.error('[Tab2] Erro no callback onError:', callbackError);
                }
            }
        } finally {
            if (loader) {
                this._removerLoader(loader);
            }
        }
    }

    /**
     * Limpa o conteúdo atual do container
     */
    _limparConteudo() {
        const container = document.querySelector(this.seletorConteudo);
        if (container) {
            container.innerHTML = '';
        }
    }

    /**
     * Cria e exibe o loader
     */
    _criarEExibirLoader() {
        const loader = this._criarLoader();
        if (loader) {
            this._exibirLoader(loader);
        }
        return loader;
    }

    /**
     * Cria e exibe o loader inline (para carregamento de mais registros)
     */
    _criarEExibirLoaderInline() {
        const loader = this._criarLoaderInline();
        if (loader) {
            this._exibirLoader(loader);
        }
        return loader;
    }

    /**
     * Processa e exibe os conteúdos carregados
     */
    _processarConteudos(resultado) {
        console.log(`[Tab2] Conteúdo da aba carregado com sucesso.`);

        const conteudoAba = document.querySelector(this.seletorConteudo);
        if (!conteudoAba) {
            console.error(`[Tab2] Conteúdo da aba com seletor "${this.seletorConteudo}" não encontrado.`);
            return;
        }

        const conteudos = resultado.data || [];
        
        console.log(`[Tab2] Informações de paginação inicial:`, {
            current_page: resultado.current_page,
            last_page: resultado.last_page,
            total: resultado.total,
            items_carregados: conteudos.length
        });

        if (!Array.isArray(conteudos) || conteudos.length === 0) {
            this.dados = [];
            this.temMaisRegistros = false;
            this._exibirMensagemVazia();
        } else {
            this.dados = conteudos;
            conteudos.forEach((conteudo, index) => {
                this.renderizarConteudo(conteudo, index);
            });

            // Verifica se há mais páginas baseado na resposta do servidor
            if (resultado.current_page >= resultado.last_page) {
                this.temMaisRegistros = false;
                console.log(`[Tab2] Não há mais páginas para carregar (página ${resultado.current_page} de ${resultado.last_page}).`);
            }
        }
    }

    /**
     * Exibe mensagem de conteúdo vazio
     */
    _exibirMensagemVazia() {
        const conteudoAba = document.querySelector(this.seletorConteudo);
        if (conteudoAba) {
            conteudoAba.innerHTML = '<p class="text-center text-gray-500 mt-8">Nenhum registro encontrado.</p>';
        }
    }

    /**
     * Exibe mensagem de erro
     */
    _exibirMensagemErro() {
        const conteudoAba = document.querySelector(this.seletorConteudo);
        if (conteudoAba) {
            conteudoAba.innerHTML = '<p class="text-center text-red-500 mt-8">Erro ao carregar os dados. Tente novamente.</p>';
        }
    }

    /**
     * Seleciona a primeira aba automaticamente
     */
    selecionarPrimeiraAba() {
        const primeiraAba = document.querySelector(`${this.seletor} button`);
        if (primeiraAba) {
            primeiraAba.classList.remove('active');
            primeiraAba.click();
        }
    }

    /**
     * Obtém os dados carregados
     */
    obterDados() {
        return this.dados;
    }

    /**
     * Carrega o conteúdo da aba via AJAX
     * 
     * @param {Object} parametros - Parâmetros da requisição
     * @returns {Promise<Object>} - Promessa que resolve com os dados e informações de paginação
     */
    async carregarConteudo(parametros = {}) {
        if (!this.url) {
            return Promise.reject(new Error('URL não definida para carregar o conteúdo da aba.'));
        }

        const containerResultados = document.querySelector(this.seletorConteudo);
        if (!containerResultados) {
            throw new Error(`[Tab2] Container de resultados "${this.seletorConteudo}" não encontrado.`);
        }

        try {
            const response = await $.ajax({
                url: this.url,
                method: this.metodo,
                dataType: 'json',
                data: parametros
            });

            console.log(`[Tab2] Resposta recebida da URL "${this.url}":`, response);
            
            // Suporta tanto response.data quanto response.notificacoes (retrocompatibilidade)
            const dados = response.data || response.notificacoes?.data || response.notificacoes || [];
            
            // Retorna dados com informações de paginação
            return {
                data: Array.isArray(dados) ? dados : [],
                current_page: response.current_page || parametros.pagina || 1,
                last_page: response.last_page || 1,
                total: response.total || (Array.isArray(dados) ? dados.length : 0)
            };
        } catch (error) {
            console.error(`[Tab2] Erro ao carregar conteúdo da aba:`, error);
            throw error;
        }
    }

    /**
     * Recarrega a aba atual
     */
    recarregar() {
        this._resetarPaginacao();
        const abaAtiva = document.querySelector(`${this.seletor} button.active`);
        if (abaAtiva) {
            abaAtiva.classList.remove('active');
            abaAtiva.click();
        } else {
            this.selecionarPrimeiraAba();
        }
    }

    /**
     * Cria um elemento de loader dinamicamente
     */
    _criarLoader() {
        try {
            const container = document.querySelector(this.seletorConteudo);
            if (!container) return null;

            const loader = document.createElement('div');
            loader.setAttribute('data-tab-loader', '');
            loader.className = 'text-center mt-8 py-4';
            loader.style.display = 'none';

            const icon = document.createElement('span');
            icon.className = 'material-icons-sharp text-5xl text-gray-400 animate-spin';
            icon.textContent = 'sync';

            loader.appendChild(icon);
            container.appendChild(loader);

            return loader;
        } catch (error) {
            console.error('[Tab2] Erro ao criar loader:', error);
            return null;
        }
    }

    /**
     * Cria um elemento de loader inline (para scroll infinito)
     */
    _criarLoaderInline() {
        try {
            const container = document.querySelector(this.seletorConteudo);
            if (!container) return null;

            const loader = document.createElement('div');
            loader.setAttribute('data-tab-loader-inline', '');
            loader.className = 'text-center py-4';
            loader.style.display = 'none';

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
        } catch (error) {
            console.error('[Tab2] Erro ao criar loader inline:', error);
            return null;
        }
    }

    /**
     * Obtém o elemento loader
     */
    _obterLoader() {
        const container = document.querySelector(this.seletorConteudo);
        if (!container) return null;
        return container.querySelector('[data-tab-loader]');
    }

    /**
     * Exibe o loader
     */
    _exibirLoader(loader) {
        if (!loader) return;
        loader.style.display = '';
    }

    /**
     * Oculta o loader
     */
    _ocultarLoader(loader) {
        if (!loader) return;
        loader.style.display = 'none';
    }

    /**
     * Remove o loader do DOM
     */
    _removerLoader(loader) {
        if (!loader) return;
        if (loader.parentNode) {
            loader.parentNode.removeChild(loader);
        }
    }

    /**
     * Renderiza um item de conteúdo no template
     * 
     * @param {Object} conteudo - Dados do item
     * @param {number} index - Índice do item na lista
     */
    renderizarConteudo(conteudo, index = 0) {
        const container = document.querySelector(this.seletorConteudo);
        if (!container) {
            console.error(`[Tab2] Container de resultados "${this.seletorConteudo}" não encontrado.`);
            return;
        }

        const template = document.querySelector(this.seletorTemplateRegistros);
        if (!template) {
            console.error(`[Tab2] Template de registros "${this.seletorTemplateRegistros}" não encontrado.`);
            return;
        }

        const clone = template.content.cloneNode(true);

        // Define o data-id no elemento raiz
        this._definirDataId(clone, conteudo);

        // Substitui placeholders no template
        this._substituirPlaceholders(clone, conteudo);

        // Aplica classes de status
        this._aplicarEstilosStatus(clone, conteudo);

        // Configura dropdown
        this._configurarDropdown(clone, conteudo);

        // Adiciona no container
        container.appendChild(clone);

        // Executa callback onItemRender após renderizar
        if (this.onItemRender && typeof this.onItemRender === 'function') {
            try {
                // Pega o elemento que acabou de ser adicionado
                const elementoRenderizado = container.lastElementChild;
                this.onItemRender(conteudo, elementoRenderizado, index);

            } catch (error) {
                console.error('[Tab2] Erro no callback onItemRender:', error);
            }
        }

        // Animação de entrada com delay escalonado
        const elemento = container.lastElementChild;
        if (elemento) {
            elemento.style.opacity = '0';
            elemento.style.transform = 'translateY(20px)';
            
            // Calcula delay baseado no índice dentro do conjunto atual
            const delayIndex = index % this.resultadosPorPagina;
            
            setTimeout(() => {
                elemento.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                elemento.style.opacity = '1';
                elemento.style.transform = 'translateY(0)';

                // Remover o transform após a animação
                setTimeout(() => {
                    elemento.style.transition = '';
                    elemento.style.transform = '';
                }, 400);
            }, delayIndex * 50); // Delay escalonado de 50ms por item
        }
    }

    /**
     * Define o atributo data-id no elemento raiz
     */
    _definirDataId(clone, conteudo) {
        const rootItem = clone.querySelector(`.${this.prefixo}-item`);
        if (rootItem && conteudo.id != null) {
            rootItem.setAttribute('data-id', conteudo.id);
        }
    }

    /**
     * Substitui placeholders {{chave}} no template pelos valores do conteúdo
     * Processa todos os {{placeholders}} independentemente de onde estejam
     */
    _substituirPlaceholders(clone, conteudo) {
        // Regex para encontrar todos os placeholders {{...}} no HTML
        const regex = /\{\{([a-zA-Z0-9_.]+)\}\}/g;
        
        // Processa todos os elementos
        const todosElementos = clone.querySelectorAll('*');
        
        todosElementos.forEach(elemento => {
            // 1. Processa todos os atributos do elemento
            Array.from(elemento.attributes).forEach(attr => {
                const valorOriginal = attr.value;
                
                // Decodifica chaves URL-encoded primeiro
                const valorDecodificado = this._decodificarChaves(valorOriginal);
                
                if (regex.test(valorDecodificado)) {
                    let novoValor = valorDecodificado.replace(regex, (match, caminho) => {
                        return this._obterValorPropriedade(conteudo, caminho);
                    });

                    // Normaliza URLs se for atributo href
                    if (attr.name === 'href') {
                        novoValor = this._normalizarUrl(novoValor);
                    }

                    elemento.setAttribute(attr.name, novoValor);
                }
            });

            // 2. Processa TextNodes filhos diretos (preserva estrutura HTML)
            Array.from(elemento.childNodes).forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const textoOriginal = node.textContent;
                    const textoDecodificado = this._decodificarChaves(textoOriginal);
                    
                    if (regex.test(textoDecodificado)) {
                        node.textContent = textoDecodificado.replace(regex, (match, caminho) => {
                            return this._obterValorPropriedade(conteudo, caminho);
                        });
                    }
                }
            });

            // 3. Processa innerHTML para elementos sem filhos (texto simples ou HTML inline)
            if (elemento.children.length === 0) {
                const htmlOriginal = elemento.innerHTML;
                const htmlDecodificado = this._decodificarChaves(htmlOriginal);
                
                if (regex.test(htmlDecodificado)) {
                    elemento.innerHTML = htmlDecodificado.replace(regex, (match, caminho) => {
                        return this._obterValorPropriedade(conteudo, caminho);
                    });
                }
            }
        });
    }

    /**
     * Obtém valor de uma propriedade do objeto, suportando notação de ponto
     * Exemplos:
     *   - 'nome' retorna conteudo.nome
     *   - 'tipo.nome' retorna conteudo.tipo.nome
     *   - 'usuario.endereco.cidade' retorna conteudo.usuario.endereco.cidade
     * 
     * @param {Object} obj - Objeto com os dados
     * @param {string} caminho - Caminho da propriedade (ex: 'tipo.nome')
     * @returns {string} Valor da propriedade ou '[não informado]'
     */
    _obterValorPropriedade(obj, caminho) {
        // Remove espaços e underscores, converte para snake_case se necessário
        const caminhoNormalizado = caminho.trim();
        const partes = caminhoNormalizado.split('.');
        let valor = obj;

        // Navega pelas propriedades aninhadas
        for (const parte of partes) {
            if (valor == null || typeof valor !== 'object') {
                return '[não informado]';
            }
            
            // Tenta acessar a propriedade diretamente
            if (parte in valor) {
                valor = valor[parte];
            } 
            // Tenta converter de camelCase para snake_case
            else {
                const parteSnakeCase = parte.replace(/([A-Z])/g, '_$1').toLowerCase();
                if (parteSnakeCase in valor) {
                    valor = valor[parteSnakeCase];
                } else {
                    return '[não informado]';
                }
            }
        }

        // Retorna o valor convertido para string, ou mensagem padrão se for null/undefined
        return valor == null ? '[não informado]' : String(valor);
    }

    /**
     * Decodifica %7B e %7D para { e }
     */
    _decodificarChaves(str) {
        return str.replace(/%7B/gi, '{').replace(/%7D/gi, '}');
    }

    /**
     * Normaliza string para URL (remove acentos, lowercase, hífens)
     */
    _normalizarUrl(str) {
        return str
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, '-')
            .toLowerCase();
    }

    /**
     * Aplica classes CSS baseadas no status do conteúdo
     */
    _aplicarEstilosStatus(clone, conteudo) {
        if (!conteudo.status) return;

        const statusElemento = clone.querySelector(`.${this.prefixo}-status`);
        if (!statusElemento) return;

        const iconeElemento = clone.querySelector(`.card-icon-container`);
        const status = conteudo.status.toLowerCase();

        const statusClasses = {
            'ativo': 'badge-green',
            'inativo': 'badge-yellow',
            'programado': 'badge-blue',
            'concluído': 'badge-purple',
            'finalizado': 'badge-purple',
            'arquivado': 'badge-gray'
        };

        const classe = statusClasses[status];
        if (classe) {
            statusElemento.classList.add(classe);
        }

        // Tratamento especial para status arquivado
        if (status === 'arquivado') {
            this._aplicarEstiloArquivado(iconeElemento, clone);
        }
    }

    /**
     * Aplica estilos para itens arquivados
     */
    _aplicarEstiloArquivado(iconeElemento, clone) {
        if (iconeElemento) {
            iconeElemento.classList.remove('bg-sky-600');
            iconeElemento.classList.add('bg-gray-500');
        }
        
        // Remove botões de ação (exceto visualização)
        clone.querySelectorAll(`[data-action]:not([data-action="visualizar"])`).forEach(botao => {
            botao.remove();
        });
    }

    /**
     * Configura funcionalidade do dropdown
     */
    _configurarDropdown(clone, conteudo) {
        const botaoDropdown = clone.querySelector(`.${this.prefixo}-dropdown-trigger`);
        if (!botaoDropdown) return;

        const menuDropdown = clone.querySelector(`.${this.prefixo}-dropdown-menu`);
        if (!menuDropdown) return;

        this._configurarToggleDropdown(botaoDropdown, menuDropdown);
        this._configurarLinkVisualizacao(menuDropdown, conteudo);
        this._configurarFechamentoDropdown(botaoDropdown, menuDropdown);
    }

    /**
     * Configura o toggle do dropdown
     */
    _configurarToggleDropdown(botaoDropdown, menuDropdown) {
        botaoDropdown.addEventListener('click', (event) => {
            event.preventDefault();
            menuDropdown.classList.toggle('hidden');
        });
    }

    /**
     * Configura o link de visualização no dropdown
     */
    _configurarLinkVisualizacao(menuDropdown, conteudo) {
        const linkVisualizar = menuDropdown.querySelector(`[data-action="visualizar"]`);
        if (!linkVisualizar) return;

        let href = linkVisualizar.href;

        if (conteudo.id != null) {
            href = href.replace('#id', conteudo.id);
        }

        if (conteudo.nome != null) {
            const nomeFormatado = this._normalizarUrl(conteudo.nome);
            href = href.replace('#nome', nomeFormatado);
        }

        linkVisualizar.href = href;
    }

    /**
     * Configura o fechamento do dropdown ao clicar fora
     */
    _configurarFechamentoDropdown(botaoDropdown, menuDropdown) {
        document.addEventListener('click', (event) => {
            if (!botaoDropdown.contains(event.target) && !menuDropdown.contains(event.target)) {
                menuDropdown.classList.add('hidden');
            }
        });
    }

    /**
     * Fecha todos os dropdowns abertos
     */
    fecharDropdowns() {
        const dropdowns = document.querySelectorAll(`${this.seletor} .${this.prefixo}-dropdown-menu`);
        dropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
}
