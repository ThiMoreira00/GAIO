<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Gestão de Turmas</h1>
    <div class="flex-shrink-0">
        <button type="button" class="button-primary inline-flex items-center gap-2" data-modal2-trigger="turma-modal-adicionar" id="button-turma-adicionar">
            <span class="material-icons-sharp">add</span>
            Adicionar nova turma
        </button>
    </div>
</header>

<main id="main-turmas-admin" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="turmas-admin-section-heading">
        <h2 id="turmas-admin-section-heading" class="sr-only">Gestão de Turmas - Administrador</h2>
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
            </div>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/../../templates/turma-lista-item.php';
    include __DIR__ . '/../../templates/turma-modal-adicionar.php';
    include __DIR__ . '/../../templates/turma-modal-editar.php';
    include __DIR__ . '/../../templates/turma-modal-arquivar.php';
    include __DIR__ . '/../../templates/turma-modal-confirmar.php';
    include __DIR__ . '/../../templates/turma-modal-finalizar.php';
    include __DIR__ . '/../../templates/turma-modal-liberar.php';
    include __DIR__ . '/../../templates/turma-modal-adicionar-alunos.php';
    include __DIR__ . '/../../templates/turma-modal-remover-aluno.php';
?>

<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/datagrid.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal2.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/turmas-admin.js') ?>"></script>
