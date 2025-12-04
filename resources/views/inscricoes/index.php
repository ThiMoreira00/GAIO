<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Inscrições - Turmas ofertadas</h1>
</header>
    
<main id="main-inscricoes-turmas" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="inscricoes-turmas-section-heading">
        <h2 id="inscricoes-turmas-section-heading" class="sr-only">Inscrições - Turmas ofertadas</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-inscricoes-turmas-filtros">
                    <form id="form-filtros-inscricoes-turmas" action="/inscricoes/turmas/filtrar" method="GET" data-tab-form>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-shrink-0">
                                <input type="hidden" name="turno" id="turno-input" value="" data-tab-turno-input>
                                <div class="bg-gray-100 p-1 rounded-lg sm:rounded-full flex items-center justify-center flex-wrap gap-1">
                                    <button type="button" class="filter-btn-turno-turma tab-item active" data-tab-turno="" data-tab-button>Todos</button>
                                    <?php foreach ($turnos as $turno): ?>
                                        <button type="button" data-tab-turno="<?= strtolower($turno->name); ?>" class="filter-btn-turno-turma tab-item" data-tab-button><?= $turno->value; ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="relative w-full sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-inscricoes-turmas" class="input-search" placeholder="Buscar turmas ofertadas..." data-tab-search>
                            </div>
                        </div>
                    </form>
                </section>

                <div id="container-inscricoes-turmas" class="space-y-4">
                </div>

                <div id="loader-inscricoes-turmas" class="hidden text-center mt-8 py-4">
                    <span class="material-icons-sharp text-5xl text-gray-400 animate-spin">sync</span>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/templates/inscricao-turma-lista-item.php';
    include __DIR__ . '/../templates/inscricao-modal-solicitar.php';
    include __DIR__ . '/../templates/inscricao-modal-cancelar.php';
?>


<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/tab.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Modais
        const modalConfirmarInscricaoTurma = document.getElementById('inscricao-turma-modal-confirmar');

        // Templates
        const templateListaItemInscricaoTurma = document.getElementById('template-lista-item-inscricao-turma');

        // Containers
        const containerInscricoesTurmas = document.getElementById('main-inscricoes-turmas');

        // Inicialização do Tab
        const tab = new Tab('#main-inscricoes-turmas', {
            prefixo: 'inscricao-turma',
            seletorConteudo: '#container-inscricoes-turmas',
            seletorTemplateRegistros: '#template-lista-item-inscricao-turma',
            url: '/inscricoes/turmas/filtrar',
            metodo: 'GET'
        });

        // Função para processar HTML nos horários
        function processarHorariosHTML() {
            document.querySelectorAll('.turma-horario').forEach(function(el) {
                const html = el.textContent;
                if (html && html.includes('<br>')) {
                    el.innerHTML = html;
                }
            });
        }
        
        // Função para aplicar estilos condicionais baseado em data attributes
        function aplicarEstilosCondicionais() {
            document.querySelectorAll('.turma-item:not([data-processado])').forEach(function(item) {
                const jaAprovado = item.getAttribute('data-ja-aprovado') === 'true' || item.getAttribute('data-ja-aprovado') === '1';
                const temInscricao = item.getAttribute('data-tem-inscricao') === 'true' || item.getAttribute('data-tem-inscricao') === '1';
                
                // Marcar como processado para evitar reprocessamento
                item.setAttribute('data-processado', 'true');
                
                // Aplicar estilos baseado no status
                // PRIORIDADE: Inscrição ativa > Já aprovado > Disponível
                if (temInscricao) {
                    // Tem inscrição ativa - PRIORIDADE MÁXIMA (mesmo se já foi aprovado em outra turma)
                    item.classList.add('border-gray-200', 'hover:border-sky-300');
                    
                    const icone = item.querySelector('.turma-icone');
                    if (icone) icone.classList.add('bg-green-600');
                    
                    item.querySelector('.turma-disciplina')?.classList.add('text-gray-900');
                    item.querySelector('.turma-professor')?.classList.add('text-gray-600');
                    item.querySelector('.turma-label-horario')?.classList.add('text-gray-500');
                    item.querySelector('.turma-horario')?.classList.add('text-gray-800');
                    item.querySelector('.turma-label-turno')?.classList.add('text-gray-500');
                    
                    const turno = item.querySelector('.turma-turno');
                    if (turno) {
                        turno.classList.add('bg-gray-100', 'text-gray-800');
                    }
                    
                    // Mostrar apenas botão cancelar
                    const btnSolicitar = item.querySelector('.button-solicitar');
                    const btnCancelar = item.querySelector('.button-cancelar');
                    const lblConcluida = item.querySelector('.turma-concluida');
                    
                    if (btnSolicitar) {
                        btnSolicitar.classList.add('hidden');
                        btnSolicitar.classList.remove('inline-flex');
                    }
                    if (btnCancelar) {
                        btnCancelar.classList.remove('hidden');
                        btnCancelar.classList.add('inline-flex');
                    }
                    if (lblConcluida) {
                        lblConcluida.classList.add('hidden');
                        lblConcluida.classList.remove('inline-flex');
                    }
                } else if (jaAprovado) {
                    // Disciplina já aprovada (e SEM inscrição ativa) - estilo cinza
                    item.classList.add('border-gray-300', 'bg-gray-50');
                    item.classList.remove('border-gray-200', 'hover:border-sky-300');
                    
                    const icone = item.querySelector('.turma-icone');
                    if (icone) icone.classList.add('bg-gray-400');
                    
                    const iconeNome = item.querySelector('.turma-icone-nome');
                    if (iconeNome) iconeNome.textContent = 'check_circle';
                    
                    item.querySelector('.turma-disciplina')?.classList.add('text-gray-500');
                    item.querySelector('.turma-professor')?.classList.add('text-gray-400');
                    item.querySelector('.turma-label-horario')?.classList.add('text-gray-400');
                    item.querySelector('.turma-horario')?.classList.add('text-gray-500');
                    item.querySelector('.turma-label-turno')?.classList.add('text-gray-400');
                    
                    const turno = item.querySelector('.turma-turno');
                    if (turno) {
                        turno.classList.add('bg-gray-200', 'text-gray-500');
                        turno.classList.remove('bg-gray-100', 'text-gray-800');
                    }
                    
                    // Mostrar apenas indicador de concluída
                    const btnSolicitar = item.querySelector('.button-solicitar');
                    const btnCancelar = item.querySelector('.button-cancelar');
                    const lblConcluida = item.querySelector('.turma-concluida');
                    
                    if (btnSolicitar) {
                        btnSolicitar.classList.add('hidden');
                        btnSolicitar.classList.remove('inline-flex');
                    }
                    if (btnCancelar) {
                        btnCancelar.classList.add('hidden');
                        btnCancelar.classList.remove('inline-flex');
                    }
                    if (lblConcluida) {
                        lblConcluida.classList.remove('hidden');
                        lblConcluida.classList.add('inline-flex');
                    }
                } else {
                    // Disponível para inscrição - estilo normal com botão solicitar
                    item.classList.add('border-gray-200', 'hover:border-sky-300');
                    
                    const icone = item.querySelector('.turma-icone');
                    if (icone) icone.classList.add('bg-sky-600');
                    
                    item.querySelector('.turma-disciplina')?.classList.add('text-gray-900');
                    item.querySelector('.turma-professor')?.classList.add('text-gray-600');
                    item.querySelector('.turma-label-horario')?.classList.add('text-gray-500');
                    item.querySelector('.turma-horario')?.classList.add('text-gray-800');
                    item.querySelector('.turma-label-turno')?.classList.add('text-gray-500');
                    
                    const turno = item.querySelector('.turma-turno');
                    if (turno) {
                        turno.classList.add('bg-gray-100', 'text-gray-800');
                    }
                    
                    // Mostrar apenas botão solicitar
                    const btnSolicitar = item.querySelector('.button-solicitar');
                    const btnCancelar = item.querySelector('.button-cancelar');
                    const lblConcluida = item.querySelector('.turma-concluida');
                    
                    if (btnSolicitar) {
                        btnSolicitar.classList.remove('hidden');
                        btnSolicitar.classList.add('inline-flex');
                    }
                    if (btnCancelar) {
                        btnCancelar.classList.add('hidden');
                        btnCancelar.classList.remove('inline-flex');
                    }
                    if (lblConcluida) {
                        lblConcluida.classList.add('hidden');
                        lblConcluida.classList.remove('inline-flex');
                    }
                }
            });
        }

        // Observar mudanças no container para processar HTML
        const observer = new MutationObserver(function(mutations) {
            processarHorariosHTML();
            aplicarEstilosCondicionais();
        });
        
        observer.observe(document.getElementById('container-inscricoes-turmas'), {
            childList: true,
            subtree: true
        });

        // Colocar um evento para quando recarregar os dados do tab
        containerInscricoesTurmas.addEventListener('tabDadosRecarregados', function() {
            // Remover marcador de processado para reprocessar novos items
            document.querySelectorAll('.turma-item[data-processado]').forEach(function(item) {
                item.removeAttribute('data-processado');
            });
            processarHorariosHTML();
            aplicarEstilosCondicionais();
            console.log('Dados do tab recarregados com sucesso.');
        });

        // Modal de solicitação de inscrição
        const templateModalSolicitarInscricao = document.getElementById('template-inscricao-modal-solicitar');
        if (templateModalSolicitarInscricao) {
            const cloneModalSolicitar = templateModalSolicitarInscricao.content.cloneNode(true);
            document.body.appendChild(cloneModalSolicitar);
            
            var modalSolicitarInscricao = new Modal('#inscricao-modal-solicitar');
            
            // Evento de fechamento
            document.getElementById('inscricao-modal-solicitar').addEventListener('fechar', function() {
                modalSolicitarInscricao.fechar();
            });
            
            // Formulário de solicitação
            var formularioSolicitarInscricao = new Formulario('#inscricao-form-solicitar', {
                onSuccess: function(response) {
                    modalSolicitarInscricao.fechar();
                    tab.recarregar();
                    notificador.sucesso(response.mensagem || 'Solicitação enviada com sucesso!', null, { alvo: '#main-inscricoes-turmas' });
                },
                onError: function(error) {
                    console.error('Erro ao solicitar inscrição:', error);
                },
                notificador: {
                    status: true,
                    alvo: '#inscricao-form-solicitar'
                }
            });
        }
        
        // Modal de cancelamento de inscrição
        const templateModalCancelarInscricao = document.getElementById('template-inscricao-modal-cancelar');
        if (templateModalCancelarInscricao) {
            const cloneModalCancelar = templateModalCancelarInscricao.content.cloneNode(true);
            document.body.appendChild(cloneModalCancelar);
            
            var modalCancelarInscricao = new Modal('#inscricao-modal-cancelar');
            
            // Evento de fechamento
            document.getElementById('inscricao-modal-cancelar').addEventListener('fechar', function() {
                modalCancelarInscricao.fechar();
            });
            
            // Formulário de cancelamento
            var formularioCancelarInscricao = new Formulario('#inscricao-form-cancelar', {
                onSuccess: function(response) {
                    console.log('Inscrição cancelada, fechando modal e recarregando...');
                    modalCancelarInscricao.fechar();
                    
                    // Dar um pequeno delay para garantir que o modal fechou
                    setTimeout(function() {
                        console.log('Recarregando Tab...');
                        tab.recarregar();
                    }, 100);
                    
                    notificador.sucesso(response.mensagem || 'Inscrição cancelada com sucesso!', null, { alvo: '#main-inscricoes-turmas' });
                },
                onError: function(error) {
                    console.error('Erro ao cancelar inscrição:', error);
                },
                notificador: {
                    status: true,
                    alvo: '#inscricao-form-cancelar'
                }
            });
        }


        /** ======================
         * EVENTOS
         * ====================== */

        // Arquivar

        if (containerInscricoesTurmas) {

            containerInscricoesTurmas.addEventListener('click', async function(event) {

                // SOLICITAR INSCRIÇÃO
                const buttonSolicitar = event.target.closest('button[data-action="solicitar"]');
                if (buttonSolicitar) {
                    event.preventDefault();
                    const turmaItem = buttonSolicitar.closest('.turma-item');
                    if (!turmaItem) return;
                    const turmaId = turmaItem.getAttribute('data-id');
                    
                    // Buscar dados da turma
                    const turmas = tab.obterDados();
                    const turma = turmas.find(t => t.id == turmaId);
                    
                    if (!turma) {
                        notificador.erro('Turma não encontrada.', null, { alvo: '#main-inscricoes-turmas' });
                        return;
                    }
                    
                    // Preencher modal com dados da turma
                    const modal = document.getElementById('inscricao-modal-solicitar');
                    if (modal) {
                        modal.querySelector('#inscricao-turma-id').value = turma.id;
                        modal.querySelector('#inscricao-turma-disciplina').textContent = turma.disciplina;
                        modal.querySelector('#inscricao-turma-professor').textContent = turma.professor;
                        modal.querySelector('#inscricao-turma-turno').textContent = turma.turno || 'N/A';
                        modal.querySelector('#inscricao-turma-modalidade').textContent = turma.modalidade || 'N/A';
                        modal.querySelector('#inscricao-turma-horario').innerHTML = turma.horario.replace(/<br>/g, '<br>');
                        
                        var modalSolicitarInscricao = new Modal('#inscricao-modal-solicitar');
                        modalSolicitarInscricao.abrir();
                    }
                    return;
                }
                
                // CANCELAR INSCRIÇÃO
                const buttonCancelar = event.target.closest('button[data-action="cancelar"]');
                if (buttonCancelar) {
                    event.preventDefault();
                    const turmaItem = buttonCancelar.closest('.turma-item');
                    if (!turmaItem) return;
                    const inscricaoId = turmaItem.getAttribute('data-inscricao-id');
                    
                    // Buscar dados da turma
                    const turmas = tab.obterDados();
                    const turma = turmas.find(t => t.inscricao_id == inscricaoId);
                    
                    if (!turma) {
                        notificador.erro('Inscrição não encontrada.', null, { alvo: '#main-inscricoes-turmas' });
                        return;
                    }
                    
                    // Preencher modal com dados da inscrição
                    const modal = document.getElementById('inscricao-modal-cancelar');
                    if (modal) {
                        modal.querySelector('#inscricao-cancelar-id').value = inscricaoId;
                        modal.querySelector('#inscricao-cancelar-disciplina').textContent = turma.disciplina;
                        modal.querySelector('#inscricao-cancelar-professor').textContent = turma.professor;
                        modal.querySelector('#inscricao-cancelar-turno').textContent = turma.turno || 'N/A';
                        modal.querySelector('#inscricao-cancelar-modalidade').textContent = turma.modalidade || 'N/A';
                        modal.querySelector('#inscricao-cancelar-horario').innerHTML = turma.horario.replace(/<br>/g, '<br>');
                        
                        var modalCancelarInscricao = new Modal('#inscricao-modal-cancelar');
                        modalCancelarInscricao.abrir();
                    }
                    return;
                }

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