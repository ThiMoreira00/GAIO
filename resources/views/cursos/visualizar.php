<div id="container-curso">
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center text-white <?= ($curso->obterStatus() ?? '') === 'Arquivado' ? 'bg-gray-500' : 'bg-sky-600' ?>">
                <span class="material-icons-sharp">school</span>
            </div>
            <div>
                <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl"><?= $curso->nome ?? '[não informado]' ?></h1>
                <p class="text-gray-500 mt-1"><?= $curso->obterGrau() ?? '[não informado]' ?></p>
            </div>
        </div>
        <div class="flex-shrink-0 flex items-center gap-2">
            <?php if (($curso->obterStatus() ?? '') !== 'Arquivado'): ?>
            <button type="button" class="button-secondary" data-modal-trigger="modal-curso-editar" data-curso-id="<?= $curso->obterId() ?>">
                <span class="material-icons-sharp -ml-1 mr-2">edit</span>
                Editar
            </button>
            <button type="button" class="button-danger" data-modal-trigger="modal-arquivar-curso" data-curso-id="<?= $curso->obterId() ?>">
                <span class="material-icons-sharp -ml-1 mr-2">archive</span>
                Arquivar
            </button>
            <?php endif; ?>
        </div>
    </header>

    <div class="mt-6" id="curso-tabs">
        <div class="border-b border-gray-200">
            <li class="-mb-px flex space-x-6" aria-label="Abas">
                <button data-tab-target="#visao-geral" class="tab-button whitespace-nowrap py-4 px-8 border-b-2 font-medium text-sm border-sky-500 text-sky-600">
                    Detalhes do curso
                </button>
            </li>
        </div>

        <div class="py-8">
            <div id="visao-geral" class="tab-panel bg-white p-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Detalhes do curso</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">badge</span>
                            <div>
                                <dt class="text-gray-500">Nome do curso</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="curso-nome"><?= $curso->obterNome() ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">abc</span>
                            <div>
                                <dt class="text-gray-500">Sigla</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="curso-sigla"><?= $curso->obterSigla() ?? '[não informada]' ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">tag</span>
                            <div>
                                <dt class="text-gray-500">Código (e-MEC)</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="curso-emec-codigo"><?= $curso->obterEmecCodigo() ?? '[não informado]' ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">workspace_premium</span>
                            <div>
                                <dt class="text-gray-500">Grau acadêmico</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="curso-grau"><?= $curso->obterGrau() ?? '[não informado]' ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">hourglass_empty</span>
                            <div>
                                <dt class="text-gray-500">Duração do curso</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="curso-duracao-minima"><?= $curso->obterDuracaoMinima() . ' período' . ($curso->obterDuracaoMinima() > 1 ? 's' : '') ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">hourglass_empty</span>
                            <div>
                                <dt class="text-gray-500">Duração máxima (integralização)</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="curso-duracao-maxima"><?= $curso->obterDuracaoMaxima() . ' período' . ($curso->obterDuracaoMaxima() > 1 ? 's' : '') ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">label</span>
                            <div>
                                <dt class="text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <?php 
                                    $status = strtolower($curso->obterStatus()->value ?? '');
                                    $statusText = strtoupper($status);
                                    $statusClass = 'bg-gray-100 text-gray-800'; 
                                    if ($status === 'ativo') $statusClass = 'bg-green-100 text-green-800';
                                    if ($status === 'inativo') $statusClass = 'bg-red-100 text-red-800';  
                                    if ($status === 'arquivado') $statusClass = 'bg-orange-100 text-orange-800';
                                    ?>
                                    <span class="text-xs font-bold inline-flex items-center px-3 py-1 rounded-full <?= $statusClass ?>"><?= $statusText ?: '[não definido]' ?></span>
                                </dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    include __DIR__ . '/../templates/curso-modal-editar.php';
    include __DIR__ . '/../templates/curso-modal-arquivar.php';
?>

<script src="<?= obterURL('/assets/javascript/utils/notificador.js'); ?>"></script>
<script src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script src="<?= obterURL('/assets/javascript/cursos.js') ?>"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', async function() {
        try {
            // Obtém o ID do curso e carrega os dados uma única vez
            const buttonEditarCurso = document.querySelector('[data-modal-trigger="modal-curso-editar"]');
            const buttonArquivarCurso = document.querySelector('[data-modal-trigger="modal-arquivar-curso"]');
            
            if (!buttonEditarCurso) {
                console.error('Botão de editar curso não encontrado');
                return;
            }

            const cursoId = buttonEditarCurso.getAttribute('data-curso-id');
            
            // Carrega os dados do curso uma única vez
            const response = await obterCurso(cursoId);
            
            if (!response || !response.curso) {
                throw new Error('Não foi possível carregar os dados do curso');
            }
            
            // Extrai o objeto curso da resposta
            var curso = response.curso;

            // Container
            const containerCurso = document.getElementById('container-curso');
            
            // Templates
            const templateModalEditarCurso = document.getElementById('template-curso-modal-editar');
            const templateModalArquivarCurso = document.getElementById('template-curso-modal-arquivar');

            const templateListaItemCurso = document.getElementById('template-lista-item-curso');
            const containerCursos = document.getElementById('main-cursos');

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

                        // Atualiza os dados do curso no container
                        containerCurso.querySelector('#curso-nome').textContent = response.data.nome || '[não informado]';
                        containerCurso.querySelector('#curso-sigla').textContent = response.data.sigla || '[não informada]';
                        containerCurso.querySelector('#curso-emec-codigo').textContent = response.data.emec_codigo || '[não informado]';
                        containerCurso.querySelector('#curso-grau').textContent = response.data.grau.nome || '[não informado]';
                        containerCurso.querySelector('#curso-duracao-minima').textContent = (response.data.duracao_minima ? response.data.duracao_minima + ' período' + (response.data.duracao_minima > 1 ? 's' : '') : '[não informado]');
                        containerCurso.querySelector('#curso-duracao-maxima').textContent = (response.data.duracao_maxima ? response.data.duracao_maxima + ' período' + (response.data.duracao_maxima > 1 ? 's' : '') : '[não informado]');

                        curso.nome = response.data.nome || curso.nome;
                        curso.sigla = response.data.sigla || curso.sigla;
                        curso.emec_codigo = response.data.emec_codigo || curso.emec_codigo;
                        curso.grau = response.data.grau || curso.grau;
                        curso.duracao_minima = response.data.duracao_minima || curso.duracao_minima;
                        curso.duracao_maxima = response.data.duracao_maxima || curso.duracao_maxima;

                        modalEditarCurso.fechar();
                        notificador.sucesso(`Curso ${response.data.nome || ''} editado com sucesso!`, null, { alvo: '#curso-tabs'});
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
                        notificador.sucesso(`Curso ${response.data.nome || ''} arquivado com sucesso!`, null, { alvo: '#container-curso' });
                        window.location.reload();
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

            // Botão de editar curso
            if (buttonEditarCurso) {
                buttonEditarCurso.addEventListener('click', function(event) {
                    try {
                        event.preventDefault();

                        const formCursoModalEditar = document.querySelector('#curso-form-editar');

                        if (!formCursoModalEditar) {
                            throw new Error('Formulário de edição não encontrado');
                        }

                        // Preenche os campos do formulário com os dados do curso já carregados
                        formCursoModalEditar.attributes['action'].value = `/cursos/${curso.id}/editar`;
                        formCursoModalEditar.querySelector("input[name='id']").value = curso.id;
                        formCursoModalEditar.querySelector("input[name='nome']").value = curso.nome;
                        formCursoModalEditar.querySelector("input[name='sigla']").value = curso.sigla || '';
                        formCursoModalEditar.querySelector("input[name='emec-codigo']").value = curso.emec_codigo || '';
                        formCursoModalEditar.querySelector("select[name='grau']").value = curso.grau_id || '';
                        formCursoModalEditar.querySelector("input[name='duracao-minima']").value = curso.duracao_minima || '';
                        formCursoModalEditar.querySelector("input[name='duracao-maxima']").value = curso.duracao_maxima || '';

                        modalEditarCurso.abrir();
                    } catch (e) {
                        console.error(e);
                        if (typeof notificador !== 'undefined') {
                            notificador.erro('Erro ao carregar os dados do curso para edição.', null, { alvo: '#curso-tabs' });
                        }
                    }
                });
            }

            // Botão de arquivar curso
            if (buttonArquivarCurso) {
                buttonArquivarCurso.addEventListener('click', function(event) {
                    try {
                        event.preventDefault();

                        if (!modalArquivarCurso) {
                            throw new Error('Modal de arquivar não encontrado');
                        }

                        const formCursoModalArquivar = document.querySelector('#curso-form-arquivar');

                        if (!formCursoModalArquivar) {
                            throw new Error('Formulário de arquivar não encontrado');
                        }

                        // Usa os dados do curso já carregados
                        formCursoModalArquivar.attributes['action'].value = `/cursos/${curso.id}/arquivar`;
                        formCursoModalArquivar.querySelector('input[name="id"]').value = curso.id;
                        
                        const elementoNomeCurso = document.querySelector('#curso-nome-arquivar');
                        if (elementoNomeCurso) {
                            elementoNomeCurso.textContent = curso.nome;
                        }

                        modalArquivarCurso.abrir();
                    } catch (e) {
                        console.error(e);
                        if (typeof notificador !== 'undefined') {
                            notificador.erro('Erro ao abrir modal de arquivamento.', null, { alvo: '#curso-tabs' });
                        }
                    }
                });
            }

        } catch (error) {
            console.error('Erro ao inicializar página do curso:', error);
            if (typeof notificador !== 'undefined') {
                notificador.erro('Erro ao carregar os dados do curso.', null, { alvo: '#curso-tabs' });
            }
        }
    });
</script>
<script>
    function configurarAbas() {
        const tabs = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('.tab-panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove classe ativa de todas as abas
                tabs.forEach(item => {
                    item.classList.remove('border-sky-500', 'text-sky-600');
                    item.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                });

                // Adiciona classe ativa à aba clicada
                tab.classList.add('border-sky-500', 'text-sky-600');
                tab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

                // Esconde todos os painéis
                panels.forEach(panel => panel.classList.add('hidden'));

                // Mostra o painel da aba ativa
                const targetPanel = document.querySelector(tab.dataset.tabTarget);
                if (targetPanel) {
                    targetPanel.classList.remove('hidden');
                }
            });
        });
    }
</script>