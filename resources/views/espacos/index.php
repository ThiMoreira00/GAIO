<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Lista de Espaços</h1>
    <div class="flex-shrink-0">
        <button type="button" class="button-primary inline-flex items-center" data-modal-trigger="modal-espaco-adicionar-form" id="button-espaco-adicionar">
            <span class="material-icons-sharp">add</span>
            Adicionar novo espaço
        </button>
    </div>
</header>

<main id="main-espacos" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="espacos-section-heading">
        <h2 id="espacos-section-heading" class="sr-only">Espaços cadastrados</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-espaco-filtros">
                    <form id="form-filtros-espacos" action="/espacos/filtrar" method="GET" data-tab-form>
                        <div class="flex flex-col sm:flex-row items-center flex-wrap gap-4">
                            <div class="flex-shrink-1">
                                <input type="hidden" name="tipo_id" id="tipo-input" value="" data-tab-status-input>
                                <div class="bg-gray-100 p-1 rounded-lg sm:rounded-lg flex items-center justify-center flex-wrap gap-1">
                                    <button type="button" class="filter-btn-espaco tab-item active" data-tab-status="" data-tipo="" data-tab-button>Todos</button>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <button type="button" data-tab-status="<?= strtoupper($tipo->name); ?>" class="filter-btn-espaco tab-item" data-tab-button><?= $tipo->value; ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="relative sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-espaco" class="input-search" placeholder="Buscar espaços..." data-tab-search>
                            </div>
                        </div>
                    </form>
                </section>

                <div id="container-espacos" class="space-y-4">
                </div>

                <div id="loader-espacos" class="text-center mt-8 py-4" style="display: none;">
                    <span class="material-icons-sharp text-5xl text-gray-400 animate-spin">sync</span>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/../templates/espaco-lista-item.php';
    include __DIR__ . '/../templates/espaco-modal-adicionar.php';
    include __DIR__ . '/../templates/espaco-modal-editar.php';
    include __DIR__ . '/../templates/espaco-modal-arquivar.php';
    include __DIR__ . '/../templates/espaco-modal-reativar.php';
    include __DIR__ . '/../templates/espaco-modal-excluir.php';
?>


<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/tab.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Botões
        const buttonAdicionarEspaco = document.getElementById('button-espaco-adicionar');

        // Templates
        const templateModalAdicionarEspaco = document.getElementById('template-espaco-modal-adicionar');
        const templateModalEditarEspaco = document.getElementById('template-espaco-modal-editar');
        const templateModalArquivarEspaco = document.getElementById('template-espaco-modal-arquivar');
        const templateModalReativarEspaco = document.getElementById('template-espaco-modal-reativar');
        const templateModalExcluirEspaco = document.getElementById('template-espaco-modal-excluir');

        const templateListaItemEspaco = document.getElementById('template-lista-item-espaco');

        const containerEspacos = document.getElementById('main-espacos');

        // Inicialização do Tab
        const tab = new Tab('#main-espacos', {
            prefixo: 'espaco',
            seletorConteudo: '#container-espacos',
            seletorTemplateRegistros: '#template-lista-item-espaco',
            url: '/espacos/filtrar',
            metodo: 'GET',
            onItemRender: (dadosElemento, elemento, index) => {
                // Verificar se a capacidade do espaço é 1 (para colocar no singular)
                const capacidadeElemento = elemento.querySelector('.espaco-capacidade-maxima');
                if (capacidadeElemento) {
                    const capacidade = dadosElemento.capacidade_maxima || 0;
                    capacidadeElemento.textContent = capacidade + ' ' + (capacidade === 1 ? 'pessoa' : 'pessoas');
                }
            },
        });

        // Verifica se existe o template do modal de adicionar espaço
        if (templateModalAdicionarEspaco) {

            // Inclui o modal no corpo do documento
            const cloneModalAdicionarEspaco = templateModalAdicionarEspaco.content.cloneNode(true);
            document.body.appendChild(cloneModalAdicionarEspaco);

            // Criação do modal
            var modalAdicionarEspaco = new Modal('#espaco-modal-adicionar');

            // Evento de abertura do modal ao clicar no botão
            buttonAdicionarEspaco.addEventListener('click', function() {
                modalAdicionarEspaco.abrir();
            });

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('espaco-modal-adicionar').addEventListener('fechar', function() {
                modalAdicionarEspaco.limparCampos();
                modalAdicionarEspaco.fechar();
            });

            // Inicialização do formulário de adicionar espaço
            var formularioAdicionarEspaco = new Formulario('#espaco-form-adicionar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalAdicionarEspaco.limparCampos();
                    modalAdicionarEspaco.fechar();
                    tab.recarregar();
                    notificador.sucesso('Espaço adicionado com sucesso!', null, { alvo: '#main-espacos' });
                },
                notificador: {
                    status: true,
                    alvo: '#espaco-form-adicionar'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalAdicionarEspaco.remove();
            
        } else {
            console.error("Template do modal de adicionar espaço não encontrado.");
        }


        // Verifica se existe o template do modal de editar espaço
        if (templateModalEditarEspaco) {

            // Inclui o modal no corpo do documento
            const cloneModalEditarEspaco = templateModalEditarEspaco.content.cloneNode(true);
            document.body.appendChild(cloneModalEditarEspaco);

            // Criação do modal
            var modalEditarEspaco = new Modal('#espaco-modal-editar');

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('espaco-modal-editar').addEventListener('fechar', function() {
                modalEditarEspaco.limparCampos();
                modalEditarEspaco.fechar();
            });

            // Inicialização do formulário de editar espaço
            var formularioEditarEspaco = new Formulario('#espaco-form-editar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalEditarEspaco.fechar();
                    tab.recarregar();
                    notificador.sucesso(`Espaço ${response.data.nome || ''} editado com sucesso!`, null, { alvo: '#main-espacos' });
                },
                notificador: {
                    status: true,
                    alvo: '#espaco-form-editar'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalEditarEspaco.remove();

        } else {
            console.error("Template do modal de editar espaço não encontrado.");
        }

        // Verifica se existe o template do modal de arquivar espaço
        if (templateModalArquivarEspaco) {
            // Inclui o modal no corpo do documento
            const cloneModalArquivarEspaco = templateModalArquivarEspaco.content.cloneNode(true);
            document.body.appendChild(cloneModalArquivarEspaco);

            // Criação do modal
            var modalArquivarEspaco = new Modal('#espaco-modal-arquivar');

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('espaco-modal-arquivar').addEventListener('fechar', function() {
                modalArquivarEspaco.limparCampos();
                modalArquivarEspaco.fechar();
            });

            // Inicialização do formulário de arquivar espaço
            var formularioArquivarEspaco = new Formulario('#espaco-form-arquivar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalArquivarEspaco.fechar();
                    tab.recarregar();
                    notificador.sucesso(`Espaço ${response.data.nome || ''} arquivado com sucesso!`, null, { alvo: '#main-espacos' });
                },
                notificador: {
                    status: true,
                    alvo: '#espaco-form-arquivar'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalArquivarEspaco.remove();

            

        } else {
            console.error("Template do modal de arquivar espaço não encontrado.");
        }

        // Verifica se existe o template do modal de reativar espaço
        if (templateModalReativarEspaco) {
            // Inclui o modal no corpo do documento
            const cloneModalReativarEspaco = templateModalReativarEspaco.content.cloneNode(true);
            document.body.appendChild(cloneModalReativarEspaco);

            // Criação do modal
            var modalReativarEspaco = new Modal('#espaco-modal-reativar');

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('espaco-modal-reativar').addEventListener('fechar', function() {
                modalReativarEspaco.limparCampos();
                modalReativarEspaco.fechar();
            });

            // Inicialização do formulário de reativar espaço
            var formularioReativarEspaco = new Formulario('#espaco-form-reativar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalReativarEspaco.fechar();
                    tab.recarregar();
                    notificador.sucesso(`Espaço ${response.data.nome || ''} reativado com sucesso!`, null, { alvo: '#main-espacos' });
                },
                notificador: {
                    status: true,
                    alvo: '#espaco-form-reativar'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalReativarEspaco.remove();

        } else {
            console.error("Template do modal de reativar espaço não encontrado.");
        }

        // Verificar se existe o template do modal de excluir espaço
        if (templateModalExcluirEspaco) {
            // Inclui o modal no corpo do documento
            const cloneModalExcluirEspaco = templateModalExcluirEspaco.content.cloneNode(true);
            document.body.appendChild(cloneModalExcluirEspaco);

            // Criação do modal
            var modalExcluirEspaco = new Modal('#espaco-modal-excluir');

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('espaco-modal-excluir').addEventListener('fechar', function() {
                modalExcluirEspaco.limparCampos();
                modalExcluirEspaco.fechar();
            });

            // Inicialização do formulário de excluir espaço
            var formularioExcluirEspaco = new Formulario('#espaco-form-excluir', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalExcluirEspaco.fechar();
                    tab.recarregar();
                    notificador.sucesso(`Espaço ${response.data.nome || ''} excluído com sucesso!`, null, { alvo: '#main-espacos' });
                },
                notificador: {
                    status: true,
                    alvo: '#espaco-form-excluir'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalExcluirEspaco.remove();
        } else {
            console.error("Template do modal de excluir espaço não encontrado.");
        }


        /** ======================
         * EVENTOS
         * ====================== */

        // Arquivar

        if (containerEspacos) {

            containerEspacos.addEventListener('click', async function(event) {

                // VISUALIZAR
                // Já está sendo feito diretamente pela classe Tab

                // EDITAR
                const buttonEditar = event.target.closest('a[data-action="editar"], button[data-action="editar"]');
                if (buttonEditar) {
                    event.preventDefault();
                    const itemEspaco = buttonEditar.closest('.espaco-item');
                    if (!itemEspaco) return;
                    const espacoId = itemEspaco.getAttribute('data-id');

                    try {
                        let espacos = tab.obterDados();
                        let espaco = espacos.find(c => c.id == espacoId);

                        if (!espaco) throw new Error('Espaço não encontrado nos dados carregados.');

                        const formEspacoModalEditar = document.querySelector('#espaco-form-editar');

                        // Preenche os campos do formulário com os dados do espaço
                        formEspacoModalEditar.attributes['action'].value = `/espacos/${espaco.id}/editar`;
                        formEspacoModalEditar.querySelector("input[name='id']").value = espaco.id;
                        formEspacoModalEditar.querySelector("input[name='nome']").value = espaco.nome;
                        formEspacoModalEditar.querySelector("input[name='capacidade-maxima']").value = espaco.capacidade_maxima;
                        formEspacoModalEditar.querySelector("select[name='tipo']").value = espaco.tipo.nome;

                        tab.fecharDropdowns();

                        if (!modalEditarEspaco) return;
                        modalEditarEspaco.abrir();

                    } catch (e) {
                        console.error(e);
                        if (typeof notificador !== 'undefined') {
                            notificador.erro('Erro ao carregar os dados do espaço para edição.', null, { alvo: '#main-espacos' });
                        }
                    }
                    return;
                }

                // ARQUIVAR
                const buttonArquivar = event.target.closest('a[data-action="arquivar"], button[data-action="arquivar"]');
                if (buttonArquivar) {

                    event.preventDefault();
                    const itemEspaco = buttonArquivar.closest('.espaco-item');
                    if (!itemEspaco) return;
                    const espacoId = itemEspaco.getAttribute('data-id');
                    if (!modalArquivarEspaco) return;

                    const formEspacoModalArquivar = document.querySelector('#espaco-form-arquivar');

                    let espaco = tab.obterDados().find(c => c.id == espacoId);

                    formEspacoModalArquivar.attributes['action'].value = `/espacos/${espaco.id}/arquivar`;
                    formEspacoModalArquivar.querySelector('input[name="id"]').value = espaco.id;
                    document.querySelector('#espaco-nome-arquivar').textContent = espaco.nome;

                    tab.fecharDropdowns();
                    modalArquivarEspaco.abrir();
                    return;
                }

                // REATIVAR
                const buttonReativar = event.target.closest('a[data-action="reativar"], button[data-action="reativar"]');
                if (buttonReativar) {

                    event.preventDefault();
                    const itemEspaco = buttonReativar.closest('.espaco-item');
                    if (!itemEspaco) return;
                    const espacoId = itemEspaco.getAttribute('data-id');
                    if (!modalReativarEspaco) return;

                    const formEspacoModalReativar = document.querySelector('#espaco-form-reativar');

                    let espaco = tab.obterDados().find(c => c.id == espacoId);

                    formEspacoModalReativar.attributes['action'].value = `/espacos/${espaco.id}/reativar`;
                    formEspacoModalReativar.querySelector('input[name="id"]').value = espaco.id;
                    document.querySelector('#espaco-nome-reativar').textContent = espaco.nome;

                    tab.fecharDropdowns();
                    modalReativarEspaco.abrir();
                    return;
                }

                // EXCLUIR
                const buttonExcluir = event.target.closest('a[data-action="excluir"], button[data-action="excluir"]');
                if (buttonExcluir) {

                    event.preventDefault();
                    const itemEspaco = buttonExcluir.closest('.espaco-item');
                    if (!itemEspaco) return;
                    const espacoId = itemEspaco.getAttribute('data-id');
                    if (!modalExcluirEspaco) return;

                    const formEspacoModalExcluir = document.querySelector('#espaco-form-excluir');

                    let espaco = tab.obterDados().find(c => c.id == espacoId);

                    formEspacoModalExcluir.attributes['action'].value = `/espacos/${espaco.id}/excluir`;
                    formEspacoModalExcluir.querySelector('input[name="id"]').value = espaco.id;
                    document.querySelector('#espaco-nome-excluir').textContent = espaco.nome;

                    tab.fecharDropdowns();
                    modalExcluirEspaco.abrir();
                    return;
                }
            });
        }
    });

</script>