/**
 * Fluxograma interativo utilizando LeaderLine
 * Renderiza as disciplinas por período e conecta pré-requisitos no vão entre os cards
 */

const fluxogramaState = {
    componentes: [],
    linhas: [],
    layout: 'vertical',
    container: null,
    board: null,
    eventosVinculados: false,
    modoVinculoAtivo: false,
    origemVinculo: null,
    componenteSelecionado: null, // Novo: rastreia o componente clicado
    modalDetalhes: null, // Novo: instância do modal de detalhes
    opcoes: {
        diagonais: false,
        espacamento: 'padrao'
    }
};

const reposicionarLinhasDebounced = debounce(reposicionarLinhas, 80);

/**
 * Inicializa o fluxograma interativo
 */
function inicializarFluxograma(componentes) {
    if (!Array.isArray(componentes)) {
        console.warn('Nenhum componente para renderizar o fluxograma.');
        return;
    }

    const canvas = document.getElementById('fluxograma-canvas');
    if (!canvas) {
        console.warn('Elemento #fluxograma-canvas não encontrado.');
        return;
    }

    if (typeof LeaderLine === 'undefined') {
        if (typeof notificador !== 'undefined') {
            notificador.erro('Não foi possível carregar o fluxograma. Biblioteca de conexões indisponível.');
        }
        console.error('LeaderLine não carregado.');
        return;
    }

    // Novo: Preparar o modal de detalhes
    prepararModalDetalhes();

    fluxogramaState.componentes = componentes;
    fluxogramaState.container = canvas;

    renderizarFluxograma();
    vincularEventosGlobais();
}

/**
 * Renderiza os períodos e componentes na tela
 */
function renderizarFluxograma() {
    limparLinhas();
    limparSelecaoVinculo();

    const periodos = extrairPeriodos(fluxogramaState.componentes);
    const componentesSemPeriodo = fluxogramaState.componentes.filter(comp => !Number.isInteger(parseInt(comp.periodo, 10)));

    const board = document.createElement('div');
    board.className = 'fluxo-board';
    if (fluxogramaState.layout === 'horizontal') {
        board.classList.add('fluxo-board-horizontal');
    }

    aplicarEstiloEspacamento(board);

    if (!fluxogramaState.container) {
        console.warn('Área do fluxograma não encontrada.');
        return;
    }

    fluxogramaState.container.innerHTML = '';
    fluxogramaState.container.appendChild(board);
    fluxogramaState.board = board;

    board.addEventListener('scroll', reposicionarLinhasDebounced, { passive: true });
    
    // Novo: Limpa a seleção ao clicar no fundo do board
    board.addEventListener('click', (evento) => {
        if (evento.target === board) {
            processarCliqueComponente(null);
        }
    });

    if (periodos.length === 0 && componentesSemPeriodo.length === 0) {
        board.innerHTML = `<div class="flex items-center justify-center w-full text-sm text-gray-500 py-16">Nenhum componente curricular cadastrado nesta matriz.</div>`;
        return;
    }

    periodos.forEach(periodo => {
        board.appendChild(criarColunaPeriodo(periodo));
    });

    if (componentesSemPeriodo.length > 0) {
        board.appendChild(criarColunaSemPeriodo(componentesSemPeriodo));
    }

    requestAnimationFrame(() => {
        desenharConexoes();
    });
}

/**
 * Cria a coluna de um período específico
 */
function criarColunaPeriodo(periodo) {
    const template = document.getElementById('template-fluxo-coluna');
    if (!template) {
        console.warn('Template da coluna do fluxograma não encontrado.');
        return document.createElement('div');
    }

    const coluna = template.content.cloneNode(true).firstElementChild;
    coluna.dataset.periodo = periodo;

    coluna.querySelector('[data-template="titulo"]').textContent = `${periodo}º Período`;
    coluna.querySelector('[data-template="subtitulo"]').textContent = rotuloPeriodo(periodo);

    const componentesDoPeriodo = fluxogramaState.componentes.filter(c => parseInt(c.periodo, 10) === periodo);
    coluna.querySelector('[data-template="contador"]').textContent = componentesDoPeriodo.length;

    const lista = coluna.querySelector('[data-template="lista"]');
    lista.dataset.periodoId = periodo;

    if (componentesDoPeriodo.length === 0) {
        coluna.dataset.empty = 'true';
    } else {
        componentesDoPeriodo.forEach(componente => {
            lista.appendChild(criarCardComponente(componente));
        });
    }

    return coluna;
}

/**
 * Cria a coluna para componentes sem período definido
 */
function criarColunaSemPeriodo(componentes) {
    const template = document.getElementById('template-fluxo-coluna');
    if (!template) {
        console.warn('Template da coluna do fluxograma não encontrado.');
        return document.createElement('div');
    }

    const coluna = template.content.cloneNode(true).firstElementChild;
    coluna.dataset.periodo = 'sem-periodo';

    coluna.querySelector('[data-template="titulo"]').textContent = 'Sem período';
    coluna.querySelector('[data-template="subtitulo"]').textContent = 'Componentes não alocados';
    coluna.querySelector('[data-template="contador"]').textContent = componentes.length;

    const lista = coluna.querySelector('[data-template="lista"]');
    coluna.dataset.empty = componentes.length === 0 ? 'true' : 'false';

    componentes.forEach(componente => {
        lista.appendChild(criarCardComponente(componente));
    });

    return coluna;
}

/**
 * Cria o card visual de um componente curricular
 */
function criarCardComponente(componente) {
    const template = document.getElementById('template-fluxo-card');
    if (!template) {
        console.warn('Template do card do fluxograma não encontrado.');
        return document.createElement('div');
    }

    const card = template.content.cloneNode(true).firstElementChild;
    card.id = `fluxo-comp-${componente.id}`;
    card.dataset.componenteId = componente.id;

    const estilo = obterEstiloTipo(componente.tipo);
    card.style.borderColor = estilo.borda;

    const badge = card.querySelector('[data-template="tipo-badge"]');
    badge.className = `fluxo-card-badge ${estilo.badgeClass}`;
    badge.textContent = estilo.rotulo;

    card.querySelector('[data-template="codigo"]').textContent = componente.codigo || '';
    card.querySelector('[data-template="nome"]').textContent = componente.nome;
    card.querySelector('[data-template="creditos"]').textContent = `${componente.creditos || 0} créd.`;
    card.querySelector('[data-template="carga-horaria"]').textContent = `${componente.carga_horaria || 0}h`;

    card.addEventListener('click', evento => {
        if (fluxogramaState.modoVinculoAtivo) {
            evento.preventDefault();
            evento.stopPropagation();
            processarCliqueParaVinculo(card, componente);
            return;
        }
        
        // Nova lógica de clique
        processarCliqueComponente(componente);
    });

    return card;
}

/**
 * Processa o clique em um componente no modo normal.
 * No primeiro clique, destaca as conexões. No segundo, mostra o modal.
 */
function processarCliqueComponente(componente) {
    // Se clicar fora de um card, limpa a seleção
    if (!componente) {
        limparDestaques();
        return;
    }

    if (fluxogramaState.componenteSelecionado && String(fluxogramaState.componenteSelecionado.id) === String(componente.id)) {
        mostrarDetalhesModal(componente);
        limparDestaques(); // Limpa o destaque ao abrir o modal
    } else {
        fluxogramaState.componenteSelecionado = componente;
        destacarConexoesComponente(componente);
    }
}

/**
 * Prepara a instância do modal de detalhes.
 */
function prepararModalDetalhes() {
    const containerModal = document.getElementById('modal-detalhes-componente');
    if (!containerModal) {
        const template = document.getElementById('template-detalhes-componente-modal');
        if (template) {
            document.body.appendChild(document.importNode(template.content, true));
        }
    }

    if (window.Modal && !fluxogramaState.modalDetalhes) {
        const el = document.getElementById('modal-detalhes-componente');
        if(el) {
            fluxogramaState.modalDetalhes = new Modal(el);
        }
    }
}

/**
 * Mostra o modal com informações detalhadas do componente.
 */
function mostrarDetalhesModal(componente) {
    if (!fluxogramaState.modalDetalhes) {
        // Fallback para o notificador se o modal falhar
        mostrarDetalhesNotificador(componente);
        return;
    }

    const corpoModal = fluxogramaState.modalDetalhes.modal.querySelector('[data-template="corpo"]');
    if (!corpoModal) return;

    const listaPreReq = Array.isArray(componente.pre_requisitos) && componente.pre_requisitos.length > 0
        ? componente.pre_requisitos.map(pr => `<li>${pr.sigla}</li>`).join('')
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
            <div><strong class="text-gray-500">Período:</strong> ${componente.periodo || 'N/A'}</div>
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

    fluxogramaState.modalDetalhes.abrir();
}

/**
 * Desenha as linhas entre componentes considerando pré-requisitos e equivalências
 */
function desenharConexoes() {
    limparLinhas();
    limparDestaques();

    const cardsMap = new Map();
    document.querySelectorAll('[id^="fluxo-comp-"]').forEach(card => {
        const id = parseInt(card.dataset.componenteId, 10);
        if (!Number.isNaN(id)) {
            cardsMap.set(id, card);
        }
    });

    const incomingCounts = new Map();
    const outgoingCounts = new Map();

    fluxogramaState.componentes.forEach(componente => {
        const destinoId = parseInt(componente.id, 10);
        if (!Number.isInteger(destinoId)) {
            return;
        }

        const destinoEl = cardsMap.get(destinoId);
        if (!destinoEl) {
            return;
        }

        const preRequisitos = Array.isArray(componente.pre_requisitos) ? componente.pre_requisitos : [];
        preRequisitos.forEach(pre => {
            const origemId = parseInt(pre.id, 10);
            if (!Number.isInteger(origemId)) {
                return;
            }

            const origemEl = cardsMap.get(origemId);
            if (!origemEl) {
                return;
            }

            incrementarMapa(incomingCounts, destinoId);
            incrementarMapa(outgoingCounts, origemId);
        });
    });

    const processedIncoming = new Map();
    const processedOutgoing = new Map();

    fluxogramaState.componentes.forEach(componente => {
        const destinoId = parseInt(componente.id, 10);
        if (!Number.isInteger(destinoId)) {
            return;
        }

        const destinoEl = cardsMap.get(destinoId);
        if (!destinoEl) {
            return;
        }

        const preRequisitos = Array.isArray(componente.pre_requisitos) ? componente.pre_requisitos : [];
        preRequisitos.forEach(pre => {
            const origemId = parseInt(pre.id, 10);
            if (!Number.isInteger(origemId)) {
                return;
            }

            const origemEl = cardsMap.get(origemId);
            if (!origemEl) {
                return;
            }

            const startIndex = processedOutgoing.get(origemId) || 0;
            const startTotal = outgoingCounts.get(origemId) || 1;
            const startGravity = calcularGravidade(startIndex, startTotal);
            processedOutgoing.set(origemId, startIndex + 1);

            const endIndex = processedIncoming.get(destinoId) || 0;
            const endTotal = incomingCounts.get(destinoId) || 1;
            const endGravity = calcularGravidade(endIndex, endTotal);
            processedIncoming.set(destinoId, endIndex + 1);

            const linhaPre = criarLinhaLeader(origemEl, destinoEl, 'prerequisito', startGravity, endGravity);
            if (linhaPre) {
                // Adiciona IDs para rastreamento
                linhaPre.start.dataset.componenteId = String(origemId);
                linhaPre.end.dataset.componenteId = String(destinoId);
                fluxogramaState.linhas.push(linhaPre);
            }
        });

        const equivalencias = Array.isArray(componente.equivalencias) ? componente.equivalencias : [];
        const equivalenciasValidas = equivalencias
            .map(eq => ({ ...eq, _id: parseInt(eq.id, 10) }))
            .filter(eq => Number.isInteger(eq._id) && destinoId < eq._id)
            .sort((a, b) => a._id - b._id);

        equivalenciasValidas.forEach((eq, index) => {
            const outroEl = cardsMap.get(eq._id);
            if (!outroEl) {
                return;
            }

            const offset = calcularGravidade(index, equivalenciasValidas.length) * 0.6;
            const linhaEq = criarLinhaLeader(destinoEl, outroEl, 'equivalencia', offset, offset);
            if (linhaEq) {
                // Adiciona IDs para rastreamento
                linhaEq.start.dataset.componenteId = String(destinoId);
                linhaEq.end.dataset.componenteId = String(eq._id);
                fluxogramaState.linhas.push(linhaEq);
            }
        });
    });

    setTimeout(reposicionarLinhas, 120);
}

/**
 * Cria uma linha LeaderLine personalizada
 */
function criarLinhaLeader(origem, destino, tipo, startGravity = 0, endGravity = 0) {
    const sockets = obterSocketsConexao(origem, destino, tipo);
    const startEhVertical = sockets.inicio === 'top' || sockets.inicio === 'bottom';
    const endEhVertical = sockets.fim === 'top' || sockets.fim === 'bottom';

    const configuracoesBase = {
        startSocket: sockets.inicio,
        endSocket: sockets.fim,
        startSocketGravity: startEhVertical ? [startGravity, 0] : [0, startGravity],
        endSocketGravity: endEhVertical ? [endGravity, 0] : [0, endGravity],
        path: sockets.path,
        size: tipo === 'equivalencia' ? 2.5 : 3,
        startPlug: 'disc',
        endPlug: tipo === 'equivalencia' ? 'arrow1' : 'arrow3',
        startPlugSize: 1.1,
        endPlugSize: 1.25,
        dash: tipo === 'equivalencia',
        color: tipo === 'equivalencia' ? '#eab308' : '#ef4444'
    };

    try {
        return new LeaderLine(origem, destino, configuracoesBase);
    } catch (erro) {
        console.warn('Falha ao desenhar conexão do fluxograma:', erro);
        return null;
    }
}

/**
 * Aplica o layout vertical ou horizontal
 */
function aplicarLayout(tipo) {
    if (fluxogramaState.layout === tipo) {
        return;
    }

    fluxogramaState.layout = tipo;
    renderizarFluxograma();
}

/**
 * Ajusta a visualização para o início do fluxograma
 */
function ajustarTela() {
    if (!fluxogramaState.container) {
        return;
    }

    fluxogramaState.container.scrollTo({ left: 0, top: 0, behavior: 'smooth' });
}

/**
 * Reposiciona as linhas ao deslocar/resize
 */
function reposicionarLinhas() {
    fluxogramaState.linhas = fluxogramaState.linhas.filter(linha => !!linha);
    fluxogramaState.linhas.forEach(linha => linha.position());
}

/**
 * Remove as linhas existentes
 */
function limparLinhas() {
    fluxogramaState.linhas.forEach(linha => linha && linha.remove());
    fluxogramaState.linhas = [];
}

if (typeof window !== 'undefined') {
    window.ocultarFluxogramaLinhas = function() {
        limparLinhas();
    };
}

function incrementarMapa(mapa, chave) {
    mapa.set(chave, (mapa.get(chave) || 0) + 1);
}

function calcularGravidade(indice, total) {
    if (total <= 1) {
        return 0;
    }

    if (total === 2) {
        return indice === 0 ? -22 : 22;
    }

    const amplitude = 28;
    const passo = (amplitude * 2) / (total - 1);
    return -amplitude + indice * passo;
}

/**
 * Mostra detalhes do componente em um toast (usado como fallback)
 */
function mostrarDetalhesNotificador(componente) {
    if (typeof notificador === 'undefined') {
        return;
    }

    const listaPreReq = Array.isArray(componente.pre_requisitos) && componente.pre_requisitos.length > 0
        ? componente.pre_requisitos.map(pr => pr.sigla).join(', ')
        : 'Nenhum';

    const listaEquivalencias = Array.isArray(componente.equivalencias) && componente.equivalencias.length > 0
        ? componente.equivalencias.map(eq => eq.sigla).join(', ')
        : 'Nenhuma';

    const detalhes = `
        <div class="space-y-3 text-left">
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Componente</div>
                <div class="text-base font-bold text-gray-900">${componente.nome}</div>
                ${componente.codigo ? `<div class="text-xs text-gray-500 mt-1">Código: ${componente.codigo}</div>` : ''}
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm text-gray-600">
                <div><span class="font-semibold text-gray-500">Tipo:</span> ${componente.tipo}</div>
                <div><span class="font-semibold text-gray-500">Período:</span> ${componente.periodo || 'N/A'}</div>
                <div><span class="font-semibold text-gray-500">Créditos:</span> ${componente.creditos || 0}</div>
                <div><span class="font-semibold text-gray-500">Carga horária:</span> ${(componente.carga_horaria || 0)}h</div>
            </div>
            <div class="text-sm text-gray-600">
                <div class="font-semibold text-gray-500 mb-1">Pré-requisitos</div>
                <p>${listaPreReq}</p>
            </div>
            <div class="text-sm text-gray-600">
                <div class="font-semibold text-gray-500 mb-1">Equivalências</div>
                <p>${listaEquivalencias}</p>
            </div>
        </div>
    `;

    notificador.info(detalhes);
}

/**
 * Configura eventos globais apenas uma vez
 */
function vincularEventosGlobais() {
    if (fluxogramaState.eventosVinculados) {
        return;
    }

    const btnFit = document.getElementById('btn-fit-screen');
    const btnVertical = document.getElementById('btn-layout-vertical');
    const btnHorizontal = document.getElementById('btn-layout-horizontal');
    const btnDiagonais = document.getElementById('btn-toggle-diagonais');
    const btnModoVinculo = document.getElementById('btn-modo-prerequisito');
    const seletorEspacamento = document.getElementById('fluxo-espacamento');

    btnFit?.addEventListener('click', ajustarTela);
    btnVertical?.addEventListener('click', () => aplicarLayout('vertical'));
    btnHorizontal?.addEventListener('click', () => aplicarLayout('horizontal'));

    btnDiagonais?.addEventListener('click', () => alternarDiagonais(btnDiagonais));
    atualizarBotaoDiagonais(btnDiagonais);

    btnModoVinculo?.addEventListener('click', () => alternarModoVinculo(btnModoVinculo));
    atualizarBotaoModoVinculo(btnModoVinculo);

    if (seletorEspacamento) {
        seletorEspacamento.value = fluxogramaState.opcoes.espacamento;
        seletorEspacamento.addEventListener('change', evento => ajustarEspacamento(evento.target.value));
    }

    fluxogramaState.container?.addEventListener('scroll', reposicionarLinhasDebounced, { passive: true });

    window.addEventListener('resize', reposicionarLinhasDebounced);

    fluxogramaState.eventosVinculados = true;
}

/**
 * Recupera conjunto de períodos existentes
 */
function extrairPeriodos(componentes) {
    const conjunto = new Set();
    componentes.forEach(componente => {
        const periodo = parseInt(componente.periodo, 10);
        if (!Number.isNaN(periodo)) {
            conjunto.add(periodo);
        }
    });
    return Array.from(conjunto).sort((a, b) => a - b);
}

/**
 * Retorna estilos específicos por tipo de componente
 */
function obterEstiloTipo(tipo) {
    const tipos = {
        'Obrigatória': {
            badgeClass: 'bg-sky-100 text-sky-700 border border-sky-200',
            borda: '#38bdf8',
            rotulo: 'Obrigatória'
        },
        'Optativa': {
            badgeClass: 'bg-green-100 text-green-700 border border-green-200',
            borda: '#22c55e',
            rotulo: 'Optativa'
        },
        'Eletiva': {
            badgeClass: 'bg-purple-100 text-purple-700 border border-purple-200',
            borda: '#a855f7',
            rotulo: 'Eletiva'
        }
    };

    return tipos[tipo] || {
        badgeClass: 'bg-gray-100 text-gray-600 border border-gray-200',
        borda: '#94a3b8',
        rotulo: tipo || 'Componente'
    };
}

/**
 * Texto auxiliar do período (ex: marcos)
 */
function rotuloPeriodo(periodo) {
    if (!Number.isFinite(periodo)) {
        return '';
    }

    if (periodo === 1) {
        return 'Ciclo inicial';
    }

    if (periodo === 2 || periodo === 3) {
        return 'Fundamentos';
    }

    return 'Período avançado';
}

function aplicarEstiloEspacamento(board) {
    const valores = {
        compacto: '0.75rem',
        padrao: '1.5rem',
        amplo: '2.5rem'
    };
    const valor = valores[fluxogramaState.opcoes.espacamento] || valores.padrao;
    board.style.gap = valor;
}

function alternarDiagonais(botao) {
    fluxogramaState.opcoes.diagonais = !fluxogramaState.opcoes.diagonais;
    atualizarBotaoDiagonais(botao || document.getElementById('btn-toggle-diagonais'));
    desenharConexoes();
}

function atualizarBotaoDiagonais(botao) {
    if (!botao) {
        return;
    }
    const ativo = fluxogramaState.opcoes.diagonais;
    botao.setAttribute('aria-pressed', ativo ? 'true' : 'false');
    botao.classList.toggle('bg-sky-600', ativo);
    botao.classList.toggle('text-white', ativo);
    botao.classList.toggle('border-sky-600', ativo);
}

function alternarModoVinculo(botao) {
    fluxogramaState.modoVinculoAtivo = !fluxogramaState.modoVinculoAtivo;
    if (!fluxogramaState.modoVinculoAtivo) {
        limparSelecaoVinculo();
    } else {
        notificador?.info?.('Selecione a disciplina que servirá como pré-requisito (origem).');
    }

    atualizarBotaoModoVinculo(botao || document.getElementById('btn-modo-prerequisito'));
}

function atualizarBotaoModoVinculo(botao) {
    if (!botao) {
        return;
    }

    const ativo = fluxogramaState.modoVinculoAtivo;
    botao.setAttribute('aria-pressed', ativo ? 'true' : 'false');
    botao.classList.remove('bg-sky-600', 'text-white', 'border-sky-600', 'bg-white', 'text-sky-700', 'shadow-md');

    if (ativo) {
        botao.classList.add('bg-sky-600', 'text-white', 'border-sky-600', 'shadow-md');
    } else {
        botao.classList.add('bg-white', 'text-sky-700', 'border-sky-600');
    }
}

function processarCliqueParaVinculo(card, componente) {
    if (!fluxogramaState.origemVinculo) {
        definirOrigemVinculo(card, componente);
        return;
    }

    const origem = fluxogramaState.origemVinculo.componente;
    if (String(origem.id) === String(componente.id)) {
        limparSelecaoVinculo();
        return;
    }

    const criado = criarVinculoPrerequisito(origem, componente);
    if (criado) {
        limparSelecaoVinculo();
    }
}

function definirOrigemVinculo(card, componente) {
    limparSelecaoVinculo();
    card.classList.add('ring-2', 'ring-red-400', 'ring-offset-2', 'ring-offset-white');
    fluxogramaState.origemVinculo = { card, componente };
    notificador?.info?.(`Agora escolha a disciplina que depende de '${componente.nome}'.`);
}

function limparSelecaoVinculo() {
    if (fluxogramaState.origemVinculo?.card) {
        fluxogramaState.origemVinculo.card.classList.remove('ring-2', 'ring-red-400', 'ring-offset-2', 'ring-offset-white');
    }
    fluxogramaState.origemVinculo = null;
}

function criarVinculoPrerequisito(origem, destino) {
    const preRequisitos = Array.isArray(destino.pre_requisitos) ? destino.pre_requisitos : [];
    const jaExiste = preRequisitos.some(item => String(item.id) === String(origem.id));
    if (jaExiste) {
        notificador?.aviso?.('Esse vínculo já existe para o componente selecionado.');
        return false;
    }

    if (typeof window.registrarVinculoPrerequisitoLocal === 'function') {
        const retorno = window.registrarVinculoPrerequisitoLocal(origem.id, destino.id);
        if (!retorno?.sucesso) {
            notificador?.erro?.(retorno?.mensagem || 'Não foi possível registrar o pré-requisito.');
            return false;
        }
    } else {
        aplicarVinculoLocal(origem, destino);
    }

    notificador?.sucesso?.(`'${origem.nome}' agora é pré-requisito de '${destino.nome}'.`, null, { tempo: 4 });
    desenharConexoes();
    return true;
}

function aplicarVinculoLocal(origem, destino) {
    if (!Array.isArray(destino.pre_requisitos)) {
        destino.pre_requisitos = [];
    }
    destino.pre_requisitos.push({ id: origem.id, nome: origem.nome, sigla: origem.sigla });
}

function ajustarEspacamento(valor) {
    fluxogramaState.opcoes.espacamento = valor || 'padrao';
    renderizarFluxograma();
}

function obterSocketsConexao(origem, destino, tipo) {
    if (!fluxogramaState.opcoes.diagonais || tipo === 'equivalencia') {
        return {
            inicio: 'right',
            fim: 'left',
            path: tipo === 'equivalencia' ? 'straight' : 'grid'
        };
    }

    const origemRect = origem.getBoundingClientRect();
    const destinoRect = destino.getBoundingClientRect();
    const destinoADireita = destinoRect.left >= origemRect.left;
    const diferencaVertical = destinoRect.top - origemRect.top;

    let inicio = destinoADireita ? 'right' : 'left';
    let fim = destinoADireita ? 'left' : 'right';

    if (Math.abs(diferencaVertical) > origemRect.height * 0.6) {
        const abaixo = diferencaVertical > 0;
        inicio = abaixo ? 'bottom' : 'top';
        fim = abaixo ? 'top' : 'bottom';
    }

    return {
        inicio,
        fim,
        path: 'straight'
    };
}

/**
 * Debounce utilitário para evitar múltiplas execuções consecutivas
 */
function debounce(func, wait = 100) {
    let timeout;
    return function executaDebounce(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

/**
 * Destaca as conexões (pré e pós-requisitos) de um componente.
 */
function destacarConexoesComponente(componente) {
    const { preRequisitos, posRequisitos, equivalencias } = obterConexoesComponente(componente);
    const idsConectados = new Set([
        componente.id,
        ...preRequisitos.map(c => c.id),
        ...posRequisitos.map(c => c.id),
        ...equivalencias.map(c => c.id)
    ]);

    // Ofusca todos os cards e linhas
    fluxogramaState.board.querySelectorAll('.fluxo-card').forEach(card => {
        card.classList.add('opacity-20', 'transition-opacity');
    });
    fluxogramaState.linhas.forEach(linha => {
        linha.setOptions({ color: 'rgba(200, 200, 200, 0.2)' });
    });

    // Remove o ofuscamento dos cards conectados
    idsConectados.forEach(id => {
        const card = document.getElementById(`fluxo-comp-${id}`);
        card?.classList.remove('opacity-20');
    });

    // Restaura a cor das linhas conectadas
    fluxogramaState.linhas.forEach(linha => {
        const origemId = linha.start.dataset.componenteId;
        const destinoId = linha.end.dataset.componenteId;

        const conectadoAoComponente = String(origemId) === String(componente.id) || String(destinoId) === String(componente.id);

        if (conectadoAoComponente) {
            const tipo = linha.dash ? 'equivalencia' : 'prerequisito';
            const corOriginal = tipo === 'equivalencia' ? '#eab308' : '#ef4444';
            linha.setOptions({ color: corOriginal });
        }
    });
}

/**
 * Remove todos os destaques de opacidade e cor.
 */
function limparDestaques() {
    fluxogramaState.componenteSelecionado = null;

    fluxogramaState.board.querySelectorAll('.fluxo-card').forEach(card => {
        card.classList.remove('opacity-20');
    });

    fluxogramaState.linhas.forEach(linha => {
        const tipo = linha.dash ? 'equivalencia' : 'prerequisito';
        const corOriginal = tipo === 'equivalencia' ? '#eab308' : '#ef4444';
        linha.setOptions({ color: corOriginal });
    });
}

/**
 * Obtém as conexões de um componente.
 */
function obterConexoesComponente(componente) {
    const id = String(componente.id);

    const preRequisitos = componente.pre_requisitos || [];

    const posRequisitos = fluxogramaState.componentes.filter(c =>
        Array.isArray(c.pre_requisitos) && c.pre_requisitos.some(pr => String(pr.id) === id)
    );

    const equivalencias = componente.equivalencias || [];

    return { preRequisitos, posRequisitos, equivalencias };
}