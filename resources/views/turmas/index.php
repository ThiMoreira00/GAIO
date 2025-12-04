<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Lista de Turmas</h1>
    <div class="flex-shrink-0">
        <button type="button" class="button-primary inline-flex items-center gap-2" data-modal2-trigger="turma-modal-adicionar" id="button-turma-adicionar">
            <span class="material-icons-sharp">add</span>
            Adicionar nova turma
        </button>
    </div>
</header>

<main id="main-turmas" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="turmas-section-heading">
        <h2 id="turmas-section-heading" class="sr-only">Turmas cadastradas</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-turma-filtros">
                    <form id="form-filtros-turmas" action="/turmas/filtrar" method="GET" data-datagrid-form>
                        <!-- Filtros de Status e Busca na mesma linha -->
                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4 mb-4">
                            <div class="flex-shrink-0">
                                <input type="hidden" name="status" id="status-input" value="" data-datagrid-status-input>
                                <div class="bg-gray-100 p-1 rounded-lg flex items-center justify-center flex-wrap gap-1">
                                    <button type="button" class="filter-btn-turma tab-item active" data-tab-status="" data-status="" data-datagrid-button>Todas</button>
                                    <button type="button" data-tab-status="ativa" class="filter-btn-turma tab-item" data-datagrid-button>Ativas</button>
                                    <button type="button" data-tab-status="confirmada" class="filter-btn-turma tab-item" data-datagrid-button>Confirmadas</button>
                                    <button type="button" data-tab-status="ofertada" class="filter-btn-turma tab-item" data-datagrid-button>Ofertadas</button>
                                    <button type="button" data-tab-status="planejada" class="filter-btn-turma tab-item" data-datagrid-button>Planejadas</button>
                                    <button type="button" data-tab-status="concluida" class="filter-btn-turma tab-item" data-datagrid-button>Concluídas</button>
                                    <button type="button" data-tab-status="arquivada" class="filter-btn-turma tab-item" data-datagrid-button>Arquivadas</button>
                                </div>
                            </div>
                            <div class="relative w-full lg:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-turma" class="input-search" placeholder="Buscar por código ou disciplina..." data-datagrid-search>
                            </div>
                        </div>

                        <!-- Filtros Avançados -->
                        <div class="flex flex-wrap justify-between items-center gap-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-sharp !text-lg text-gray-600">tune</span>
                                <span class="text-sm font-medium text-gray-600">Filtros avançados:</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <label for="periodo-input" class="text-sm font-medium text-gray-700 whitespace-nowrap">Período:</label>
                                    <select name="periodo_id" id="periodo-input" class="form-select text-sm" data-datagrid-filter>
                                        <option value="">Todos</option>
                                        <?php foreach ($periodos as $periodo): ?>
                                            <option value="<?= $periodo->obterId(); ?>"><?= $periodo->obterSigla(); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <label for="turno-input" class="text-sm font-medium text-gray-700 whitespace-nowrap">Turno:</label>
                                    <select name="turno" id="turno-input" class="form-select text-sm" data-datagrid-filter>
                                        <option value="">Todos</option>
                                        <?php foreach ($turnos as $turno): ?>
                                            <option value="<?= $turno->name; ?>"><?= ucfirst(strtolower($turno->value)); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <label for="modalidade-input" class="text-sm font-medium text-gray-700 whitespace-nowrap">Modalidade:</label>
                                    <select name="modalidade" id="modalidade-input" class="form-select text-sm" data-datagrid-filter>
                                        <option value="">Todas</option>
                                        <?php foreach ($modalidades as $modalidade): ?>
                                            <option value="<?= $modalidade->name; ?>"><?= $modalidade->value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="button" id="btn-limpar-filtros-turmas" class="text-sm px-4 py-2 text-gray-600 hover:text-sky-600 hover:bg-sky-50 rounded-lg font-medium transition-colors flex items-center gap-1.5 whitespace-nowrap">
                                    <span class="material-icons-sharp !text-lg">clear_all</span>
                                    Limpar
                                </button>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Container de resultados -->
                <div id="container-turmas" class="space-y-3"></div>

                <!-- Estado vazio -->
                <div id="estado-vazio-turmas" class="hidden text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <span class="material-icons-sharp !text-4xl text-gray-400">groups</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhuma turma encontrada</h3>
                    <p class="text-gray-500 text-base mb-6">Tente ajustar os filtros ou <a href="#" class="text-sky-600 underline font-semibold" data-modal2-trigger="turma-modal-adicionar">adicione uma nova turma</a>.</p>
                </div>

                <!-- Estado de erro -->
                <div id="estado-erro-turmas" class="hidden text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 mb-4">
                        <span class="material-icons-sharp !text-4xl text-red-400">error_outline</span>
                    </div>
                    <h3 class="text-lg font-semibold text-red-900 mb-2">Erro ao carregar turmas</h3>
                    <p class="text-gray-600 text-sm mb-6">Não foi possível conectar ao servidor. Verifique sua conexão.</p>
                    <button type="button" id="button-recarregar-turmas" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <span class="material-icons-sharp text-base mr-2">refresh</span>
                        Tentar novamente
                    </button>
                </div>

                <!-- Loader inicial e de scroll -->
                <!-- <div id="loader-turmas" class="hidden text-center py-4">
                    <span class="material-icons-sharp animate-spin text-4xl text-gray-400">autorenew</span>
                    <p class="text-gray-500 text-sm mt-2">Carregando turmas...</p>
                </div> -->
            </div>
        </div>
    </section>
</main>

<?php

    include __DIR__ . '/../templates/turma-lista-item.php';
    include __DIR__ . '/../templates/turma-modal-adicionar.php';
    include __DIR__ . '/../templates/turma-modal-editar.php';
    include __DIR__ . '/../templates/turma-modal-arquivar.php';
    include __DIR__ . '/../templates/turma-modal-confirmar.php';
    include __DIR__ . '/../templates/turma-modal-finalizar.php';
    include __DIR__ . '/../templates/turma-modal-liberar.php';
?>


<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/datagrid.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal2.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Botoes
        const buttonAdicionarTurma = document.getElementById('button-turma-adicionar');

        // Templates
        const templateModalAdicionarTurma = document.getElementById('template-turma-modal-adicionar');
        const templateModalEditarTurma = document.getElementById('template-turma-modal-editar');
        const templateModalArquivarTurma = document.getElementById('template-turma-modal-arquivar');
        const templateModalConfirmarTurma = document.getElementById('template-turma-modal-confirmar');
        const templateModalFinalizarTurma = document.getElementById('template-turma-modal-finalizar');
        const templateModalLiberarTurma = document.getElementById('template-turma-modal-liberar');

        const templateListaItemTurma = document.getElementById('template-lista-item-turma');

        const containerTurmas = document.getElementById('main-turmas');

        // =============================
        // INICIALIZAÇÃO DO DATAGRID
        // =============================

        // Inicialização do DataGrid
        const datagrid = new DataGrid({
            endpoint: '/turmas/filtrar',
            container: '#container-turmas',
            template: '#template-lista-item-turma',
            campos: {
                busca: '#busca-turma',
                status: '#status-input',
                periodo_id: '#periodo-input',
                turno: '#turno-input',
                modalidade: '#modalidade-input'
            },
            metodo: 'GET',
            itensPorPagina: 30,
            exibirLoader: true, // Deixa o datagrid controlar o loader
            debounceDelay: 1000,
            
            // Callbacks
            callbacks: {
                beforeLoad: async (parametros) => {
                    console.log('[DataGrid] Carregando turmas...', parametros);
                },
                
                onComplete: async (dados, parametros, response) => {
                    console.log(`[DataGrid] ${dados.length} turmas carregadas`);
                    console.log(`[DataGrid] Página ${response.current_page}/${response.last_page}`);
                    
                    // Oculta loader
                    const loader = document.getElementById('loader-turmas');
                    if (loader) loader.classList.add('hidden');
                },
                
                onError: async (error, parametros) => {
                    console.error('[DataGrid] Erro ao carregar turmas:', error);
                },
                
                onLoadMore: async (pagina) => {
                    console.log(`[DataGrid] Carregando página ${pagina}...`);
                    const loader = document.getElementById('loader-turmas');
                    if (loader) loader.classList.remove('hidden');
                },
                
                onItemRender: (item, elemento, index) => {
                    // Adiciona o atributo data-status no span de status para aplicar CSS correto
                    const spanStatus = elemento.querySelector('.turma-status');
                    if (spanStatus && item.status_valor) {
                        spanStatus.setAttribute('data-status', item.status_valor.toUpperCase());
                    }
                }
            }
        });

        // Carregar dados iniciais
        datagrid.carregar();

        // Gerenciar estado ativo dos botões de status
        document.querySelectorAll('.filter-btn-turma').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active de todos
                document.querySelectorAll('.filter-btn-turma').forEach(b => {
                    b.classList.remove('active');
                });
                
                // Adiciona active no clicado
                this.classList.add('active');
                
                // Atualiza o input hidden
                const status = this.getAttribute('data-tab-status') || '';
                document.getElementById('status-input').value = status;
                
                // Recarrega os dados
                datagrid.recarregar();
            });
        });

        // Colocar um evento para quando recarregar os dados
        containerTurmas.addEventListener('datagridDadosRecarregados', function() {
            console.log('Dados do datagrid recarregados com sucesso.');
        });

        // Botão Limpar Filtros
        const btnLimparFiltros = document.getElementById('btn-limpar-filtros-turmas');
        if (btnLimparFiltros) {
            btnLimparFiltros.addEventListener('click', function() {
                // Limpar status
                document.getElementById('status-input').value = '';
                document.querySelectorAll('.filter-btn-turma').forEach(btn => {
                    btn.classList.remove('active');
                });
                document.querySelector('.filter-btn-turma[data-tab-status=""]').classList.add('active');
                
                // Limpar período
                document.getElementById('periodo-input').value = '';
                
                // Limpar turno
                document.getElementById('turno-input').value = '';
                
                // Limpar modalidade
                document.getElementById('modalidade-input').value = '';
                
                // Limpar busca
                document.getElementById('busca-turma').value = '';
                
                // Recarregar
                datagrid.recarregar();
            });
        }

        // Verifica se existe o template do modal de adicionar turma
        if (templateModalAdicionarTurma) {
            // Inclui o modal no corpo do documento
            const cloneModalAdicionarTurma = templateModalAdicionarTurma.content.cloneNode(true);
            document.body.appendChild(cloneModalAdicionarTurma);
            
            // Deleta o template para evitar duplicacao
            templateModalAdicionarTurma.remove();
        } else {
            console.error("Template do modal de adicionar turma não encontrado.");
        }

        // Verifica se existe o template do modal de editar turma
        if (templateModalEditarTurma) {

            // Inclui o modal no corpo do documento
            const cloneModalEditarTurma = templateModalEditarTurma.content.cloneNode(true);
            document.body.appendChild(cloneModalEditarTurma);

            // Criacao do modal
            var modalEditarTurma = new Modal('#turma-modal-editar');

            // Evento de fechamento do modal ao clicar no botao de fechar (ou cancelar)
            document.getElementById('turma-modal-editar').addEventListener('fechar', function() {
                modalEditarTurma.limparCampos();
                modalEditarTurma.fechar();
            });

            // Inicializacao do formulario de editar turma
            var formularioEditarTurma = new Formulario('#turma-form-editar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalEditarTurma.fechar();
                    datagrid.recarregar();
                    notificador.sucesso(`Turma ${response.data.codigo || ''} editada com sucesso!`, null, { alvo: '#main-turmas'});
                },
                notificador: {
                    status: true,
                    alvo: '#turma-form-editar'
                }
            });

            // Deleta o template para evitar duplicacao
            templateModalEditarTurma.remove();

        } else {
            console.error("Template do modal de editar turma não encontrado.");
        }

        // Verifica se existe o template do modal de arquivar turma
        if (templateModalArquivarTurma) {
            // Inclui o modal no corpo do documento
            const cloneModalArquivarTurma = templateModalArquivarTurma.content.cloneNode(true);
            document.body.appendChild(cloneModalArquivarTurma);

            // Criacao do modal
            var modalArquivarTurma = new Modal('#turma-modal-arquivar');

            // Evento de fechamento do modal ao clicar no botao de fechar (ou cancelar)
            document.getElementById('turma-modal-arquivar').addEventListener('fechar', function() {
                modalArquivarTurma.limparCampos();
                modalArquivarTurma.fechar();
            });

            // Inicializacao do formulario de arquivar turma
            var formularioArquivarTurma = new Formulario('#turma-form-arquivar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalArquivarTurma.fechar();
                    datagrid.recarregar();
                    notificador.sucesso(`Turma ${response.data.codigo || ''} arquivada com sucesso!`, null, { alvo: '#main-turmas' });
                },
                notificador: {
                    status: true,
                    alvo: '#turma-form-arquivar'
                }
            });

            // Deleta o template para evitar duplicacao
            templateModalArquivarTurma.remove();

        } else {
            console.error("Template do modal de arquivar turma não encontrado.");
        }

        // Modal Confirmar Turma
        if (templateModalConfirmarTurma) {
            const cloneModalConfirmarTurma = templateModalConfirmarTurma.content.cloneNode(true);
            document.body.appendChild(cloneModalConfirmarTurma);

            var modalConfirmarTurma = new Modal('#turma-modal-confirmar');

            document.getElementById('turma-modal-confirmar').addEventListener('fechar', function() {
                modalConfirmarTurma.limparCampos();
                modalConfirmarTurma.fechar();
            });

            var formularioConfirmarTurma = new Formulario('#turma-form-confirmar', {
                onSuccess: function(response) {
                    modalConfirmarTurma.fechar();
                    datagrid.recarregar();
                    notificador.sucesso('Turma confirmada com sucesso!', null, { alvo: '#main-turmas' });
                },
                notificador: {
                    status: true,
                    alvo: '#turma-form-confirmar'
                }
            });

            templateModalConfirmarTurma.remove();
        }

        // Modal Finalizar Turma
        if (templateModalFinalizarTurma) {
            const cloneModalFinalizarTurma = templateModalFinalizarTurma.content.cloneNode(true);
            document.body.appendChild(cloneModalFinalizarTurma);

            var modalFinalizarTurma = new Modal('#turma-modal-finalizar');

            document.getElementById('turma-modal-finalizar').addEventListener('fechar', function() {
                modalFinalizarTurma.limparCampos();
                modalFinalizarTurma.fechar();
            });

            var formularioFinalizarTurma = new Formulario('#turma-form-finalizar', {
                onSuccess: function(response) {
                    modalFinalizarTurma.fechar();
                    datagrid.recarregar();
                    notificador.sucesso('Turma finalizada com sucesso!', null, { alvo: '#main-turmas' });
                },
                notificador: {
                    status: true,
                    alvo: '#turma-form-finalizar'
                }
            });

            templateModalFinalizarTurma.remove();
        }

        // Modal Liberar Turma
        if (templateModalLiberarTurma) {
            const cloneModalLiberarTurma = templateModalLiberarTurma.content.cloneNode(true);
            document.body.appendChild(cloneModalLiberarTurma);

            var modalLiberarTurma = new Modal('#turma-modal-liberar');

            document.getElementById('turma-modal-liberar').addEventListener('fechar', function() {
                modalLiberarTurma.limparCampos();
                modalLiberarTurma.fechar();
            });

            var formularioLiberarTurma = new Formulario('#turma-form-liberar', {
                onSuccess: function(response) {
                    modalLiberarTurma.fechar();
                    datagrid.recarregar();
                    notificador.sucesso('Turma liberada com sucesso!', null, { alvo: '#main-turmas' });
                },
                notificador: {
                    status: true,
                    alvo: '#turma-form-liberar'
                }
            });

            templateModalLiberarTurma.remove();
        }


        /** ======================
         * EVENTOS
         * ====================== */

        if (containerTurmas) {

            containerTurmas.addEventListener('click', async function(event) {

                // VISUALIZAR
                const buttonVisualizar = event.target.closest('a[data-action="visualizar"]');
                if (buttonVisualizar) {
                    // Ja sendo feito diretamente pelo link
                    return;
                }

                // EDITAR
                const buttonEditar = event.target.closest('a[data-action="editar"], button[data-action="editar"]');
                if (buttonEditar) {
                    event.preventDefault();
                    const itemTurma = buttonEditar.closest('.turma-item');
                    if (!itemTurma) return;
                    const turmaId = itemTurma.getAttribute('data-id');

                    try {
                        // Carregar dados completos da turma do servidor
                        const response = await fetch(`/turmas/${turmaId}/dados`);
                        const resultado = await response.json();
                        
                        if (resultado.status !== 'sucesso') {
                            throw new Error(resultado.mensagem || 'Erro ao carregar dados da turma.');
                        }

                        const turma = resultado.data;
                        const formTurmaModalEditar = document.querySelector('#turma-form-editar');

                        // Preenche os campos do formulario com os dados da turma
                        formTurmaModalEditar.attributes['action'].value = `/turmas/${turma.id}/editar`;
                        formTurmaModalEditar.querySelector("input[name='id']").value = turma.id;
                        formTurmaModalEditar.querySelector("input[name='codigo']").value = turma.codigo;
                        formTurmaModalEditar.querySelector("select[name='professor_id']").value = turma.professor_id || '';
                        formTurmaModalEditar.querySelector("input[name='capacidade_maxima']").value = turma.capacidade_maxima || '';
                        formTurmaModalEditar.querySelector("select[name='turno']").value = turma.turno_valor || '';
                        formTurmaModalEditar.querySelector("select[name='modalidade']").value = turma.modalidade_valor || '';

                        datagrid.fecharDropdowns();

                        if (!modalEditarTurma) return;
                        modalEditarTurma.abrir();

                    } catch (e) {
                        console.error(e);
                        if (typeof notificador !== 'undefined') {
                            notificador.erro('Erro ao carregar os dados da turma para edição.', null, { alvo: '#main-turmas' });
                        }
                    }
                    return;
                }

                // ARQUIVAR
                const buttonArquivar = event.target.closest('a[data-action="arquivar"], button[data-action="arquivar"]');
                if (buttonArquivar) {
                    event.preventDefault();
                    const itemTurma = buttonArquivar.closest('.turma-item');
                    if (!itemTurma) return;
                    const turmaId = itemTurma.getAttribute('data-id');
                    if (!modalArquivarTurma) return;

                    const formTurmaModalArquivar = document.querySelector('#turma-form-arquivar');

                    let turma = datagrid.obterDados().find(t => t.id == turmaId);
                    
                    formTurmaModalArquivar.attributes['action'].value = `/turmas/${turma.id}/arquivar`;
                    formTurmaModalArquivar.querySelector('input[name="id"]').value = turma.id;
                    document.querySelector('#turma-codigo-arquivar').textContent = turma.codigo;

                    datagrid.fecharDropdowns();
                    modalArquivarTurma.abrir();
                    return;
                }

                // CONFIRMAR
                const buttonConfirmar = event.target.closest('a[data-action="confirmar"], button[data-action="confirmar"]');
                if (buttonConfirmar) {
                    event.preventDefault();
                    const itemTurma = buttonConfirmar.closest('.turma-item');
                    if (!itemTurma) return;
                    const turmaId = itemTurma.getAttribute('data-id');
                    if (!modalConfirmarTurma) return;

                    const formTurmaModalConfirmar = document.querySelector('#turma-form-confirmar');

                    let turma = datagrid.obterDados().find(t => t.id == turmaId);
                    
                    formTurmaModalConfirmar.attributes['action'].value = `/turmas/${turma.id}/confirmar`;
                    formTurmaModalConfirmar.querySelector('input[name="id"]').value = turma.id;
                    document.querySelector('#turma-codigo-confirmar').textContent = turma.codigo;

                    datagrid.fecharDropdowns();
                    modalConfirmarTurma.abrir();
                    return;
                }

                // FINALIZAR
                const buttonFinalizar = event.target.closest('a[data-action="finalizar"], button[data-action="finalizar"]');
                if (buttonFinalizar) {
                    event.preventDefault();
                    const itemTurma = buttonFinalizar.closest('.turma-item');
                    if (!itemTurma) return;
                    const turmaId = itemTurma.getAttribute('data-id');
                    if (!modalFinalizarTurma) return;

                    const formTurmaModalFinalizar = document.querySelector('#turma-form-finalizar');

                    let turma = datagrid.obterDados().find(t => t.id == turmaId);
                    
                    formTurmaModalFinalizar.attributes['action'].value = `/turmas/${turma.id}/finalizar`;
                    formTurmaModalFinalizar.querySelector('input[name="id"]').value = turma.id;
                    document.querySelector('#turma-codigo-finalizar').textContent = turma.codigo;

                    datagrid.fecharDropdowns();
                    modalFinalizarTurma.abrir();
                    return;
                }

                // LIBERAR
                const buttonLiberar = event.target.closest('a[data-action="liberar"], button[data-action="liberar"]');
                if (buttonLiberar) {
                    event.preventDefault();
                    const itemTurma = buttonLiberar.closest('.turma-item');
                    if (!itemTurma) return;
                    const turmaId = itemTurma.getAttribute('data-id');
                    if (!modalLiberarTurma) return;

                    const formTurmaModalLiberar = document.querySelector('#turma-form-liberar');

                    let turma = datagrid.obterDados().find(t => t.id == turmaId);
                    
                    formTurmaModalLiberar.attributes['action'].value = `/turmas/${turma.id}/liberar`;
                    formTurmaModalLiberar.querySelector('input[name="id"]').value = turma.id;
                    document.querySelector('#turma-codigo-liberar').textContent = turma.codigo;

                    datagrid.fecharDropdowns();
                    modalLiberarTurma.abrir();
                    return;
                }
            });
        }

        // =============================
        // MODAL2: ADICIONAR TURMA
        // =============================
        
        const modal2AdicionarTurma = new Modal2('#turma-modal-adicionar', {
            totalSteps: 2,
            onStepChange: function(step) {
                console.log('[Modal2] Mudança para etapa:', step);
                
                if (step === 2) {
                    // Ao entrar na etapa 2, carregar horários baseado no turno selecionado
                    const turno = document.getElementById('turma-turno').value;
                    if (turno) {
                        carregarGradeHorarios(turno);
                    }
                }
            },
            onValidateStep: function(step) {
                if (step === 1) {
                    // Validar etapa 1
                    const curso = document.getElementById('turma-curso').value;
                    const disciplina = document.getElementById('turma-disciplina').value;
                    const codigo = document.getElementById('turma-codigo').value;
                    const periodo = document.getElementById('turma-periodo').value;
                    const professor = document.getElementById('turma-professor').value;
                    const turno = document.getElementById('turma-turno').value;
                    const capacidade = document.getElementById('turma-capacidade').value;
                    const modalidade = document.getElementById('turma-modalidade').value;

                    if (!curso || !disciplina || !codigo || !periodo || !professor || !turno || !capacidade || !modalidade) {
                        notificador.aviso('Preencha todos os campos obrigatórios.');
                        return false;
                    }

                    // Atualizar label do turno selecionado
                    const turnoText = document.getElementById('turma-turno').selectedOptions[0].text;
                    document.querySelector('[data-turno-selecionado]').textContent = turnoText;
                }
                return true;
            }
        });

        // Buscar disciplinas ao selecionar curso
        document.getElementById('turma-curso')?.addEventListener('change', async function() {
            const cursoId = this.value;
            const selectDisciplina = document.getElementById('turma-disciplina');
            
            if (!cursoId) {
                selectDisciplina.disabled = true;
                selectDisciplina.innerHTML = '<option value="" disabled selected>Selecione primeiro o curso</option>';
                return;
            }

            // Mostrar loading
            selectDisciplina.disabled = true;
            selectDisciplina.innerHTML = '<option value="" disabled selected>Carregando...</option>';

            try {
                const response = await fetch(`/turmas/disciplinas?curso_id=${cursoId}`);
                const data = await response.json();

                if (data.status === 'sucesso') {
                    if (data.data.length === 0) {
                        selectDisciplina.innerHTML = '<option value="" disabled selected>Nenhuma disciplina disponível</option>';
                    } else {
                        let options = '<option value="" disabled selected>Selecione a disciplina</option>';
                        data.data.forEach(disc => {
                            options += '<option value="' + disc.id + '">' + disc.nome + ' (' + disc.sigla + ') - ' + disc.periodo + 'º período</option>';
                        });
                        selectDisciplina.innerHTML = options;
                        selectDisciplina.disabled = false;
                    }
                } else {
                    notificador.erro(data.mensagem || 'Erro ao carregar disciplinas.');
                    selectDisciplina.innerHTML = '<option value="" disabled selected>Erro ao carregar</option>';
                }
            } catch (error) {
                console.error('[Turmas] Erro ao buscar disciplinas:', error);
                notificador.erro('Erro ao carregar disciplinas. Tente novamente.');
                selectDisciplina.innerHTML = '<option value="" disabled selected>Erro ao carregar</option>';
            }
        });

        // Função para carregar grade de horários
        async function carregarGradeHorarios(turno) {
            const tbody = document.getElementById('grade-horarios-body');
            
            if (!turno) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-gray-500">Selecione o turno na etapa anterior</td></tr>';
                return;
            }

            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><span class="material-icons-sharp animate-spin">autorenew</span></td></tr>';

            try {
                const response = await fetch(`/turmas/horarios?turno=${turno}`);
                const data = await response.json();

                if (data.status === 'sucesso' && data.data.length > 0) {
                    tbody.innerHTML = '';
                    
                    data.data.forEach((horario, index) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50';
                        
                        // Coluna de horário
                        const tdHorario = document.createElement('td');
                        tdHorario.className = 'px-3 py-2 text-xs font-medium text-gray-700';
                        tdHorario.textContent = `${horario.inicio} - ${horario.fim}`;
                        tr.appendChild(tdHorario);

                        // Colunas para cada dia da semana
                        ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'].forEach(dia => {
                            const td = document.createElement('td');
                            td.className = 'px-3 py-2 text-center';
                            
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.className = 'form-checkbox h-5 w-5 text-blue-600 rounded cursor-pointer';
                            checkbox.dataset.dia = dia;
                            checkbox.dataset.horarioIndex = index;
                            checkbox.dataset.inicio = horario.inicio;
                            checkbox.dataset.fim = horario.fim;
                            
                            checkbox.addEventListener('change', atualizarHorariosSelecionados);
                            
                            td.appendChild(checkbox);
                            tr.appendChild(td);
                        });

                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-gray-500">Nenhum horário disponível para este turno</td></tr>';
                }
            } catch (error) {
                console.error('[Turmas] Erro ao buscar horários:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-red-500">Erro ao carregar horários</td></tr>';
            }
        }

        // Função para atualizar campo hidden com horários selecionados
        function atualizarHorariosSelecionados() {
            const checkboxes = document.querySelectorAll('#grade-horarios-body input[type="checkbox"]:checked');
            const horarios = [];

            checkboxes.forEach(cb => {
                horarios.push({
                    dia: cb.dataset.dia,
                    inicio: cb.dataset.inicio,
                    fim: cb.dataset.fim
                });
            });

            document.getElementById('horarios-selecionados').value = JSON.stringify(horarios);
            console.log('[Turmas] Horários selecionados:', horarios);
        }

        // Configurar formulário de adicionar turma
        const formAdicionarTurma = new Formulario('#turma-form-adicionar', {
            onSuccess: function(response) {
                notificador.sucesso('Turma adicionada com sucesso!', null, { alvo: '#main-turmas'});
                datagrid.recarregar();
                formAdicionarTurma.limpar();
                modal2AdicionarTurma.fechar();
                
                // Resetar grade de horários
                document.getElementById('grade-horarios-body').innerHTML = '';
                document.getElementById('horarios-selecionados').value = '[]';
            },
            onError: function(response) {
                notificador.erro(response.mensagem || 'Erro ao adicionar turma. Tente novamente.');
            }
        });
    });

</script>


