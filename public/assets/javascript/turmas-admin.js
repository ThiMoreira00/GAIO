/**
 * Script para gerenciamento de turmas - Administrador
 * @file turmas-admin.js
 */

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
    const templateModalAdicionarAlunos = document.getElementById('template-turma-modal-adicionar-alunos');
    const templateModalRemoverAluno = document.getElementById('template-turma-modal-remover-aluno');

    const templateListaItemTurma = document.getElementById('template-lista-item-turma');

    const containerTurmas = document.getElementById('main-turmas-admin');

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
        exibirLoader: true,
        debounceDelay: 1000,
        
        callbacks: {
            beforeLoad: async (parametros) => {
                console.log('[DataGrid] Carregando turmas...', parametros);
            },
            
            onComplete: async (dados, parametros, response) => {
                console.log(`[DataGrid] ${dados.length} turmas carregadas`);
                console.log(`[DataGrid] Página ${response.current_page}/${response.last_page}`);
            },
            
            onError: async (error, parametros) => {
                console.error('[DataGrid] Erro ao carregar turmas:', error);
            },
            
            onItemRender: (item, elemento, index) => {
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
            document.querySelectorAll('.filter-btn-turma').forEach(b => {
                b.classList.remove('active');
            });
            
            this.classList.add('active');
            
            const status = this.getAttribute('data-tab-status') || '';
            document.getElementById('status-input').value = status;
            
            datagrid.recarregar();
        });
    });

    // Botão Limpar Filtros
    const btnLimparFiltros = document.getElementById('btn-limpar-filtros-turmas');
    if (btnLimparFiltros) {
        btnLimparFiltros.addEventListener('click', function() {
            document.getElementById('status-input').value = '';
            document.querySelectorAll('.filter-btn-turma').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.filter-btn-turma[data-tab-status=""]').classList.add('active');
            
            document.getElementById('periodo-input').value = '';
            document.getElementById('turno-input').value = '';
            document.getElementById('modalidade-input').value = '';
            document.getElementById('busca-turma').value = '';
            
            datagrid.recarregar();
        });
    }

    // =============================
    // MODAIS
    // =============================

    // Modal: Editar Turma
    if (templateModalEditarTurma) {
        const cloneModalEditarTurma = templateModalEditarTurma.content.cloneNode(true);
        document.body.appendChild(cloneModalEditarTurma);

        var modalEditarTurma = new Modal('#turma-modal-editar');

        document.getElementById('turma-modal-editar').addEventListener('fechar', function() {
            modalEditarTurma.limparCampos();
            modalEditarTurma.fechar();
        });

        var formularioEditarTurma = new Formulario('#turma-form-editar', {
            onSuccess: function(response) {
                modalEditarTurma.fechar();
                datagrid.recarregar();
                notificador.sucesso(`Turma ${response.data.codigo || ''} editada com sucesso!`, null, { alvo: '#main-turmas-admin'});
            },
            notificador: {
                status: true,
                alvo: '#turma-form-editar'
            }
        });

        templateModalEditarTurma.remove();
    }

    // Modal: Arquivar Turma
    if (templateModalArquivarTurma) {
        const cloneModalArquivarTurma = templateModalArquivarTurma.content.cloneNode(true);
        document.body.appendChild(cloneModalArquivarTurma);

        var modalArquivarTurma = new Modal('#turma-modal-arquivar');

        document.getElementById('turma-modal-arquivar').addEventListener('fechar', function() {
            modalArquivarTurma.limparCampos();
            modalArquivarTurma.fechar();
        });

        var formularioArquivarTurma = new Formulario('#turma-form-arquivar', {
            onSuccess: function(response) {
                modalArquivarTurma.fechar();
                datagrid.recarregar();
                notificador.sucesso(`Turma ${response.data.codigo || ''} arquivada com sucesso!`, null, { alvo: '#main-turmas-admin' });
            },
            notificador: {
                status: true,
                alvo: '#turma-form-arquivar'
            }
        });

        templateModalArquivarTurma.remove();
    }

    // Modal: Confirmar Turma
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
                notificador.sucesso('Turma confirmada com sucesso!', null, { alvo: '#main-turmas-admin' });
            },
            notificador: {
                status: true,
                alvo: '#turma-form-confirmar'
            }
        });

        templateModalConfirmarTurma.remove();
    }

    // Modal: Finalizar Turma
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
                notificador.sucesso('Turma finalizada com sucesso!', null, { alvo: '#main-turmas-admin' });
            },
            notificador: {
                status: true,
                alvo: '#turma-form-finalizar'
            }
        });

        templateModalFinalizarTurma.remove();
    }

    // Modal: Liberar Turma
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
                notificador.sucesso('Turma liberada com sucesso!', null, { alvo: '#main-turmas-admin' });
            },
            notificador: {
                status: true,
                alvo: '#turma-form-liberar'
            }
        });

        templateModalLiberarTurma.remove();
    }

    // Modal: Adicionar Alunos
    if (templateModalAdicionarAlunos) {
        const cloneModalAdicionarAlunos = templateModalAdicionarAlunos.content.cloneNode(true);
        document.body.appendChild(cloneModalAdicionarAlunos);

        var modalAdicionarAlunos = new Modal('#turma-modal-adicionar-alunos');

        document.getElementById('turma-modal-adicionar-alunos').addEventListener('fechar', function() {
            modalAdicionarAlunos.limparCampos();
            modalAdicionarAlunos.fechar();
        });

        var formularioAdicionarAlunos = new Formulario('#turma-form-adicionar-alunos', {
            onSuccess: function(response) {
                modalAdicionarAlunos.fechar();
                notificador.sucesso(response.mensagem || 'Alunos adicionados com sucesso!', null, { alvo: '#main-turmas-admin' });
            },
            notificador: {
                status: true,
                alvo: '#turma-form-adicionar-alunos'
            }
        });

        templateModalAdicionarAlunos.remove();
    }

    // Modal: Remover Aluno
    if (templateModalRemoverAluno) {
        const cloneModalRemoverAluno = templateModalRemoverAluno.content.cloneNode(true);
        document.body.appendChild(cloneModalRemoverAluno);

        var modalRemoverAluno = new Modal('#turma-modal-remover-aluno');

        document.getElementById('turma-modal-remover-aluno').addEventListener('fechar', function() {
            modalRemoverAluno.limparCampos();
            modalRemoverAluno.fechar();
        });

        var formularioRemoverAluno = new Formulario('#turma-form-remover-aluno', {
            onSuccess: function(response) {
                modalRemoverAluno.fechar();
                notificador.sucesso('Aluno removido com sucesso!', null, { alvo: '#main-turmas-admin' });
            },
            notificador: {
                status: true,
                alvo: '#turma-form-remover-aluno'
            }
        });

        templateModalRemoverAluno.remove();
    }

    // =============================
    // EVENTOS
    // =============================

    if (containerTurmas) {

        containerTurmas.addEventListener('click', async function(event) {

            // VISUALIZAR
            const buttonVisualizar = event.target.closest('a[data-action="visualizar"]');
            if (buttonVisualizar) {
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
                    const response = await fetch(`/turmas/${turmaId}/dados`);
                    const resultado = await response.json();
                    
                    if (resultado.status !== 'sucesso') {
                        throw new Error(resultado.mensagem || 'Erro ao carregar dados da turma.');
                    }

                    const turma = resultado.data;
                    const formTurmaModalEditar = document.querySelector('#turma-form-editar');

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
                    notificador.erro('Erro ao carregar os dados da turma para edição.', null, { alvo: '#main-turmas-admin' });
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
    
    if (templateModalAdicionarTurma) {
        // Código do Modal2 já implementado na view principal
    }
});
