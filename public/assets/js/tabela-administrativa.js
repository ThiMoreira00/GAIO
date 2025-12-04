class TabelaAdministrativa {
    constructor(idTabela, colunasFiltaveis, colunaPrimaria = null, opcoes = {}, acoesAuxiliares = {}, statusClasses = {}) {
        this.tabela = document.getElementById(idTabela);
        if (!this.tabela) {
            console.error(`Erro: Tabela com ID "${idTabela}" não encontrada.`);
            return;
        }

        this.colunasFiltaveis = colunasFiltaveis;
        this.colunaPrimaria = colunaPrimaria;
        this.MAX_FILTER_OPTIONS = 10;

        this.acoes = {
            editar: acoesAuxiliares.editar || ((id) => console.log(`Ação: Editar item com ID: ${id}`)),
            visualizar: acoesAuxiliares.visualizar || ((id) => console.log(`Ação: Visualizar item com ID: ${id}`)),
            excluir: acoesAuxiliares.excluir || ((id) => console.log(`Ação: Excluir item com ID: ${id}`)),
        };

        this.statusClasses = {
            'Ativo': 'bg-green-100 text-green-800',
            'Inativo': 'bg-red-100 text-red-800',
            'Pendente': 'bg-yellow-100 text-yellow-800',
            ...statusClasses
        };

        this.cabecalhoTabela = this.tabela.querySelector('thead');
        this.corpoTabela = this.tabela.querySelector('tbody');

        if (!this.cabecalhoTabela || !this.corpoTabela) {
            console.error("Erro: `<thead>` ou `<tbody>` da tabela não encontrados dentro do elemento com ID:", idTabela);
            return;
        }

        this.mapeamentoThParaChaveDados = this.#construirMapeamentoThParaDados();
        this.dadosOriginais = this.#extrairDadosDoHTML();
        this.colunasOrdenaveisKeys = this.#identificarColunasOrdenaveisComDados(this.dadosOriginais);

        const seletoresPadrao = {
            seletorRegistros: 'seletor-registros',
            campoPesquisa: 'campo-pesquisa-geral',
            checkboxTodos: 'checkbox-selecionar-todos',
            infoInicioIntervalo: 'inicio-intervalo',
            infoFinalIntervalo: 'final-intervalo',
            infoTotalRegistros: 'total-registros',
            infoPaginaAtual: 'pagina-atual',
            infoTotalPaginas: 'total-paginas',
            btnAnterior: 'btn-pagina-anterior',
            btnProxima: 'btn-pagina-proxima',
            modalFiltros: 'modal-filtros',
            btnAbrirFiltros: 'btn-abrir-filtros',
            modalFiltrosCorpo: 'modal-filtros-corpo',
            btnAplicarFiltros: 'btn-aplicar-filtros',
            btnLimparFiltros: 'btn-limpar-filtros',
            contadorFiltros: 'contador-filtros',
        };

        this.seletores = { ...seletoresPadrao, ...opcoes };

        this.seletorRegistros = document.getElementById(this.seletores.seletorRegistros);
        this.campoPesquisa = document.getElementById(this.seletores.campoPesquisa);
        this.checkboxTodos = document.getElementById(this.seletores.checkboxTodos);

        this.infoInicioIntervalo = document.getElementById(this.seletores.infoInicioIntervalo);
        this.infoFinalIntervalo = document.getElementById(this.seletores.infoFinalIntervalo);
        this.infoTotalRegistros = document.getElementById(this.seletores.infoTotalRegistros);

        this.infoPaginaAtual = document.getElementById(this.seletores.infoPaginaAtual);
        this.infoTotalPaginas = document.getElementById(this.seletores.infoTotalPaginas);
        this.btnAnterior = document.getElementById(this.seletores.btnAnterior);
        this.btnProxima = document.getElementById(this.seletores.btnProxima);

        this.filtroDropdown = document.getElementById(this.seletores.modalFiltros);
        this.btnAbrirFiltros = document.getElementById(this.seletores.btnAbrirFiltros);
        this.modalFiltrosCorpo = document.getElementById(this.seletores.modalFiltrosCorpo);
        this.btnAplicarFiltros = document.getElementById(this.seletores.btnAplicarFiltros);
        this.btnLimparFiltros = document.getElementById(this.seletores.btnLimparFiltros);
        this.contadorFiltros = document.getElementById(this.seletores.contadorFiltros);

        if (!this.seletorRegistros || !this.campoPesquisa || !this.checkboxTodos || !this.filtroDropdown || !this.btnAbrirFiltros || !this.modalFiltrosCorpo || !this.btnAplicarFiltros || !this.btnLimparFiltros || !this.contadorFiltros || !this.infoInicioIntervalo || !this.infoFinalIntervalo || !this.infoTotalRegistros || !this.infoPaginaAtual || !this.infoTotalPaginas || !this.btnAnterior || !this.btnProxima) {
            console.warn("Aviso: Um ou mais elementos de controle da tabela não foram encontrados. Algumas funcionalidades podem não estar disponíveis.");
        }

        this.dadosAtuais = [...this.dadosOriginais];
        this.estado = {
            paginaAtual: 1,
            registrosPorPagina: parseInt(this.seletorRegistros ? this.seletorRegistros.value : 10, 10),
            termoBusca: '',
            filtrosColuna: {},
            colunaOrdenacao: null,
            direcaoOrdenacao: 'asc',
            idsSelecionados: new Set()
        };

        this._portalMenu = null;
        this._portalFiltro = null;
        this.originalFiltroDropdownParent = this.filtroDropdown ? this.filtroDropdown.parentElement : null;


        this.popularModalDeFiltros();
        this.#renderizarCabecalhoOrdenavel();
        this.iniciarOuvintesDeEventos();
        this.atualizarTabela();
    }

    #construirMapeamentoThParaDados() {
        const mapping = new Map();
        if (!this.cabecalhoTabela) return mapping;

        const headers = Array.from(this.cabecalhoTabela.querySelectorAll('th'));

        headers.forEach((th) => {
            if (th.querySelector('input[type="checkbox"]')) {
                mapping.set(th, 'checkbox-col');
                return;
            }
            const thText = th.textContent.trim();
            if (thText === 'Ações' || th.querySelector('[id^="menu-button-"]')) {
                mapping.set(th, 'actions-col');
                return;
            }

            let dataKey = null;
            if (th.dataset.key) {
                dataKey = th.dataset.key;
            } else if (th.dataset.sortKey) {
                dataKey = th.dataset.sortKey;
            } else {
                dataKey = thText.toLowerCase();
            }

            if (dataKey) {
                mapping.set(th, dataKey);
            }
        });
        return mapping;
    }

    #extrairDadosDoHTML() {
        const dadosExtraidos = [];
        const linhas = Array.from(this.corpoTabela.querySelectorAll('tr:not(.linha-detalhes)'));

        const chavesDeDadosOrdenadas = Array.from(this.mapeamentoThParaChaveDados.values())
            .filter(key => key !== 'checkbox-col' && key !== 'actions-col');

        linhas.forEach(linha => {
            const item = {};
            if (linha.dataset.id) {
                item.id = String(linha.dataset.id);
            }

            const celulas = Array.from(linha.querySelectorAll('td'));
            let dataKeyCounter = 0;

            celulas.forEach(celula => {
                const isCheckboxCol = celula.querySelector('input[type="checkbox"]');
                const isActionsCol = celula.querySelector('[id^="menu-button-"]');

                if (isCheckboxCol || isActionsCol) {
                    return;
                }

                const chaveDaCelula = chavesDeDadosOrdenadas[dataKeyCounter];

                if (chaveDaCelula) {
                    let valor;
                    const spanElement = celula.querySelector('.wrap-break-word');
                    if (spanElement) {
                        valor = spanElement.textContent.trim();
                    } else {
                        const statusBadge = celula.querySelector('.px-2.inline-flex');
                        if (statusBadge) {
                            valor = statusBadge.textContent.trim();
                        } else {
                            valor = celula.textContent.trim();
                        }
                    }

                    if (chaveDaCelula === 'id' && item.hasOwnProperty('id')) {
                    } else {
                        item[chaveDaCelula] = valor;
                    }
                }
                dataKeyCounter++;
            });
            dadosExtraidos.push(item);
        });
        return dadosExtraidos;
    }

    #identificarColunasOrdenaveisComDados(dados) {
        const trulySortableKeys = new Set();
        if (!dados || dados.length === 0) return trulySortableKeys;

        this.mapeamentoThParaChaveDados.forEach(dataKey => {
            if (dataKey === 'checkbox-col' || dataKey === 'actions-col' || dataKey === 'id') {
                return;
            }

            const valoresUnicos = new Set();
            let hasMultipleValues = false;
            for (const item of dados) {
                if (item.hasOwnProperty(dataKey)) {
                    valoresUnicos.add(item[dataKey]);
                }
                if (valoresUnicos.size > 1) {
                    hasMultipleValues = true;
                    break;
                }
            }
            if (hasMultipleValues) {
                trulySortableKeys.add(dataKey);
            }
        });
        return trulySortableKeys;
    }


    iniciarOuvintesDeEventos() {
        let debounceTimeout;

        if (this.campoPesquisa) {
            this.campoPesquisa.addEventListener('keyup', (e) => {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    this.estado.termoBusca = e.target.value.toLowerCase();
                    this.estado.paginaAtual = 1;
                    this.atualizarTabela();
                }, 300);
            });
        }

        if (this.seletorRegistros) {
            this.seletorRegistros.addEventListener('change', (e) => {
                this.estado.registrosPorPagina = parseInt(e.target.value, 10);
                this.estado.paginaAtual = 1;
                this.atualizarTabela();
            });
        }

        if (this.btnAnterior) {
            this.btnAnterior.addEventListener('click', () => this.irParaPagina(this.estado.paginaAtual - 1));
        }

        if (this.btnProxima) {
            this.btnProxima.addEventListener('click', () => this.irParaPagina(this.estado.paginaAtual + 1));
        }

        if (this.checkboxTodos) {
            this.checkboxTodos.addEventListener('change', this.selecionarTodos.bind(this));
        }

        document.addEventListener('click', this.lidarComCliquesGerais.bind(this));

        if (this.btnAbrirFiltros) {
            this.btnAbrirFiltros.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleFiltroDropdown();
            });
        }

        if (this.filtroDropdown) {
            this.filtroDropdown.addEventListener('click', (e) => {
                if (e.target !== this.btnAplicarFiltros && e.target !== this.btnLimparFiltros) {
                    e.stopPropagation();
                }
            });
        }

        if (this.btnAplicarFiltros) {
            this.btnAplicarFiltros.addEventListener('click', this.aplicarFiltrosDoModal.bind(this));
        }
        if (this.btnLimparFiltros) {
            this.btnLimparFiltros.addEventListener('click', this.limparFiltros.bind(this));
        }

        if (this.cabecalhoTabela) {
            this.cabecalhoTabela.addEventListener('click', (e) => {
                const th = e.target.closest('th');
                if (th && th.dataset.sortKeyInternal) {
                    const colunaKey = th.dataset.sortKeyInternal;
                    if (this.colunasOrdenaveisKeys.has(colunaKey)) {
                        this.ordenarTabela(colunaKey);
                    } else {
                        this.#atualizarIconesOrdenacaoNoCabecalho();
                    }
                }
            });
        }
    }

    lidarComCliquesGerais(event) {
        const alvo = event.target;
        const btnMenuAcao = alvo.closest('[id^="menu-button-"]');
        const clickedInsideFilterModal = this._portalFiltro && this._portalFiltro.contains(alvo);

        if (btnMenuAcao) {
            event.stopPropagation();
            const idLinha = btnMenuAcao.id.replace('menu-button-', '');
            const linhaAtual = btnMenuAcao.closest('tr');
            this.fecharTodosOsMenusDeAcao();
            this.fecharFiltroDropdown();
            this.#renderizarMenuAcoesNoPortal(btnMenuAcao, idLinha, linhaAtual);
        } else if (this._portalMenu && !this._portalMenu.contains(alvo)) {
            this.fecharTodosOsMenusDeAcao();
        }

        if (this.btnAbrirFiltros && !this.btnAbrirFiltros.contains(alvo) && !clickedInsideFilterModal) {
            this.fecharFiltroDropdown();
        }

        const gatilhoExpansao = alvo.closest('.gatilho-expansao');
        if (gatilhoExpansao && window.innerWidth < 640 && !alvo.closest('[id^="menu-button-"]')) {
            const linhaPrincipal = gatilhoExpansao.parentElement;
            const linhaDetalhes = linhaPrincipal.nextElementSibling;

            this.corpoTabela.querySelectorAll('.linha-detalhes:not(.hidden)').forEach(detalhe => {
                if (detalhe !== linhaDetalhes) {
                    detalhe.classList.add('hidden');
                    detalhe.previousElementSibling.classList.remove('bg-sky-50');
                    const icone = detalhe.previousElementSibling.querySelector('.icone-expansao');
                    if (icone) icone.classList.remove('rotate-45');
                }
            });

            linhaDetalhes.classList.toggle('hidden');
            linhaPrincipal.classList.toggle('bg-sky-50');
            gatilhoExpansao.querySelector('.icone-expansao')?.classList.toggle('rotate-45');
            event.stopPropagation();
        }
    }

    #renderizarMenuAcoesNoPortal(btnMenuAcao, idLinha, linhaAtual) {
        if (!this._portalMenu) {
            this._portalMenu = document.createElement('div');
            this._portalMenu.id = 'tabela-menu-portal';
            document.body.appendChild(this._portalMenu);
        }

        const menuHtml = `
            <div class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-gray-400/70 py-1">
                <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100" data-action="editar">Editar</a>
                <a href="#" class="block px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100" data-action="visualizar">Visualizar</a>
                <a href="#" class="block px-4 py-2 text-sm font-semibold text-red-700 hover:bg-gray-100" data-action="excluir">Excluir</a>
            </div>
        `;

        this._portalMenu.innerHTML = menuHtml;
        const menuElement = this._portalMenu.querySelector('div');

        const rect = btnMenuAcao.getBoundingClientRect();
        menuElement.style.position = 'absolute';
        menuElement.style.top = `${rect.bottom + window.scrollY}px`;
        menuElement.style.left = `${rect.left + window.scrollX + rect.width - menuElement.offsetWidth}px`;
        menuElement.style.zIndex = '9999';

        linhaAtual.classList.add('linha-ativa', '!bg-sky-100');

        Array.from(menuElement.querySelectorAll('a[data-action]')).forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const action = e.target.dataset.action;
                if (this.acoes[action]) {
                    this.acoes[action](idLinha);
                } else {
                    console.warn(`Nenhuma função auxiliar definida para a ação: "${action}"`);
                }
                this.fecharTodosOsMenusDeAcao();
            });
        });

        this._portalMenu.classList.remove('hidden');
    }

    popularModalDeFiltros() {
        if (!this.modalFiltrosCorpo) return;
        this.modalFiltrosCorpo.innerHTML = '';

        this.colunasFiltaveis.forEach(nomeColuna => {
            const dataKey = nomeColuna.toLowerCase();
            const temDadosParaColuna = this.dadosOriginais.some(item => item.hasOwnProperty(dataKey));
            if (!temDadosParaColuna) return;

            const valoresUnicos = [...new Set(this.dadosOriginais.map(item => item[dataKey]))]
                .filter(val => val !== null && val !== undefined)
                .sort();

            if (valoresUnicos.length < 2) {
                return;
            }

            let secaoFiltroHTML = `<div data-coluna-filtro="${dataKey}" class="mb-3 last:mb-0">
                <h4 class="text-sm uppercase font-semibold text-gray-800 mb-2">${nomeColuna}</h4>
                <div class="space-y-1">`;

            const valoresParaExibir = valoresUnicos.slice(0, this.MAX_FILTER_OPTIONS);
            valoresParaExibir.forEach(valor => {
                const inputValue = String(valor);
                secaoFiltroHTML += `<label class="flex items-center cursor-pointer mx-2">
                    <input type="checkbox" value="${inputValue}" class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <span class="ml-2 text-gray-700 py-1 text-sm">${valor}</span>
                </label>`;
            });

            if (valoresUnicos.length > this.MAX_FILTER_OPTIONS) {
                secaoFiltroHTML += `<p class="text-gray-500 text-xs mx-2 mt-1">Mais ${valoresUnicos.length - this.MAX_FILTER_OPTIONS} opções ocultas.</p>`;
            }

            secaoFiltroHTML += `</div></div>`;
            this.modalFiltrosCorpo.innerHTML += secaoFiltroHTML;
        });
    }

    toggleFiltroDropdown() {
        if (!this.filtroDropdown) return;
        this.fecharTodosOsMenusDeAcao();

        if (this.filtroDropdown.classList.contains('hidden')) {
            this.#renderizarModalFiltroNoPortal();
            Object.entries(this.estado.filtrosColuna).forEach(([nomeColuna, valores]) => {
                const secao = this._portalFiltro.querySelector(`[data-coluna-filtro="${nomeColuna}"]`);
                if (secao) {
                    secao.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                    valores.forEach(valor => {
                        const checkbox = secao.querySelector(`input[value="${valor}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            });
        } else {
            this.fecharFiltroDropdown();
        }
    }

    #renderizarModalFiltroNoPortal() {
        if (!this._portalFiltro) {
            this._portalFiltro = document.createElement('div');
            this._portalFiltro.id = 'tabela-filtro-portal';
            document.body.appendChild(this._portalFiltro);
            this._portalFiltro.appendChild(this.filtroDropdown);
        }

        const rect = this.btnAbrirFiltros.getBoundingClientRect();
        this.filtroDropdown.style.position = 'absolute';
        this.filtroDropdown.style.top = `${rect.bottom + window.scrollY + 8}px`;

        this.filtroDropdown.style.left = `${rect.left + window.scrollX}px`;

        const viewportWidth = window.innerWidth;
        const modalRightEdge = rect.left + this.filtroDropdown.offsetWidth;
        if (modalRightEdge > viewportWidth - 10) {
            this.filtroDropdown.style.left = `${viewportWidth - this.filtroDropdown.offsetWidth - 10}px`;
        }
        const modalLeftEdge = rect.left;
        if (modalLeftEdge < 10) {
            this.filtroDropdown.style.left = `10px`;
        }

        this.filtroDropdown.style.zIndex = '1000';

        this.filtroDropdown.classList.remove('hidden');
    }

    fecharFiltroDropdown() {
        if (this.filtroDropdown && !this.filtroDropdown.classList.contains('hidden')) {
            this.filtroDropdown.classList.add('hidden');
        }
    }

    aplicarFiltrosDoModal() {
        const novosFiltros = {};
        if (this.filtroDropdown) {
            const secoesDeFiltro = this.filtroDropdown.querySelectorAll('[data-coluna-filtro]');
            secoesDeFiltro.forEach(secao => {
                const nomeColuna = secao.dataset.colunaFiltro;
                const checkboxesMarcados = secao.querySelectorAll('input:checked');
                if (checkboxesMarcados.length > 0) {
                    novosFiltros[nomeColuna] = Array.from(checkboxesMarcados).map(cb => cb.value);
                }
            });
        }

        this.estado.filtrosColuna = novosFiltros;
        this.estado.paginaAtual = 1;
        this.atualizarTabela();
        this.fecharFiltroDropdown();
    }

    atualizarIndicadorDeFiltro() {
        if (!this.contadorFiltros) return;
        const totalFiltros = Object.values(this.estado.filtrosColuna).reduce((acc, arr) => acc + arr.length, 0);
        if (totalFiltros > 0) {
            this.contadorFiltros.textContent = totalFiltros;
            this.contadorFiltros.classList.remove('hidden');
        } else {
            this.contadorFiltros.classList.add('hidden');
        }
    }

    limparFiltros() {
        this.estado.filtrosColuna = {};
        this.estado.paginaAtual = 1;

        if (this.contadorFiltros) {
            this.contadorFiltros.textContent = '';
            this.contadorFiltros.classList.add('hidden');
        }

        if (this.filtroDropdown) {
            this.filtroDropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        }

        this.atualizarTabela();
        this.fecharFiltroDropdown();
    }

    ordenarTabela(colunaKey) {
        if (this.estado.colunaOrdenacao === colunaKey) {
            this.estado.direcaoOrdenacao = this.estado.direcaoOrdenacao === 'asc' ? 'desc' : 'asc';
        } else {
            this.estado.colunaOrdenacao = colunaKey;
            this.estado.direcaoOrdenacao = 'asc';
        }

        this.estado.paginaAtual = 1;
        this.atualizarTabela();
    }

    aplicarOrdenacao(dados) {
        const { colunaOrdenacao, direcaoOrdenacao } = this.estado;

        if (!colunaOrdenacao || !this.colunasOrdenaveisKeys.has(colunaOrdenacao)) {
            return dados;
        }

        const isAsc = direcaoOrdenacao === 'asc';

        return dados.sort((a, b) => {
            const valA = a[colunaOrdenacao];
            const valB = b[colunaOrdenacao];

            if (valA === undefined || valA === null) return isAsc ? 1 : -1;
            if (valB === undefined || valB === null) return isAsc ? -1 : 1;

            const numA = parseFloat(valA);
            const numB = parseFloat(valB);

            if (!isNaN(numA) && !isNaN(numB) && isFinite(numA) && isFinite(numB)) {
                return isAsc ? numA - numB : numB - numA;
            }

            const strA = String(valA).toLowerCase();
            const strB = String(valB).toLowerCase();

            const compareResult = strA.localeCompare(strB, undefined, { sensitivity: 'base' });

            return isAsc ? compareResult : -compareResult;
        });
    }

    #renderizarCabecalhoOrdenavel() {
        if (!this.cabecalhoTabela) return;

        const row = this.cabecalhoTabela.querySelector('tr');
        if (!row) return;

        const headers = Array.from(row.querySelectorAll('th'));

        headers.forEach((th) => {
            const originalText = th.textContent.trim();
            const dataKey = this.mapeamentoThParaChaveDados.get(th);

            if (dataKey && this.colunasOrdenaveisKeys.has(dataKey)) {
                th.setAttribute('data-sort-key-internal', dataKey);
                th.classList.add('cursor-pointer', 'select-none', 'relative');

                th.innerHTML = `
                    <span class="flex items-center justify-between">
                        ${originalText}
                        <span class="ml-1 flex-shrink-0 text-gray-400 order-icon-container">
                            </span>
                    </span>
                `;
            } else {
                th.removeAttribute('data-sort-key-internal');
                th.classList.remove('cursor-pointer', 'select-none', 'relative');
                th.innerHTML = originalText;
            }
        });
        this.#atualizarIconesOrdenacaoNoCabecalho();
    }

    #atualizarIconesOrdenacaoNoCabecalho() {
        if (!this.cabecalhoTabela) return;

        const headers = Array.from(this.cabecalhoTabela.querySelectorAll('th[data-sort-key-internal]'));

        headers.forEach((th) => {
            const sortKey = th.dataset.sortKeyInternal;
            const iconContainer = th.querySelector('.order-icon-container');

            if (iconContainer) {
                const valoresUnicosPosFiltro = new Set();
                for (const item of this.dadosAtuais) {
                    if (item.hasOwnProperty(sortKey)) {
                        valoresUnicosPosFiltro.add(item[sortKey]);
                    }
                    if (valoresUnicosPosFiltro.size > 1) break;
                }

                if (valoresUnicosPosFiltro.size <= 1) {
                    iconContainer.innerHTML = '';
                    th.classList.remove('cursor-pointer');
                    return;
                }

                th.classList.add('cursor-pointer');

                iconContainer.classList.remove('text-sky-600');
                iconContainer.classList.add('text-gray-400');

                if (this.estado.colunaOrdenacao === sortKey) {
                    iconContainer.classList.remove('text-gray-400');
                    iconContainer.classList.add('text-sky-600');

                    iconContainer.innerHTML = this.estado.direcaoOrdenacao === 'asc'
                        ? `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>`
                        : `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>`;
                } else {
                    iconContainer.innerHTML = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 4l4 4H6l4-4z"></path><path d="M10 16l-4-4h8l-4 4z"></path></svg>`;
                }
            }
        });
    }

    aplicarFiltrosEBusca() {
        let dadosFiltrados = [...this.dadosOriginais];

        const filtrosAtivos = Object.entries(this.estado.filtrosColuna);
        if (filtrosAtivos.length > 0) {
            dadosFiltrados = dadosFiltrados.filter(linha => {
                return filtrosAtivos.every(([nomeColuna, valores]) => {
                    const valorLinha = linha[nomeColuna];
                    return valorLinha !== undefined && valorLinha !== null && valores.includes(String(valorLinha));
                });
            });
        }

        if (this.estado.termoBusca) {
            dadosFiltrados = dadosFiltrados.filter(linha => {
                return Object.values(linha).some(valor =>
                    String(valor).toLowerCase().includes(this.estado.termoBusca)
                );
            });
        }

        this.dadosAtuais = this.aplicarOrdenacao(dadosFiltrados);
    }

    atualizarTabela() {
        this.aplicarFiltrosEBusca();
        this.corpoTabela.innerHTML = '';
        const { paginaAtual, registrosPorPagina } = this.estado;
        const inicio = (paginaAtual - 1) * registrosPorPagina;
        const fim = inicio + registrosPorPagina;
        const dadosDaPagina = this.dadosAtuais.slice(inicio, fim);

        const numeroDeColunas = this.cabecalhoTabela?.querySelector('tr')?.cells.length || 5;

        if (dadosDaPagina.length === 0) {
            this.corpoTabela.innerHTML = `<tr><td colspan="${numeroDeColunas}" class="text-center py-8 text-gray-500">Nenhum registro encontrado.</td></tr>`;
        }

        const headers = Array.from(this.cabecalhoTabela.querySelectorAll('th'));

        dadosDaPagina.forEach((item, index) => {
            const idLinha = item.id || `item-${inicio + index + 1}`;

            let celulasDadosHTML = '';

            headers.forEach(th => {
                const mappedKey = this.mapeamentoThParaChaveDados.get(th);

                if (mappedKey === 'checkbox-col') {
                    const isChecked = this.estado.idsSelecionados.has(String(idLinha));
                    celulasDadosHTML += `<td class="py-3 ps-4"><input type="checkbox" class="border-gray-200 rounded-sm item-checkbox" data-id="${idLinha}" ${isChecked ? 'checked' : ''}></td>`;
                } else if (mappedKey === 'actions-col') {
                    celulasDadosHTML += `
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                             <div class="relative inline-block text-left">
                                <button type="button" class="inline-flex items-center p-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 rounded-md" id="menu-button-${idLinha}" aria-label="Ações para o item"><svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg></button>
                                </div>
                        </td>
                    `;
                } else if (mappedKey) {
                    const valor = item[mappedKey] !== undefined && item[mappedKey] !== null ? item[mappedKey] : '';
                    let contentHTML = `<span class="wrap-break-word">${valor}</span>`;
                    let tdClasses = 'px-6 py-4 whitespace-nowrap text-sm text-gray-800';

                    if (this.colunaPrimaria && mappedKey === this.colunaPrimaria) {
                        tdClasses += ' font-medium';
                    }

                    if (mappedKey === 'nome' && item.hasOwnProperty('nome')) {
                        tdClasses += ' gatilho-expansao cursor-pointer sm:cursor-default';
                        contentHTML = `
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-gray-500 transform transition-transform duration-200 sm:hidden icone-expansao" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                <span class="wrap-break-word">${valor}</span>
                            </div>
                        `;
                    }

                    if (mappedKey === 'status' && item.hasOwnProperty('status')) {
                        const statusVal = item.status || '';
                        const statusClass = this.statusClasses[statusVal] || 'bg-gray-100 text-gray-800';
                        contentHTML = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusVal}</span>`;
                    }

                    const originalTh = Array.from(this.cabecalhoTabela.querySelectorAll('th')).find(thElem => this.mapeamentoThParaChaveDados.get(thElem) === mappedKey);
                    if (originalTh && originalTh.classList.contains('sm:table-cell') && mappedKey !== 'nome') {
                        tdClasses += ' hidden sm:table-cell';
                    } else if (originalTh && originalTh.classList.contains('md:table-cell') && mappedKey !== 'nome') {
                        tdClasses += ' hidden md:table-cell';
                    }

                    celulasDadosHTML += `<td class="${tdClasses}">${contentHTML}</td>`;
                }
            });

            const linhaPrincipalHTML = `<tr data-id="${idLinha}" class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150 ${this.estado.idsSelecionados.has(String(idLinha)) ? 'bg-gray-100' : ''}">${celulasDadosHTML}</tr>`;

            let linhaDetalhesContentHTML = '';
            headers.forEach(th => {
                const mappedKey = this.mapeamentoThParaChaveDados.get(th);
                const valor = item[mappedKey] !== undefined && item[mappedKey] !== null ? item[mappedKey] : '';
                const headerTextForDetails = th.textContent.trim();

                if (mappedKey !== 'nome' && mappedKey !== 'checkbox-col' && mappedKey !== 'actions-col') {
                    if (mappedKey === 'status' && item.hasOwnProperty('status')) {
                        const statusVal = item.status || '';
                        const statusClass = this.statusClasses[statusVal] || 'bg-gray-100 text-gray-800';
                        linhaDetalhesContentHTML += `
                            <div>
                                <strong class="block text-gray-500">${headerTextForDetails}:</strong>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${statusVal}</span>
                            </div>`;
                    } else {
                        linhaDetalhesContentHTML += `
                            <div>
                                <strong class="block text-gray-500">${headerTextForDetails}:</strong>
                                <span class="text-gray-800">${valor}</span>
                            </div>`;
                    }
                }
            });

            const linhaDetalhesHTML = `
                <tr class="linha-detalhes hidden sm:hidden bg-gray-50 border-b border-gray-200">
                    <td colspan="${numeroDeColunas}" class="p-4">
                        <div class="grid gap-4 text-sm">
                            ${linhaDetalhesContentHTML}
                        </div>
                    </td>
                </tr>
            `;

            this.corpoTabela.insertAdjacentHTML('beforeend', linhaPrincipalHTML + linhaDetalhesHTML);
        });

        this.#atualizarInformacoesRodape();
        this.atualizarIndicadorDeFiltro();
        this.#atualizarIconesOrdenacaoNoCabecalho();

        this.corpoTabela.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const id = e.target.dataset.id;
                if (e.target.checked) {
                    this.estado.idsSelecionados.add(id);
                } else {
                    this.estado.idsSelecionados.delete(id);
                }
                this.#atualizarCheckboxTodos();
            });
        });
        this.#atualizarCheckboxTodos();
    }

    #atualizarInformacoesRodape() {
        const { paginaAtual, registrosPorPagina } = this.estado;
        const totalRegistrosFiltrados = this.dadosAtuais.length;
        const totalPaginas = Math.ceil(totalRegistrosFiltrados / registrosPorPagina) || 1;

        const inicioIntervalo = totalRegistrosFiltrados > 0 ? ((paginaAtual - 1) * registrosPorPagina) + 1 : 0;
        const fimIntervalo = Math.min(inicioIntervalo + registrosPorPagina - 1, totalRegistrosFiltrados);

        if (this.infoInicioIntervalo) this.infoInicioIntervalo.textContent = inicioIntervalo;
        if (this.infoFinalIntervalo) this.infoFinalIntervalo.textContent = fimIntervalo;
        if (this.infoTotalRegistros) this.infoTotalRegistros.textContent = totalRegistrosFiltrados;

        if (this.infoPaginaAtual) this.infoPaginaAtual.textContent = paginaAtual;
        if (this.infoTotalPaginas) this.infoTotalPaginas.textContent = totalPaginas;

        if (this.btnAnterior) this.btnAnterior.disabled = paginaAtual === 1;
        if (this.btnProxima) this.btnProxima.disabled = paginaAtual === totalPaginas;
    }

    irParaPagina(numeroPagina) {
        const totalPaginas = Math.ceil(this.dadosAtuais.length / this.estado.registrosPorPagina) || 1;
        if (numeroPagina >= 1 && numeroPagina <= totalPaginas) {
            this.estado.paginaAtual = numeroPagina;
            this.atualizarTabela();
        }
    }

    selecionarTodos() {
        if (!this.checkboxTodos) return;
        const checkboxesNaPagina = this.corpoTabela.querySelectorAll('.item-checkbox');
        checkboxesNaPagina.forEach(cb => {
            cb.checked = this.checkboxTodos.checked;
            const id = cb.dataset.id;
            if (cb.checked) {
                this.estado.idsSelecionados.add(id);
            } else {
                this.estado.idsSelecionados.delete(id);
            }
        });
    }

    #atualizarCheckboxTodos() {
        if (!this.checkboxTodos) return;
        const checkboxesNaPagina = this.corpoTabela.querySelectorAll('.item-checkbox');
        const todosMarcados = Array.from(checkboxesNaPagina).every(cb => cb.checked);
        const nenhumMarcado = Array.from(checkboxesNaPagina).every(cb => !cb.checked);

        this.checkboxTodos.checked = todosMarcados;
        this.checkboxTodos.indeterminate = !todosMarcados && !nenhumMarcado;
    }


    fecharTodosOsMenusDeAcao() {
        if (this._portalMenu) {
            this._portalMenu.innerHTML = '';
        }
        const linhasAtivas = this.tabela.querySelectorAll('tbody tr.linha-ativa');
        linhasAtivas.forEach(linha => {
            linha.classList.remove('linha-ativa', '!bg-sky-100');
        });
    }

    getIdsSelecionados() {
        return Array.from(this.estado.idsSelecionados);
    }

    limparSelecao() {
        this.estado.idsSelecionados.clear();
        this.atualizarTabela();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const colunasParaFiltrar = ['Tipo', 'Status', 'Criado Em'];
    const colunaParaDestaque = 'nome';

    const minhasAcoesExternas = {
        editar: (id) => {
            alert(`Você clicou em Editar o item com ID: ${id}`);
        },
        visualizar: (id) => {
            alert(`Você clicou em Visualizar o item com ID: ${id}`);
        },
        excluir: (id) => {
            if (confirm(`Tem certeza que deseja excluir o item com ID: ${id}?`)) {
                alert(`Excluindo item com ID: ${id}`);
            }
        },
    };

    const classesDeStatusPersonalizadas = {
        'Ativo': 'bg-green-100 text-green-800',
        'Inativo': 'bg-red-100 text-red-800',
        'Pendente': 'bg-yellow-100 text-yellow-800',
        'Concluído': 'bg-sky-100 text-sky-800',
        'Rejeitado': 'bg-purple-100 text-purple-800'
    };

    const tabelaUsuarios = new TabelaAdministrativa(
        'tabela-usuarios',
        colunasParaFiltrar,
        colunaParaDestaque,
        {},
        minhasAcoesExternas,
        classesDeStatusPersonalizadas
    );

    document.getElementById('algum-botao-acao-em-massa')?.addEventListener('click', () => {
        const ids = tabelaUsuarios.getIdsSelecionados();
        if (ids.length > 0) {
            alert(`IDs selecionados para ação em massa: ${ids.join(', ')}`);
            tabelaUsuarios.limparSelecao();
        } else {
            alert('Nenhum item selecionado!');
        }
    });
});