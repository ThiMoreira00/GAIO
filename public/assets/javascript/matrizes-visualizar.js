/**
 * Camada de interação da página de visualização da matriz curricular.
 * Responsável por abas, filtros, estatísticas, modais e organização visual.
 */
(function () {
    const ESTADO_GLOBAL_CHAVE = '__estadoVisualizarMatriz';
    const CLASSES_DROP_ATIVO = ['ring-2', 'ring-sky-300', 'bg-white'];


    function corrigirEncodingSeNecessario(texto) {
        if (typeof texto !== 'string') {
            return texto;
        }

        if (!/[ÃÂÊÔÕ¼½]/.test(texto)) {
            return texto;
        }

        const tentarDecodificar = () => {
            if (typeof TextDecoder !== 'undefined') {
                const decoder = new TextDecoder('utf-8');
                const bytes = new Uint8Array(texto.length);
                for (let i = 0; i < texto.length; i += 1) {
                    bytes[i] = texto.charCodeAt(i);
                }
                return decoder.decode(bytes);
            }
            return decodeURIComponent(escape(texto));
        };

        try {
            return tentarDecodificar();
        } catch (erro) {
            try {
                return decodeURIComponent(escape(texto));
            } catch (erroSecundario) {
                return texto;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('container-matriz');
        if (!container) {
            return;
        }

        const estado = montarEstadoInicial(container);
        if (!estado) {
            return;
        }

        registrarEstadoGlobal(estado);
        iniciarTela(estado);
    });

    function montarEstadoInicial(container) {
        let configuracao = {};
        try {
            configuracao = JSON.parse(container.dataset.config || '{}');
        } catch (erro) {
            console.error('Configuração da matriz inválida:', erro);
            return null;
        }

        const permissoes = configuracao.permissoes || {};
        const matrizStatus = configuracao.matrizStatus || '';
        const editavel = !!(permissoes.editar && matrizStatus !== 'Arquivado');

        return {
            config: {
                matrizId: configuracao.matrizId,
                matrizStatus,
                permissoes,
                rotas: configuracao.rotas || {}
            },
            elementos: {
                container,
                abas: document.querySelectorAll('.tab-button'),
                paineis: document.querySelectorAll('.tab-panel'),
                tabelaComponentes: document.getElementById('tabela-componentes'),
                filtroPeriodo: document.getElementById('filtro-periodo'),
                filtroTipo: document.getElementById('filtro-tipo'),
                quadroOrganizacao: document.getElementById('matrix-board'),
                areaNaoVinculados: document.getElementById('unlinked-area'),
                listaNaoVinculados: document.getElementById('unlinked-list'),
                botaoModoEdicao: document.getElementById('btn-matriz-edit'),
                textoBotaoModoEdicao: document.getElementById('btn-matriz-edit-text'),
                botaoNovaDisciplina: document.getElementById('btn-matriz-add'),
                botaoGradeNovo: document.getElementById('btn-adicionar-componente')
            },
            modais: {
                adicionarComponente: null,
                detalhesComponente: null
            },
            filtros: {
                periodo: '',
                tipo: ''
            },
            componentes: [],
            componentesFiltrados: [],
            carregando: false,
            organizacao: {
                editavel,
                modoEdicao: false,
                dragIdAtivo: null,
                alertouPendencia: false
            }
        };
    }

    async function iniciarTela(estado) {
        try {
            prepararAbas(estado);
            prepararFiltros(estado);
            prepararBotoesOrganizacao(estado);
            prepararModais(estado);
            prepararOrganizacaoVisual(estado);
            await buscarComponentes(estado);
        } catch (erro) {
            console.error('Erro ao carregar visão da matriz:', erro);
            notificador?.erro?.('Erro ao carregar dados da matriz curricular.', null, { alvo: '#matriz-tabs' });
        }
    }

    function prepararAbas(estado) {
        const { abas } = estado.elementos;
        if (!abas?.length) {
            return;
        }

        abas.forEach(botao => {
            botao.addEventListener('click', () => {
                if (botao.classList.contains('tab-active')) {
                    return;
                }
                ativarAba(botao, estado);
            });
        });
    }

    function ativarAba(botaoAtivo, estado) {
        const { abas, paineis } = estado.elementos;
        abas.forEach(botao => {
            botao.classList.remove('tab-active', 'border-sky-500', 'text-sky-600');
            botao.classList.add('border-transparent', 'text-gray-500');
        });

        botaoAtivo.classList.add('tab-active', 'border-sky-500', 'text-sky-600');
        botaoAtivo.classList.remove('border-transparent', 'text-gray-500');

        paineis.forEach(painel => {
            const estavaVisivel = !painel.classList.contains('hidden');
            painel.classList.add('hidden');

            if (estavaVisivel && painel.id === 'fluxograma') {
                window.ocultarFluxogramaLinhas?.();
            }

            if (estavaVisivel && painel.id === 'organizar-matriz') {
                window.ocultarLinhasOrganizacao?.();
            }
        });

        const alvo = document.querySelector(botaoAtivo.dataset.tabTarget);
        if (!alvo) {
            return;
        }

        alvo.classList.remove('hidden');

        if (alvo.id === 'fluxograma') {
            setTimeout(() => sincronizarFluxograma(estado), 120);
        }

        if (alvo.id === 'organizar-matriz') {
            setTimeout(() => {
                atualizarOrganizacaoVisual(estado);
                window.recriarLinhasOrganizacao?.();
            }, 80);
        }
    }

    function prepararFiltros(estado) {
        const { filtroPeriodo, filtroTipo } = estado.elementos;

        filtroPeriodo?.addEventListener('change', evento => {
            estado.filtros.periodo = evento.target.value || '';
            aplicarFiltros(estado);
        });

        filtroTipo?.addEventListener('change', evento => {
            estado.filtros.tipo = evento.target.value || '';
            aplicarFiltros(estado);
        });
    }

    async function buscarComponentes(estado) {
        if (!estado.config.permissoes.visualizar) {
            return;
        }

        const rota = estado.config.rotas.componentes;
        if (!rota) {
            console.warn('Rota para componentes não informada.');
            return;
        }

        definirEstadoCarregamento(estado, true);

        try {
            const resposta = await fetch(rota, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const payload = await resposta.json();
            if (!payload?.sucesso) {
                throw new Error(payload?.mensagem || 'Erro ao carregar componentes');
            }

            const componentesNormalizados = Array.isArray(payload.componentes)
                ? payload.componentes.map(normalizarComponente)
                : [];

            estado.componentes = componentesNormalizados;
            estado.componentesFiltrados = [...componentesNormalizados];

            popularFiltros(estado);
            desenharTabelaComponentes(estado, estado.componentesFiltrados);
            atualizarResumoComponentes(componentesNormalizados);
            atualizarOrganizacaoVisual(estado);
            sincronizarFluxograma(estado);
        } catch (erro) {
            console.error('Erro ao buscar componentes curriculares:', erro);
            notificador?.erro?.('Erro ao carregar componentes curriculares.', null, { alvo: '#componentes' });
        } finally {
            definirEstadoCarregamento(estado, false);
        }
    }

    function definirEstadoCarregamento(estado, carregando) {
        estado.carregando = carregando;
        if (!estado.elementos.tabelaComponentes) {
            return;
        }

        if (carregando) {
            estado.elementos.tabelaComponentes.innerHTML = `
                <div class="flex justify-center items-center text-gray-400 gap-3 py-12">
                    <span class="material-icons-sharp animate-spin">autorenew</span>
                    <span>Carregando componentes...</span>
                </div>
            `;
        }
    }

    function normalizarComponente(componente) {
        const periodoNumero = Number.isFinite(parseInt(componente.periodo, 10))
            ? parseInt(componente.periodo, 10)
            : null;

        const normalizarListaNominal = lista => (Array.isArray(lista)
            ? lista.map(item => ({
                ...item,
                nome: corrigirEncodingSeNecessario(item?.nome || '')
            }))
            : []);

        return {
            ...componente,
            periodo: periodoNumero,
            nome: corrigirEncodingSeNecessario(componente.nome || ''),
            codigo: corrigirEncodingSeNecessario(componente.codigo || ''),
            tipo: corrigirEncodingSeNecessario(componente.tipo || 'Obrigatória'),
            creditos: parseInt(componente.creditos, 10) || 0,
            carga_horaria: parseInt(componente.carga_horaria, 10) || 0,
            pre_requisitos: normalizarListaNominal(componente.pre_requisitos),
            equivalencias: normalizarListaNominal(componente.equivalencias)
        };
    }

    function popularFiltros(estado) {
        const { filtroPeriodo } = estado.elementos;
        if (!filtroPeriodo) {
            return;
        }

        const opcoes = ['<option value="">Todos os períodos</option>'];
        const periodos = obterPeriodosOrdenados(estado.componentes);
        periodos.forEach(periodo => {
            opcoes.push(`<option value="${periodo}">${periodo}º Período</option>`);
        });
        opcoes.push('<option value="sem-periodo">Sem período</option>');
        filtroPeriodo.innerHTML = opcoes.join('');

        if (estado.filtros.periodo) {
            filtroPeriodo.value = estado.filtros.periodo;
        }
    }

    function aplicarFiltros(estado) {
        const filtrados = estado.componentes.filter(componente => {
            const periodoFiltro = estado.filtros.periodo;
            const tipoFiltro = estado.filtros.tipo;

            const passaPeriodo = !periodoFiltro
                || (periodoFiltro === 'sem-periodo' && !Number.isInteger(componente.periodo))
                || Number(periodoFiltro) === componente.periodo;

            const passaTipo = !tipoFiltro || componente.tipo === tipoFiltro;

            return passaPeriodo && passaTipo;
        });

        estado.componentesFiltrados = filtrados;
        desenharTabelaComponentes(estado, filtrados);
    }

    function desenharTabelaComponentes(estado, componentes) {
        const container = estado.elementos.tabelaComponentes;
        if (!container) {
            return;
        }

        if (!componentes.length) {
            container.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhum componente curricular encontrado com os filtros selecionados.</p>';
            return;
        }

        const agrupado = componentes.reduce((acumulado, componente) => {
            const chave = Number.isInteger(componente.periodo) ? componente.periodo : 'sem-periodo';
            if (!acumulado[chave]) {
                acumulado[chave] = [];
            }
            acumulado[chave].push(componente);
            return acumulado;
        }, {});

        const periodosOrdenados = Object.keys(agrupado).sort((a, b) => {
            if (a === 'sem-periodo') return 1;
            if (b === 'sem-periodo') return -1;
            return Number(a) - Number(b);
        });

        const tabela = document.createElement('table');
        tabela.className = 'min-w-full divide-y divide-gray-200';
        tabela.innerHTML = `
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Créditos</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Carga Horária</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pré-requisitos</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
        `;
        const corpoTabela = document.createElement('tbody');
        corpoTabela.className = 'bg-white divide-y divide-gray-100';

        const templateLinha = document.getElementById('template-tabela-componentes-linha');
        const templateCabecalho = document.getElementById('template-tabela-componentes-cabecalho-periodo');

        if (!templateLinha || !templateCabecalho) {
            container.innerHTML = '<p class="text-center text-red-500 py-8">Templates da tabela não encontrados.</p>';
            return;
        }

        periodosOrdenados.forEach(periodo => {
            const titulo = periodo === 'sem-periodo' ? 'Componentes sem período' : `${periodo}º Período`;
            const cabecalho = templateCabecalho.content.cloneNode(true);
            cabecalho.querySelector('[data-template="titulo"]').textContent = titulo;
            corpoTabela.appendChild(cabecalho);

            agrupado[periodo].forEach(componente => {
                const linhaNode = templateLinha.content.cloneNode(true);
                const linha = linhaNode.querySelector('tr');
                linha.querySelector('[data-template="nome"]').textContent = componente.nome;
                linha.querySelector('[data-template="codigo"]').textContent = componente.codigo || '';
                linha.querySelector('[data-template="tipo"]').textContent = componente.tipo;
                linha.querySelector('[data-template="creditos"]').textContent = componente.creditos;
                linha.querySelector('[data-template="carga-horaria"]').textContent = `${componente.carga_horaria}h`;
                linha.querySelector('[data-template="prerequisitos"]').textContent = componente.pre_requisitos.map(p => p.nome).join(', ') || 'Nenhum';
                
                const acoesContainer = linha.querySelector('[data-template="acoes"]');
                acoesContainer.innerHTML = renderizarAcoesComponente(componente);
                // Adiciona o ID do componente ao botão para fácil acesso
                const botaoDetalhes = acoesContainer.querySelector('button');
                if(botaoDetalhes) {
                    botaoDetalhes.dataset.componenteId = componente.id;
                }

                corpoTabela.appendChild(linhaNode);
            });
        });

        tabela.appendChild(corpoTabela);
        
        const wrapper = document.createElement('div');
        wrapper.className = 'overflow-hidden border border-gray-200 rounded-2xl shadow-sm';
        wrapper.appendChild(tabela);

        container.innerHTML = '';
        container.appendChild(wrapper);
        vincularAcoesTabela(container, estado);
    }

    function renderizarAcoesComponente(componente) {
        return `
            <button type="button" class="text-sky-600 hover:text-sky-800 text-xs font-semibold" data-acao="detalhes" data-componente-id="${componente.id}">
                Ver detalhes
            </button>
        `;
    }

    function vincularAcoesTabela(container, estado) {
        container.querySelectorAll('[data-acao="detalhes"]').forEach(botao => {
            botao.addEventListener('click', evento => {
                const id = evento.currentTarget.getAttribute('data-componente-id');
                const componente = estado.componentes.find(item => String(item.id) === String(id));
                if (componente) {
                    mostrarDetalhesComponente(componente, estado);
                }
            });
        });
    }

    function atualizarResumoComponentes(componentes) {
        const total = componentes.length;
        const obrigatorias = componentes.filter(item => item.tipo === 'Obrigatória').length;
        const optativas = componentes.filter(item => item.tipo === 'Optativa').length;
        const cargaHoraria = componentes.reduce((soma, item) => soma + (item.carga_horaria || 0), 0);

        const totalSpan = document.getElementById('stat-total-componentes');
        const obrigatoriasSpan = document.getElementById('stat-obrigatorias');
        const optativasSpan = document.getElementById('stat-optativas');
        const cargaSpan = document.getElementById('stat-carga-horaria');

        if (totalSpan) totalSpan.textContent = total;
        if (obrigatoriasSpan) obrigatoriasSpan.textContent = obrigatorias;
        if (optativasSpan) optativasSpan.textContent = optativas;
        if (cargaSpan) cargaSpan.textContent = `${cargaHoraria}h`;
    }

    function prepararModais(estado) {
        const templateAdicionar = document.getElementById('template-componente-modal-adicionar');
        if (templateAdicionar && !document.getElementById('componente-modal-adicionar')) {
            document.body.appendChild(document.importNode(templateAdicionar.content, true));
        }

        if (window.Modal && document.getElementById('componente-modal-adicionar')) {
            estado.modais.adicionarComponente = new Modal('#componente-modal-adicionar');
        }

        // Preparar o modal de detalhes
        if (!document.getElementById('modal-detalhes-componente')) {
            const templateDetalhes = document.getElementById('template-detalhes-componente-modal');
            if (templateDetalhes) {
                document.body.appendChild(document.importNode(templateDetalhes.content, true));
            } else {
                console.error('Template template-detalhes-componente-modal não encontrado');
                return;
            }
        }

        // Função para tentar inicializar o modal
        const tentarInicializarModal = () => {
            if (typeof Modal !== 'undefined') {
                const modalEl = document.getElementById('modal-detalhes-componente');
                if (modalEl) {
                    estado.modais.detalhesComponente = new Modal('#modal-detalhes-componente');
                    console.log('Modal de detalhes inicializado com sucesso');
                } else {
                    console.error('Elemento modal-detalhes-componente não encontrado');
                }
            } else {
                // Se Modal ainda não está disponível, tenta novamente após um pequeno delay
                setTimeout(tentarInicializarModal, 50);
            }
        };

        // Aguarda o próximo frame e então tenta inicializar
        requestAnimationFrame(tentarInicializarModal);

        const acionarModal = () => abrirModalAdicionarComponente(estado);
        estado.elementos.botaoGradeNovo?.addEventListener('click', acionarModal);
        estado.elementos.botaoNovaDisciplina?.addEventListener('click', acionarModal);
    }

    function abrirModalAdicionarComponente(estado) {
        const modal = estado.modais.adicionarComponente;
        if (!modal) {
            notificador?.erro?.('Não foi possível abrir o formulário de componentes.');
            return;
        }

        const inputMatriz = modal.modal?.querySelector('input[name="matriz_curricular_id"]');
        if (inputMatriz) {
            inputMatriz.value = estado.config.matrizId || '';
        }

        modal.abrir();
    }

    function prepararBotoesOrganizacao(estado) {
        const { botaoModoEdicao, botaoNovaDisciplina } = estado.elementos;

        if (botaoModoEdicao && estado.organizacao.editavel) {
            botaoModoEdicao.addEventListener('click', () => alternarModoOrganizacao(estado));
        } else if (botaoModoEdicao) {
            botaoModoEdicao.disabled = true;
            botaoModoEdicao.classList.add('opacity-50', 'cursor-not-allowed');
        }

        botaoNovaDisciplina?.classList.add('hidden');
    }

    function alternarModoOrganizacao(estado) {
        if (!estado.organizacao.editavel) {
            return;
        }

        estado.organizacao.modoEdicao = !estado.organizacao.modoEdicao;
        const texto = estado.organizacao.modoEdicao ? 'Concluir edição' : 'Editar Matriz';
        if (estado.elementos.textoBotaoModoEdicao) {
            estado.elementos.textoBotaoModoEdicao.textContent = texto;
        }

        estado.elementos.botaoNovaDisciplina?.classList.toggle('hidden', !estado.organizacao.modoEdicao);
        atualizarOrganizacaoVisual(estado);
        atualizarAreaNaoVinculados(estado);

        if (estado.organizacao.modoEdicao) {
            notificador?.info?.('Arraste os componentes para reorganizar os períodos.');
        }
    }

    function prepararOrganizacaoVisual(estado) {
        const quadro = estado.elementos.quadroOrganizacao;
        if (!quadro) {
            return;
        }

        quadro.innerHTML = `
            <div class="m-auto flex flex-col items-center text-gray-400 py-16">
                <span class="material-icons-sharp text-3xl animate-spin mb-2">autorenew</span>
                <span class="text-sm text-center">Carregando estrutura curricular...</span>
            </div>
`;
    }

    function atualizarOrganizacaoVisual(estado) {
        const quadro = estado.elementos.quadroOrganizacao;
        if (!quadro) {
            return;
        }

        if (!estado.componentes.length) {
            quadro.innerHTML = `
                <div class="m-auto flex flex-col items-center text-gray-400 py-12">
                    <span class="material-icons-sharp text-3xl mb-2">table_view</span>
                    <span class="text-sm">Nenhum componente cadastrado para organizar.</span>
                </div>
`;
            atualizarAreaNaoVinculados(estado);
            return;
        }

        const fragmento = document.createDocumentFragment();
        const periodos = obterPeriodosOrdenados(estado.componentes);
        periodos.forEach(periodo => fragmento.appendChild(criarColunaOrganizacao(estado, periodo)));

        quadro.innerHTML = '';
        quadro.appendChild(fragmento);

        aplicarEstadoEdicaoOrganizacao(estado);
        atualizarAreaNaoVinculados(estado);
    }

    function criarColunaOrganizacao(estado, periodo) {
        const template = document.getElementById('template-organizacao-coluna');
        if (!template) {
            console.warn('Template da coluna de organização não encontrado.');
            return document.createElement('div');
        }

        const coluna = template.content.cloneNode(true).firstElementChild;
        
        coluna.querySelector('[data-template="titulo"]').textContent = `${periodo}º Período`;
        coluna.querySelector('[data-template="subtitulo"]').textContent = rotuloPeriodo(periodo);
        
        const componentesDoPeriodo = estado.componentes.filter(item => item.periodo === periodo);
        coluna.querySelector('[data-template="contador"]').textContent = componentesDoPeriodo.length;

        const lista = coluna.querySelector('[data-template="lista"]');
        lista.dataset.periodId = String(periodo);

        if (!componentesDoPeriodo.length) {
            lista.innerHTML = '<p class="text-xs text-gray-400 text-center py-6">Sem componentes</p>';
        } else {
            componentesDoPeriodo.forEach(componente => lista.appendChild(criarCardOrganizacao(estado, componente)));
        }

        permitirSoltar(estado, lista);
        return coluna;
    }

    function criarCardOrganizacao(estado, componente) {
        const template = document.getElementById('template-organizacao-card');
        if (!template) {
            console.warn('Template do card de organização não encontrado.');
            return document.createElement('div');
        }

        const card = template.content.cloneNode(true).firstElementChild;
        card.dataset.componenteId = componente.id;

        const badge = card.querySelector('[data-template="tipo-badge"]');
        badge.className += ` ${classeBadgePorTipo(componente.tipo)}`;
        badge.textContent = componente.tipo;

        card.querySelector('[data-template="codigo"]').textContent = componente.codigo || '';
        card.querySelector('[data-template="nome"]').textContent = componente.nome;
        card.querySelector('[data-template="creditos"]').textContent = `${componente.creditos} créd.`;
        card.querySelector('[data-template="carga-horaria"]').textContent = `${componente.carga_horaria}h`;
        
        const preRequisitosTexto = componente.pre_requisitos.map(item => item.nome).join(', ') || 'Nenhum';
        const preRequisitosEl = card.querySelector('[data-template="prerequisitos"]');
        preRequisitosEl.textContent = `Pré: ${preRequisitosTexto}`;
        preRequisitosEl.title = `Pré: ${preRequisitosTexto}`;

        card.addEventListener('click', () => mostrarDetalhesComponente(componente, estado));

        if (estado.organizacao.modoEdicao) {
            habilitarArraste(card, estado);
        }

        return card;
    }

    function habilitarArraste(card, estado) {
        card.setAttribute('draggable', 'true');
        card.addEventListener('dragstart', evento => {
            estado.organizacao.dragIdAtivo = card.dataset.componenteId;
            evento.dataTransfer?.setData('text/plain', card.dataset.componenteId);
            card.classList.add('opacity-50');
        });

        card.addEventListener('dragend', () => {
            estado.organizacao.dragIdAtivo = null;
            card.classList.remove('opacity-50');
        });
    }

    function permitirSoltar(estado, zona) {
        zona.addEventListener('dragover', evento => {
            if (!estado.organizacao.modoEdicao) {
                return;
            }
            evento.preventDefault();
            zona.classList.add(...CLASSES_DROP_ATIVO);
        });

        zona.addEventListener('dragleave', () => {
            zona.classList.remove(...CLASSES_DROP_ATIVO);
        });

        zona.addEventListener('drop', evento => {
            if (!estado.organizacao.modoEdicao) {
                return;
            }

            evento.preventDefault();
            zona.classList.remove(...CLASSES_DROP_ATIVO);

            const componenteId = evento.dataTransfer?.getData('text/plain') || estado.organizacao.dragIdAtivo;
            if (!componenteId) {
                return;
            }

            moverComponenteParaPeriodo(estado, componenteId, zona.dataset.periodId);
        });
    }

    function moverComponenteParaPeriodo(estado, componenteId, destinoPeriodo) {
        const alvo = estado.componentes.find(item => String(item.id) === String(componenteId));
        if (!alvo) {
            return;
        }

        alvo.periodo = destinoPeriodo === 'null' || destinoPeriodo === 'sem-periodo'
            ? null
            : parseInt(destinoPeriodo, 10);

        if (!estado.organizacao.alertouPendencia) {
            estado.organizacao.alertouPendencia = true;
            notificador?.info?.('Alteração aplicada localmente. Integração com salvamento será adicionada em breve.');
        }

        atualizarOrganizacaoVisual(estado);
        aplicarFiltros(estado);
    }

    function aplicarEstadoEdicaoOrganizacao(estado) {
        const quadro = estado.elementos.quadroOrganizacao;
        if (!quadro) {
            return;
        }

        quadro.querySelectorAll('.matrix-card').forEach(card => {
            if (estado.organizacao.modoEdicao) {
                habilitarArraste(card, estado);
            } else {
                card.removeAttribute('draggable');
            }
        });
    }

    function atualizarAreaNaoVinculados(estado) {
        const area = estado.elementos.areaNaoVinculados;
        const lista = estado.elementos.listaNaoVinculados;
        if (!area || !lista) {
            return;
        }

        const naoVinculados = estado.componentes.filter(item => !Number.isInteger(item.periodo));
        area.classList.toggle('hidden', !estado.organizacao.modoEdicao && naoVinculados.length === 0);

        lista.innerHTML = '';
        lista.dataset.periodId = 'null';
        lista.className = 'drop-zone flex flex-wrap gap-3 min-h-[100px] p-2 rounded-lg bg-white';
        permitirSoltar(estado, lista);

        if (!naoVinculados.length) {
            lista.innerHTML = '<p class="text-xs text-gray-400">Nenhum componente desacoplado.</p>';
            return;
        }

        naoVinculados.forEach(componente => {
            const card = criarCardOrganizacao(estado, componente);
            card.classList.add('w-60');
            lista.appendChild(card);
        });
    }

    function mostrarDetalhesComponente(componente, estado) {
        // Fallback para obter estado global se não for passado
        if (!estado) {
            estado = window.obterEstadoVisualizarMatriz?.();
        }

        if (!estado?.modais?.detalhesComponente) {
            console.warn('Modal de detalhes não disponível.');
            return;
        }

        const modal = estado.modais.detalhesComponente;
        const corpoModal = modal.modal?.querySelector('[data-template="corpo"]');
        if (!corpoModal) {
            console.warn('Corpo do modal de detalhes não encontrado.');
            return;
        }

        const listaPreReq = Array.isArray(componente.pre_requisitos) && componente.pre_requisitos.length > 0
            ? componente.pre_requisitos.map(pr => `<li>${pr.nome}</li>`).join('')
            : '<li>Nenhum</li>';

        const listaEquivalencias = Array.isArray(componente.equivalencias) && componente.equivalencias.length > 0
            ? componente.equivalencias.map(eq => `<li>${eq.nome}</li>`).join('')
            : '<li>Nenhuma</li>';

        corpoModal.innerHTML = `
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Componente</div>
                <div class="text-lg font-bold text-gray-900">${componente.nome}</div>
                ${componente.codigo ? `<div class="text-sm text-gray-500 mt-1">Código: ${componente.codigo}</div>` : ''}
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 bg-gray-50 p-4 rounded-md">
                <div><strong class="text-gray-500">Tipo:</strong> ${componente.tipo}</div>
                <div><strong class="text-gray-500">Período:</strong> ${componente.periodo ?? 'Sem período'}</div>
                <div><strong class="text-gray-500">Créditos:</strong> ${componente.creditos || 0}</div>
                <div><strong class="text-gray-500">Carga horária:</strong> ${(componente.carga_horaria || 0)}h</div>
            </div>
            <div class="text-sm">
                <div class="font-semibold text-gray-600 mb-2">Pré-requisitos</div>
                <ul class="list-disc list-inside text-gray-600">${listaPreReq}</ul>
            </div>
            <div class="text-sm">
                <div class="font-semibold text-gray-600 mb-2">Equivalências</div>
                <ul class="list-disc list-inside text-gray-600">${listaEquivalencias}</ul>
            </div>
        `;

        modal.abrir();
    }

    function obterPeriodosOrdenados(componentes) {
        const set = new Set();
        componentes.forEach(componente => {
            if (Number.isInteger(componente.periodo)) {
                set.add(componente.periodo);
            }
        });
        return Array.from(set).sort((a, b) => a - b);
    }

    function rotuloPeriodo(periodo) {
        if (periodo === 1) return 'Ciclo inicial';
        if (periodo === 2 || periodo === 3) return 'Fundamentos';
        return 'Período avançado';
    }

    function classeBadgePorTipo(tipo) {
        switch (tipo) {
            case 'Optativa':
                return 'bg-green-100 text-green-700 border border-green-200';
            case 'Eletiva':
                return 'bg-purple-100 text-purple-700 border border-purple-200';
            default:
                return 'bg-sky-100 text-sky-700 border border-sky-200';
        }
    }

    function sincronizarFluxograma(estado) {
        const painelFluxograma = document.getElementById('fluxograma');
        if (!painelFluxograma || painelFluxograma.classList.contains('hidden')) {
            return;
        }

        if (typeof inicializarFluxograma === 'function' && estado.componentes.length) {
            inicializarFluxograma(estado.componentes, estado.config.matrizId);
        }
    }

    function registrarEstadoGlobal(estado) {
        window[ESTADO_GLOBAL_CHAVE] = estado;
        window.obterEstadoVisualizarMatriz = () => window[ESTADO_GLOBAL_CHAVE];
        window.recarregarComponentesMatriz = () => {
            const estadoAtual = window[ESTADO_GLOBAL_CHAVE];
            return estadoAtual ? buscarComponentes(estadoAtual) : Promise.resolve();
        };
    }

    if (typeof window !== 'undefined') {
        window.recriarLinhasOrganizacao = function () {
            /* Integração de linhas será adicionada futuramente. */
        };

        window.ocultarLinhasOrganizacao = function () {
            /* Ainda não há linhas para remover. */
        };
    }
})();






