<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Lista de Cursos</h1>
    <div class="flex-shrink-0">
        <button type="button" class="button-primary inline-flex items-center" data-modal-trigger="modal-curso-adicionar-form" id="button-curso-adicionar">
            <span class="material-icons-sharp -ml-1 mr-2">add</span>
            Adicionar novo curso
        </button>
    </div>
</header>

<main id="main-cursos" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="cursos-section-heading">
        <h2 id="cursos-section-heading" class="sr-only">Cursos cadastrados</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-curso-filtros">
                    <form id="form-filtros-cursos" action="/cursos/filtrar" method="GET" data-tab-form>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-shrink-0">
                                <input type="hidden" name="grau_id" id="grau-input" value="" data-tab-status-input>
                                <div class="bg-gray-100 p-1 rounded-lg sm:rounded-full flex items-center justify-center flex-wrap gap-1">
                                    <button type="button" class="filter-btn-curso tab-item active" data-tab-status="" data-grau="" data-tab-button>Todos</button>
                                    <?php foreach ($graus as $grau): ?>
                                        <button type="button" data-tab-status="<?= strtolower($grau->nome); ?>" class="filter-btn-curso tab-item" data-tab-button><?= $grau->nome; ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="relative w-full sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-curso" class="input-search" placeholder="Buscar cursos..." data-tab-search>
                            </div>
                        </div>
                    </form>
                </section>

                <div id="container-cursos" class="space-y-4">
                </div>

                <div id="loader-cursos" class="text-center mt-8 py-4" style="display: none;">
                    <span class="material-icons-sharp text-5xl text-gray-400 animate-spin">sync</span>
                </div>
            </div>
        </div>
    </section>
</main>

<?php

    include __DIR__ . '/../templates/curso-lista-item.php';
    include __DIR__ . '/../templates/curso-modal-adicionar.php';
    include __DIR__ . '/../templates/curso-modal-editar.php';
    include __DIR__ . '/../templates/curso-modal-arquivar.php';
?>


<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/tab.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Botões
        const buttonAdicionarCurso = document.getElementById('button-curso-adicionar');

        // Templates
        const templateModalAdicionarCurso = document.getElementById('template-curso-modal-adicionar');
        const templateModalEditarCurso = document.getElementById('template-curso-modal-editar');
        const templateModalArquivarCurso = document.getElementById('template-curso-modal-arquivar');

        const templateListaItemCurso = document.getElementById('template-lista-item-curso');

        const containerCursos = document.getElementById('main-cursos');

        // Inicialização do Tab
        const tab = new Tab('#main-cursos', {
            prefixo: 'curso',
            seletorConteudo: '#container-cursos',
            seletorTemplateRegistros: '#template-lista-item-curso',
            url: '/cursos/filtrar',
            metodo: 'GET'
        });

        // Colocar um evento para quando recarregar os dados do tab
        containerCursos.addEventListener('tabDadosRecarregados', function() {
            // Aqui você pode colocar o código que deseja executar após os dados serem recarregados
            console.log('Dados do tab recarregados com sucesso.');
        });

        // Verifica se existe o template do modal de adicionar curso
        if (templateModalAdicionarCurso) {

            // Inclui o modal no corpo do documento
            const cloneModalAdicionarCurso = templateModalAdicionarCurso.content.cloneNode(true);
            document.body.appendChild(cloneModalAdicionarCurso);

            // Criação do modal
            var modalAdicionarCurso = new Modal('#curso-modal-adicionar');

            // Evento de abertura do modal ao clicar no botão
            buttonAdicionarCurso.addEventListener('click', function() {
                modalAdicionarCurso.abrir();
            });

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('curso-modal-adicionar').addEventListener('fechar', function() {
                modalAdicionarCurso.limparCampos();
                modalAdicionarCurso.fechar();
            });

            // Inicialização do formulário de adicionar curso
            var formularioAdicionarCurso = new Formulario('#curso-form-adicionar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalAdicionarCurso.limparCampos();
                    modalAdicionarCurso.fechar();
                    tab.recarregar();
                    notificador.sucesso('Curso adicionado com sucesso!', null, { alvo: '#main-cursos' });
                },
                notificador: {
                    status: true,
                    alvo: '#curso-form-adicionar'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalAdicionarCurso.remove();
            
        } else {
            console.error("Template do modal de adicionar curso não encontrado.");
        }


        // Verifica se existe o template do modal de editar curso
        if (templateModalEditarCurso) {

            // Inclui o modal no corpo do documento
            const cloneModalEditarCurso = templateModalEditarCurso.content.cloneNode(true);
            document.body.appendChild(cloneModalEditarCurso);

            // Criação do modal
            var modalEditarCurso = new Modal('#curso-modal-editar');

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('curso-modal-editar').addEventListener('fechar', function() {
                modalEditarCurso.limparCampos();
                modalEditarCurso.fechar();
            });

            // Inicialização do formulário de editar curso
            var formularioEditarCurso = new Formulario('#curso-form-editar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalEditarCurso.fechar();
                    tab.recarregar();
                    notificador.sucesso(`Curso ${response.data.nome || ''} editado com sucesso!`, null, { alvo: '#main-cursos'});
                },
                notificador: {
                    status: true,
                    alvo: '#curso-form-editar'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalEditarCurso.remove();

        } else {
            console.error("Template do modal de editar curso não encontrado.");
        }

        // Verifica se existe o template do modal de arquivar curso
        if (templateModalArquivarCurso) {
            // Inclui o modal no corpo do documento
            const cloneModalArquivarCurso = templateModalArquivarCurso.content.cloneNode(true);
            document.body.appendChild(cloneModalArquivarCurso);

            // Criação do modal
            var modalArquivarCurso = new Modal('#curso-modal-arquivar');

            // Evento de fechamento do modal ao clicar no botão de fechar (ou cancelar)
            document.getElementById('curso-modal-arquivar').addEventListener('fechar', function() {
                modalArquivarCurso.limparCampos();
                modalArquivarCurso.fechar();
            });

            // Inicialização do formulário de arquivar curso
            var formularioArquivarCurso = new Formulario('#curso-form-arquivar', {
                onBeforeSubmit: function() {
                    // TODO: Verificar os campos antes de enviar
                },
                onSuccess: function(response) {
                    modalArquivarCurso.fechar();
                    tab.recarregar();
                    notificador.sucesso(`Curso ${response.data.nome || ''} arquivado com sucesso!`, null, { alvo: '#main-cursos' });
                },
                notificador: {
                    status: true,
                    alvo: '#curso-form-arquivar'
                }
            });

            // Deleta o template para evitar duplicação
            templateModalArquivarCurso.remove();

            

        } else {
            console.error("Template do modal de arquivar curso não encontrado.");
        }


        /** ======================
         * EVENTOS
         * ====================== */

        // Arquivar

        if (containerCursos) {

            containerCursos.addEventListener('click', async function(event) {

                // VISUALIZAR
                // Já está sendo feito diretamente pela classe Tab

                // EDITAR
                const buttonEditar = event.target.closest('a[data-action="editar"], button[data-action="editar"]');
                if (buttonEditar) {
                    event.preventDefault();
                    const itemCurso = buttonEditar.closest('.curso-item');
                    if (!itemCurso) return;
                    const cursoId = itemCurso.getAttribute('data-id');

                    try {
                        let cursos = tab.obterDados();
                        let curso = cursos.find(c => c.id == cursoId);
                        
                        if (!curso) throw new Error('Curso não encontrado nos dados carregados.');

                        const formCursoModalEditar = document.querySelector('#curso-form-editar');

                        // Preenche os campos do formulário com os dados do curso
                        formCursoModalEditar.attributes['action'].value = `/cursos/${curso.id}/editar`;
                        formCursoModalEditar.querySelector("input[name='id']").value = curso.id;
                        formCursoModalEditar.querySelector("input[name='nome']").value = curso.nome;
                        formCursoModalEditar.querySelector("input[name='sigla']").value = curso.sigla || '';
                        formCursoModalEditar.querySelector("input[name='emec-codigo']").value = curso.emec_codigo || '';
                        formCursoModalEditar.querySelector("select[name='grau']").value = curso.grau_id || '';
                        formCursoModalEditar.querySelector("input[name='duracao-minima']").value = curso.duracao_minima || '';
                        formCursoModalEditar.querySelector("input[name='duracao-maxima']").value = curso.duracao_maxima || '';

                        tab.fecharDropdowns();

                        if (!modalEditarCurso) return;
                        modalEditarCurso.abrir();

                    } catch (e) {
                        console.error(e);
                        if (typeof notificador !== 'undefined') {
                            notificador.erro('Erro ao carregar os dados do curso para edição.', null, { alvo: '#main-cursos' });
                        }
                    }
                    return;
                }

                // ARQUIVAR
                const buttonArquivar = event.target.closest('a[data-action="arquivar"], button[data-action="arquivar"]');
                if (buttonArquivar) {

                    event.preventDefault();
                    const itemCurso = buttonArquivar.closest('.curso-item');
                    if (!itemCurso) return;
                    const cursoId = itemCurso.getAttribute('data-id');
                    if (!modalArquivarCurso) return;

                    const formCursoModalArquivar = document.querySelector('#curso-form-arquivar');

                    let curso = tab.obterDados().find(c => c.id == cursoId);
                    
                    
                    formCursoModalArquivar.attributes['action'].value = `/cursos/${curso.id}/arquivar`;
                    formCursoModalArquivar.querySelector('input[name="id"]').value = curso.id;
                    document.querySelector('#curso-nome-arquivar').textContent = curso.nome;

                    tab.fecharDropdowns();
                    modalArquivarCurso.abrir();
                    return;
                }
            });
        }
    });

</script>