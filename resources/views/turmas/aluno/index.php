<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Minhas Turmas</h1>
</header>

<main id="main-turmas-aluno" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="turmas-aluno-section-heading">
        <h2 id="turmas-aluno-section-heading" class="sr-only">Turmas do Aluno</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-turmas-aluno-filtros">
                    <form id="form-filtros-turmas-aluno" action="/turmas/aluno/filtrar" method="GET" data-tab-form>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-shrink-0">
                                <input type="hidden" name="status" id="status-input" value="" data-tab-status-input>
                                <div class="bg-gray-100 p-1 rounded-lg sm:rounded-full flex items-center justify-center flex-wrap gap-1">
                                    <button type="button" class="filter-btn-status-turma tab-item active" data-tab-status="" data-tab-button>Todas</button>
                                    <button type="button" data-tab-status="ativa" class="filter-btn-status-turma tab-item" data-tab-button>Ativas</button>
                                    <button type="button" data-tab-status="concluida" class="filter-btn-status-turma tab-item" data-tab-button>Concluídas</button>
                                </div>
                            </div>
                            <div class="relative w-full sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-turmas-aluno" class="input-search" placeholder="Buscar por disciplina ou código..." data-tab-search>
                            </div>
                        </div>
                    </form>
                </section>

                <div id="container-turmas-aluno" class="space-y-4">
                </div>

                <div id="loader-turmas-aluno" class="hidden text-center mt-8 py-4">
                    <span class="material-icons-sharp text-5xl text-gray-400 animate-spin">sync</span>
                </div>

                <!-- Estado vazio -->
                <div id="estado-vazio-turmas-aluno" class="hidden text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <span class="material-icons-sharp !text-4xl text-gray-400">school</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhuma turma encontrada</h3>
                    <p class="text-gray-500 text-base mb-6">Você ainda não está matriculado em nenhuma turma.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/../templates/turma-aluno-lista-item.php';
?>

<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/tab.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Templates
        const templateListaItemTurmaAluno = document.getElementById('template-lista-item-turma-aluno');

        // Containers
        const containerTurmasAluno = document.getElementById('main-turmas-aluno');

        // Inicialização do Tab
        const tab = new Tab('#main-turmas-aluno', {
            prefixo: 'turma-aluno',
            seletorConteudo: '#container-turmas-aluno',
            seletorTemplateRegistros: '#template-lista-item-turma-aluno',
            url: '/turmas/filtrar',
            metodo: 'GET'
        });

        // Evento de clique para visualizar turma
        containerTurmasAluno.addEventListener('click', function(event) {
            const buttonVisualizar = event.target.closest('a[data-action="visualizar"]');
            if (buttonVisualizar) {
                // Navegação já feita pelo href do link
                return;
            }
        });
    });

</script>
