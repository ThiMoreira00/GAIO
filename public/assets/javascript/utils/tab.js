/**
 * Classe Tab2
 * 
 * Versão melhorada do gerenciador de abas com suporte a eventos e código mais modular
 */

class Tab {

    // Atributos
    prefixo = 'tab';
    seletor = null;
    seletorConteudo = null;
    seletorTemplateRegistros = null;
    url = null;
    metodo = 'GET';
    resultadosPorPagina = 10;
    loading = true;
    dados = [];
    timeoutBusca = null;

    // Eventos
    beforeLoad = null;
    onComplete = null;
    onError = null;
    onItemRender = null;

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
     */
    constructor(seletor, opcoes = {}) {
        this.prefixo = opcoes.prefixo || 'tab';
        this.seletor = seletor;
        this.seletorConteudo = opcoes.seletorConteudo || `${seletor}-conteudo`;
        this.seletorLoader = (opcoes.loading !== false) ? (opcoes.seletorLoader || `${seletor}-loader`) : null;
        this.seletorTemplateRegistros = opcoes.seletorTemplateRegistros || `${seletor}-template-registros`;
        this.url = opcoes.url || null;
        this.metodo = opcoes.metodo || 'GET';
        this.resultadosPorPagina = opcoes.resultadosPorPagina || 10;
        this.loading = opcoes.loading !== false;

        // Eventos
        this.beforeLoad = opcoes.beforeLoad || null;
        this.onComplete = opcoes.onComplete || null;
        this.onError = opcoes.onError || null;
        this.onItemRender = opcoes.onItemRender || null;

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
        const parametros = {};
        
        // Obtém status do botão ativo
        if (btn?.dataset.tabStatus) {
            parametros.status = btn.dataset.tabStatus;
        }
        
        // Obtém turno do botão ativo ou input hidden
        if (btn?.dataset.tabTurno !== undefined) {
            parametros.turno = btn.dataset.tabTurno;
        }
        
        // Atualiza input hidden se existir
        const turnoInput = document.querySelector('[data-tab-turno-input]');
        if (turnoInput && parametros.turno !== undefined) {
            turnoInput.value = parametros.turno;
        }
        
        // Obtém valor do campo de busca pelo atributo data-tab-search
        const inputBusca = document.querySelector(`${this.seletor} [data-tab-search]`);
        if (inputBusca?.value) {
            parametros.busca = inputBusca.value;
        }
        
        // Coleta outros parâmetros do formulário se existir
        const form = document.querySelector(`${this.seletor} [data-tab-form]`);
        if (form) {
            const formData = new FormData(form);
            for (const [key, value] of formData.entries()) {
                if (value && !parametros[key]) {
                    parametros[key] = value;
                }
            }
        }
        
        return parametros;
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
            const conteudos = await this.carregarConteudo(parametros);
            this._processarConteudos(conteudos);
            
            // Executa callback onComplete
            if (this.onComplete && typeof this.onComplete === 'function') {
                try {
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
     * Processa e exibe os conteúdos carregados
     */
    _processarConteudos(conteudos) {
        console.log(`[Tab2] Conteúdo da aba carregado com sucesso.`);

        const conteudoAba = document.querySelector(this.seletorConteudo);
        if (!conteudoAba) {
            console.error(`[Tab2] Conteúdo da aba com seletor "${this.seletorConteudo}" não encontrado.`);
            return;
        }

        if (!Array.isArray(conteudos) || conteudos.length === 0) {
            this.dados = [];
            this._exibirMensagemVazia();
        } else {
            this.dados = conteudos;
            conteudos.forEach((conteudo, index) => {
                this.renderizarConteudo(conteudo, index);
            });
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
     * @returns {Promise<Array>} - Promessa que resolve com os dados
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
            return response.data || [];
        } catch (error) {
            console.error(`[Tab2] Erro ao carregar conteúdo da aba:`, error);
            throw error;
        }
    }

    /**
     * Recarrega a aba atual
     */
    recarregar() {
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

        // Exemplo: aplicar animação de entrada
        const elemento = container.lastElementChild;
        elemento.style.opacity = '0';
        setTimeout(() => {
            elemento.style.transition = 'opacity 0.3s';
            elemento.style.opacity = '1';
        }, index * 50); // Delay escalonado
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
     */
    _substituirPlaceholders(clone, conteudo) {
        // Primeiro, processa placeholders simples (chaves diretas do objeto)
        for (const chave in conteudo) {
            const elementos = clone.querySelectorAll(`.${this.prefixo}-${chave.replace(/_/g, '-')}`);
            if (!elementos.length) continue;

            const valor = conteudo[chave] == null ? "[não informado]" : String(conteudo[chave]);

            elementos.forEach(elemento => {
                this._substituirConteudoElemento(elemento, chave, valor);
                this._substituirAtributosElemento(elemento, chave, valor);
            });
        }

        // Depois, processa placeholders aninhados (ex: tipo.nome)
        this._substituirPlaceholdersAninhados(clone, conteudo);
    }

    /**
     * Substitui placeholders aninhados como {{tipo.nome}} no template
     */
    _substituirPlaceholdersAninhados(clone, conteudo) {
        // Regex para encontrar todos os placeholders {{...}} no HTML
        const regex = /\{\{([a-zA-Z0-9_.]+)\}\}/g;
        
        // Processa elementos com texto
        this._processarElementosTexto(clone, conteudo, regex);
        
        // Processa atributos dos elementos
        this._processarAtributosElementos(clone, conteudo, regex);
    }

    /**
     * Processa elementos de texto substituindo placeholders aninhados
     */
    _processarElementosTexto(clone, conteudo, regex) {
        const todosElementos = clone.querySelectorAll('*');
        
        todosElementos.forEach(elemento => {
            // Processa TextNodes filhos diretos
            Array.from(elemento.childNodes).forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    const textoOriginal = node.textContent;
                    if (regex.test(textoOriginal)) {
                        node.textContent = textoOriginal.replace(regex, (match, caminho) => {
                            return this._obterValorAninhado(conteudo, caminho);
                        });
                    }
                }
            });

            // Processa innerHTML se não tiver filhos elementos (para preservar HTML simples)
            if (elemento.children.length === 0 && elemento.innerHTML) {
                const htmlOriginal = elemento.innerHTML;
                if (regex.test(htmlOriginal)) {
                    elemento.innerHTML = htmlOriginal.replace(regex, (match, caminho) => {
                        return this._obterValorAninhado(conteudo, caminho);
                    });
                }
            }
        });
    }

    /**
     * Processa atributos dos elementos substituindo placeholders aninhados
     */
    _processarAtributosElementos(clone, conteudo, regex) {
        const todosElementos = clone.querySelectorAll('*');
        
        todosElementos.forEach(elemento => {
            Array.from(elemento.attributes).forEach(attr => {
                if (attr.name === 'class') return;
                
                const valorOriginal = attr.value;
                if (regex.test(valorOriginal)) {
                    let novoValor = valorOriginal.replace(regex, (match, caminho) => {
                        return this._obterValorAninhado(conteudo, caminho);
                    });

                    if (attr.name === 'href') {
                        novoValor = this._normalizarUrl(novoValor);
                    }

                    elemento.setAttribute(attr.name, novoValor);
                }
            });
        });
    }

    /**
     * Obtém valor de propriedade aninhada usando notação de ponto
     * Exemplo: _obterValorAninhado({tipo: {nome: 'Teste'}}, 'tipo.nome') retorna 'Teste'
     */
    _obterValorAninhado(obj, caminho) {
        const partes = caminho.split('.');
        let valor = obj;

        for (const parte of partes) {
            if (valor == null) {
                return '[não informado]';
            }
            valor = valor[parte];
        }

        return valor == null ? '[não informado]' : String(valor);
    }

    /**
     * Substitui placeholders no conteúdo do elemento (texto ou HTML)
     */
    _substituirConteudoElemento(elemento, chave, valor) {
        const temFilhosElementos = elemento.children.length > 0;
        const placeholder = `{{${chave}}}`;

        if (temFilhosElementos) {
            let html = elemento.innerHTML || '';
            html = this._decodificarChaves(html);
            elemento.innerHTML = html.replaceAll(placeholder, valor);
        } else {
            let txt = elemento.textContent || '';
            txt = this._decodificarChaves(txt);
            elemento.textContent = txt.replaceAll(placeholder, valor);
        }
    }

    /**
     * Substitui placeholders nos atributos do elemento
     */
    _substituirAtributosElemento(elemento, chave, valor) {
        const placeholder = `{{${chave}}}`;

        Array.from(elemento.attributes).forEach(attr => {
            if (attr.name === 'class' || !attr.value) return;

            let valorAttr = this._decodificarChaves(attr.value);
            valorAttr = valorAttr.replaceAll(placeholder, valor);

            if (attr.name === 'href') {
                valorAttr = this._normalizarUrl(valorAttr);
            }

            elemento.setAttribute(attr.name, valorAttr);
        });
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
