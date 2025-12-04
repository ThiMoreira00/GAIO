<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Períodos Letivos</h1>
    <div class="flex-shrink-0">
        <button type="button" class="button-primary inline-flex items-center gap-2" data-modal-trigger="modal-periodos-letivos-adicionar-form" id="button-periodo-letivo-adicionar">
            <span class="material-icons-sharp">add</span>
            Adicionar novo período letivo
        </button>
    </div>
</header>

<main id="main-periodos-letivos" class="tab">
    <section class="bg-white p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="periodos-letivos-section-heading">
        <h2 id="periodos-letivos-section-heading" class="sr-only">Períodos Letivos</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8 tab" id="tab-periodos-letivos-filtros">
                    <form id="form-filtros-periodos-letivos" action="/periodos-letivos/filtrar" method="GET" data-tab-form>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-shrink-0">
                                <input type="hidden" name="status" id="status-input" value="" data-tab-status-input>
                                <div class="bg-gray-100 p-1 rounded-lg sm:rounded-full flex items-center justify-center flex-wrap gap-1">
                                    <button type="button" data-tab-status="" class="filter-btn-periodo-letivo tab-item active" data-tab-button>Todos</button>
                                    <?php foreach ($status_periodos as $status): ?>
                                        <button type="button" data-tab-status="<?= $status->name; ?>" class="filter-btn-periodo-letivo tab-item" data-tab-button><?= ucfirst($status->value); ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="relative w-full sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-periodo-letivo" class="input-search" placeholder="Buscar períodos letivos..." data-tab-search>
                            </div>
                        </div>
                    </form>
                </section>

                <div id="container-periodos-letivos" class="space-y-4" data-tab-results>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/../templates/periodo-letivo-lista-item.php';
    include __DIR__ . '/../templates/periodo-letivo-modal-adicionar.php';
?>

<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/tab.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Botões
        const buttonAdicionarPeriodo = document.getElementById('button-periodo-letivo-adicionar');

        // Templates
        const templateModalAdicionarPeriodo = document.getElementById('template-periodo-letivo-modal-adicionar');
        const templateListaItemPeriodoLetivo = document.getElementById('template-lista-item-periodo-letivo');

        // Inicialização do Tab
        const tab = new Tab('#tab-periodos-letivos-filtros', {
            prefixo: 'periodo-letivo',
            seletorConteudo: '#container-periodos-letivos',
            seletorTemplateRegistros: '#template-lista-item-periodo-letivo',
            url: '/periodos-letivos/filtrar',
            metodo: 'GET',
            resultadosPorPagina: 15,
            loading: true
        });

        // Verifica se existe o template do modal de adicionar período letivo
        if (templateModalAdicionarPeriodo) {

            // Inclui o modal no corpo do documento
            const cloneModalAdicionarPeriodo = templateModalAdicionarPeriodo.content.cloneNode(true);
            document.body.appendChild(cloneModalAdicionarPeriodo);

            // Criação do modal
            const modalAdicionarPeriodo = new Modal('#periodo-letivo-modal-adicionar-form');

            // Evento de abertura do modal ao clicar no botão
            buttonAdicionarPeriodo.addEventListener('click', function() {
                modalAdicionarPeriodo.abrir();
            });

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('periodo-letivo-modal-adicionar-form').addEventListener('fechar', function() {
                modalAdicionarPeriodo.limparCampos();
                modalAdicionarPeriodo.fechar();
            });

            const formularioAdicionarPeriodo = new Formulario({
                formId: 'form-adicionar-periodo-letivo',
                beforeSubmit: function() {
                    // TODO: Verifica os campos
                },
                onSuccess: (response) => {
                    modalAdicionarPeriodo.fechar();
                    notificador.sucesso('Período letivo adicionado com sucesso!', null, { alvo: '#main-periodos-letivos' });
                    formularioAdicionarPeriodo.limparCampos();
                    tab.recarregar();
                },
                notificador: {
                    status: true,
                    alvo: '#form-adicionar-periodo-letivo'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalAdicionarPeriodo.remove();
            
        } else {
            console.error("Template do modal de adicionar período letivo não encontrado.");
        }

        

    });
</script>