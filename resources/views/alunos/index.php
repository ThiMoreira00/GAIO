<?php
use App\Models\Enumerations\AlunoMatriculaStatus;
?>

<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Lista de Alunos</h1>
    <div class="flex-shrink-0 flex gap-2">
        <button type="button" class="button-secondary inline-flex items-center gap-2" onclick="abrirModalImportarSisu()" id="button-importar-sisu">
            <span class="material-icons-sharp">upload</span>
            Importar via SISU
        </button>
        <button type="button" class="button-primary inline-flex items-center gap-2" data-modal-trigger="modal-aluno-adicionar-form" id="button-aluno-adicionar">
            <span class="material-icons-sharp">add</span>
            Adicionar novo aluno
        </button>
    </div>
</header>

<main class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="alunos-section-heading">
        <h2 id="alunos-section-heading" class="sr-only">Alunos cadastrados</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-aluno-filtros">
                    <form id="form-filtros-alunos" action="/alunos/filtrar" method="GET" data-tab-form>
                        <!-- Filtros Principais: Curso + Busca -->
                        <label for="busca" class="form-label mb-2">Buscar:</label>
                        <div class="flex flex-col lg:flex-row gap-3 mb-4">
                            <div class="relative w-full lg:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-aluno" class="input-search" placeholder="Buscar por nome, email, CPF ou matrícula..." data-tab-search>
                            </div>
                        </div>
                        
                        <!-- Filtros Secundários -->
                        <div class="flex flex-wrap justify-between items-center gap-4 py-4 border-t border-gray-200">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-sharp !text-lg text-gray-600">tune</span>
                                <span class="text-sm font-medium text-gray-600">Filtros avançados:</span>
                            </div>

                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <label for="filtro-situacao-matricula" class="text-sm font-medium text-gray-700 whitespace-nowrap">Situação:</label>
                                    <select name="filtro-situacao-matricula" id="filtro-situacao-matricula" class="form-select text-sm">
                                        <option value="">Todas</option>
                                        <?php foreach (AlunoMatriculaStatus::cases() as $status): ?>
                                            <option value="<?= strtolower($status->value); ?>"><?= ucfirst($status->value); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <label for="filtro-periodo-entrada" class="text-sm font-medium text-gray-700 whitespace-nowrap">Período de entrada:</label>
                                    <select name="filtro-periodo-entrada" id="filtro-periodo-entrada" class="form-select text-sm">
                                        <option value="">Todos</option>
                                        <?php 
                                        $anoAtual = date('Y');
                                        for ($ano = $anoAtual; $ano >= $anoAtual - 10; $ano--): 
                                            foreach ([1, 2] as $semestre):
                                        ?>
                                            <option value="<?= $ano . '.' . $semestre; ?>"><?= $ano . '.' . $semestre; ?></option>
                                        <?php 
                                            endforeach;
                                        endfor; 
                                        ?>
                                    </select>
                                </div>
                                
                                <button type="button" id="btn-limpar-filtros" class="text-sm px-4 py-2 text-gray-600 hover:text-sky-600 hover:bg-sky-50 rounded-lg font-medium transition-colors flex items-center gap-1.5 whitespace-nowrap">
                                    <span class="material-icons-sharp !text-lg">clear_all</span>
                                    Limpar
                                </button>
                            </div>
                        </div>

                        <!-- Ações em Bloco -->
                        <div class="flex flex-wrap justify-between items-center gap-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-sharp !text-lg text-gray-600">checklist</span>
                                <span class="text-sm font-medium text-gray-600">Ações em bloco:</span>
                                <button type="button" id="btn-selecionar-todos" class="text-sm px-4 py-2 text-gray-600 hover:text-sky-600 hover:bg-sky-50 rounded-lg font-medium transition-colors flex items-center gap-1.5 whitespace-nowrap">
                                    <span class="material-icons-sharp !text-lg">select_all</span>
                                    Selecionar todos
                                </button>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                

                                <div id="container-acoes-bloco" class="hidden flex-wrap items-center gap-2">
                                    <span class="text-sm font-medium text-sky-700">
                                        <span id="contador-selecionados">0</span> selecionado(s)
                                    </span>
                                    <div class="w-px h-6 bg-gray-300"></div>
                                    <button type="button" id="btn-inativar-bloco" class="button-yellow gap-2">
                                        <span class="material-icons-sharp">block</span>
                                        Inativar
                                    </button>
                                    <button type="button" id="btn-reativar-bloco" class="button-green gap-2">
                                        <span class="material-icons-sharp">autorenew</span>
                                        Reativar
                                    </button>
                                    <button type="button" id="btn-cancelar-selecao" class="button-gray-transparent gap-2">
                                        <span class="material-icons-sharp">cancel</span>
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- Container de resultados -->
                <div id="container-alunos" class="space-y-3"></div>

                <!-- Estado vazio -->
                <div id="estado-vazio" class="hidden text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <span class="material-icons-sharp !text-4xl text-gray-400">school</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum aluno encontrado</h3>
                    <p class="text-gray-500 text-base mb-6">Tente ajustar os filtros ou <a href="#" class="text-sky-600 underline font-semibold" data-modal-trigger="modal-aluno-adicionar-form">adicione um novo aluno</a>.</p>
                </div>

                <!-- Estado de erro -->
                <div id="estado-erro" class="hidden text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 mb-4">
                        <span class="material-icons-sharp !text-4xl text-red-400">error_outline</span>
                    </div>
                    <h3 class="text-lg font-semibold text-red-900 mb-2">Erro ao carregar alunos</h3>
                    <p class="text-gray-600 text-sm mb-6">Não foi possível conectar ao servidor. Verifique sua conexão.</p>
                    <button type="button" id="button-recarregar-alunos" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <span class="material-icons-sharp text-base mr-2">refresh</span>
                        Tentar novamente
                    </button>
                </div>

                <!-- Loader inicial -->
                <div id="loader-alunos" class="hidden text-center">
                    <span class="material-icons-sharp animate-spin text-4xl text-gray-400">autorenew</span>
                    <p class="text-gray-500 text-sm">Carregando alunos...</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/../templates/aluno-lista-item.php';
    include __DIR__ . '/../templates/aluno-modal-inativar.php';
    include __DIR__ . '/../templates/aluno-modal-reativar.php';
    include __DIR__ . '/../templates/aluno-modal-gerenciar-matriculas.php';
    include __DIR__ . '/../templates/aluno-modal-adicionar.php';
    include __DIR__ . '/../templates/aluno-modal-importar-sisu.php';
?>

<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/DataGrid.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal2.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/utils.js') ?>"></script>

<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', function() {

        const containerAlunos = document.getElementById('container-alunos');

        // Templates
        const templateModalInativarAluno = document.getElementById('template-aluno-modal-inativar');
        const templateModalReativarAluno = document.getElementById('template-aluno-modal-reativar');
        const templateModalGerenciarMatriculasAluno = document.getElementById('template-aluno-modal-gerenciar-matriculas');
        const templateModalAdicionarAluno = document.getElementById('template-aluno-modal-adicionar');
        const templateModalImportarSisu = document.getElementById('template-aluno-modal-importar-sisu');

        // Adicionar o template no conteúdo HTML
        const cloneModalAdicionarAluno = templateModalAdicionarAluno.content.cloneNode(true);
        document.body.appendChild(cloneModalAdicionarAluno);

        // Adicionar o template do modal importar SISU
        const cloneModalImportarSisu = templateModalImportarSisu.content.cloneNode(true);
        document.body.appendChild(cloneModalImportarSisu);

        // Configurar formulário
        const formAdicionarAluno = new Formulario('#aluno-form-adicionar', {
            onSuccess: function(response) {
                notificador.sucesso('Aluno adicionado com sucesso!', null, { alvo: '#main-alunos'});
                gridAlunos.recarregar();
                formAdicionarAluno.limpar();
                const modal = new Modal2('#aluno-modal-adicionar');
                modal.fechar();
            },
            onError: function(response) {
                notificador.erro(response.mensagem || 'Erro ao adicionar aluno. Tente novamente.');
            }
        });


        // Inicialização do DataGrid
        const gridAlunos = new DataGrid({
            endpoint: '/alunos/filtrar',
            container: '#container-alunos',
            template: '#template-lista-item-aluno',
            
            // Configuração dos campos de filtro
            campos: {
                busca: '#busca-aluno',
                situacao_matricula: '#filtro-situacao-matricula',
                periodo_entrada: '#filtro-periodo-entrada'
            },
            
            // Configurações
            metodo: 'GET',
            itensPorPagina: 100, // Carrega 100 alunos por vez
            debounceDelay: 800,
            
            // Elementos HTML customizados
            seletorLoader: '#loader-alunos',
            seletorMensagemVazio: '#estado-vazio',
            seletorMensagemErro: '#estado-erro',
            
            // Callbacks
            callbacks: {
                beforeLoad: async (parametros) => {
                    console.log('[GridAlunos] Carregando alunos...', parametros);
                    // Aguarda a última requisição em background terminar antes de aplicar o filtro
                    await aguardarCarregamentoBackground();
                },
                
                onComplete: async (dados, parametros) => {
                    console.log(`[GridAlunos] ${dados.length} alunos carregados`);
                    
                    // Limpa a seleção quando os dados são recarregados
                    limparSelecao();
                    
                    // Inicia carregamento em background após a primeira carga
                    if (parametros.pagina === 1 || !parametros.pagina) {
                        setTimeout(() => {
                            carregarRestanteEmBackground();
                        }, 500); // Aguarda 500ms após exibir os primeiros 50
                    }
                },
                
                onError: async (error, parametros) => {
                    console.error('[GridAlunos] Erro ao carregar alunos:', error);
                },
                
                onItemRender: (dadosElemento, elemento, index) => {

                    // Cria evento para o menu de ações
                    const buttonDropdown = elemento.querySelector('.aluno-dropdown-trigger');
                    const menuDropdown = elemento.querySelector('.aluno-dropdown-menu');

                    buttonDropdown.addEventListener('click', (event) => {
                        event.stopPropagation();
                        menuDropdown.classList.toggle('hidden');
                    });

                    // Fecha o menu ao clicar fora
                    document.addEventListener('click', () => {
                        if (!menuDropdown.classList.contains('hidden')) {
                            menuDropdown.classList.add('hidden');
                        }
                    });

                    // Atualiza o estilo do status da última matrícula
                    const statusElemento = elemento.querySelector('.aluno-status');
                    if (statusElemento && dadosElemento.matricula) {
                        statusElemento.classList.remove('bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800', 'bg-gray-100', 'text-gray-800', 'bg-blue-100', 'text-blue-800', 'bg-red-100', 'text-red-800');

                        switch (dadosElemento.matricula.status.nome) {
                            case 'CURSANDO':
                                statusElemento.classList.add('bg-green-100', 'text-green-800');
                                break;
                            case 'TRANCADO':
                                statusElemento.classList.add('bg-yellow-100', 'text-yellow-800');
                                break;
                            case 'EVADIDO':
                                statusElemento.classList.add('bg-gray-100', 'text-gray-800');
                                break;
                            case 'CONCLUIDO':
                                statusElemento.classList.add('bg-blue-100', 'text-blue-800');
                                break;
                            case 'DESISTENTE':
                                statusElemento.classList.add('bg-red-100', 'text-red-800');
                                break;
                            default:
                                statusElemento.classList.add('bg-red-100', 'text-red-800');
                                break;
                        }

                        statusElemento.textContent = dadosElemento.matricula.status.valor.toUpperCase();
                    }

                    console.log(dadosElemento);

                    // Verifica o nome correto a ser utilizado (nome social ou nome civil)
                    const nomeElemento = elemento.querySelector('.aluno-nome');
                    if (nomeElemento) {
                        if (dadosElemento.nome_social) {
                            nomeElemento.textContent = dadosElemento.nome_social;
                        } else {
                            nomeElemento.textContent = dadosElemento.nome_civil;
                        }
                    } else {
                        nomeElemento.textContent = dadosElemento.nome_civil;
                    }

                    // Verifica o e-mail preferencial
                    const emailElemento = elemento.querySelector('.aluno-email');
                    if (emailElemento) {
                        if (dadosElemento.email_institucional && dadosElemento.email_institucional.trim() !== '') {
                            emailElemento.innerHTML = '<span class="material-icons-sharp !text-sm text-gray-400">email</span>' + dadosElemento.email_institucional;
                        } else if (dadosElemento.email_pessoal && dadosElemento.email_pessoal.trim() !== '') {
                            emailElemento.innerHTML = '<span class="material-icons-sharp !text-sm text-gray-400">email</span>' + dadosElemento.email_pessoal;
                        } else {
                            emailElemento.innerHTML = '[não informado]';
                        }
                    }
                },
                
                onFilter: async (parametros) => {
                    console.log('[GridAlunos] Filtros aplicados:', parametros);
                },
                
                onLoadMore: async (pagina) => {
                    console.log(`[GridAlunos] Carregando página ${pagina}...`);
                }
            }
        });

        // Carrega os dados iniciais
        gridAlunos.carregar();


        // ===========================
        // EVENTOS
        // ===========================

        // Adiciona evento de clique para o botão de adicionar aluno
        const buttonAdicionarAluno = document.getElementById('button-aluno-adicionar');
        if (buttonAdicionarAluno) {
            buttonAdicionarAluno.addEventListener('click', function() {
                // Abre o modal de adicionar aluno
                const modalAdicionarAluno = new Modal2('#aluno-modal-adicionar', { debug: true });
                modalAdicionarAluno.abrir();
            });
        }


        // =============================
        // CARREGAMENTO EM BACKGROUND
        // =============================
        
        let carregandoEmBackground = false;
        let requisicaoBackgroundAtual = null;

        /**
         * Carrega todas as páginas restantes em background após a primeira carga
         */
        async function carregarRestanteEmBackground() {
            if (carregandoEmBackground) return;
            
            carregandoEmBackground = true;
            console.log('[Background] Iniciando carregamento das páginas restantes...');

            try {
                let paginaAtual = 2; // Começa da página 2 (página 1 já foi carregada)
                let temMaisPaginas = true;

                while (temMaisPaginas && carregandoEmBackground) {
                    const parametros = gridAlunos._obterParametrosFiltro();
                    parametros.pagina = paginaAtual;
                    parametros.limite = 50;

                    requisicaoBackgroundAtual = $.ajax({
                        url: '/alunos/filtrar',
                        method: 'GET',
                        dataType: 'json',
                        data: parametros
                    });

                    const response = await requisicaoBackgroundAtual;
                    requisicaoBackgroundAtual = null;

                    const novosItens = response.data || [];
                    
                    if (novosItens.length > 0) {
                        // Adiciona os novos itens aos dados do grid sem renderizar
                        gridAlunos.dados.push(...novosItens);
                        console.log(`[Background] Página ${paginaAtual} carregada: +${novosItens.length} alunos (Total: ${gridAlunos.dados.length})`);
                    }

                    // Verifica se chegou na última página
                    if (response.current_page >= response.last_page || novosItens.length === 0) {
                        temMaisPaginas = false;
                        console.log(`[Background] ✓ Carregamento concluído! Total de ${gridAlunos.dados.length} alunos disponíveis`);
                        break;
                    }

                    paginaAtual++;

                    // Verifica novamente se deve continuar antes de aguardar
                    if (!carregandoEmBackground) {
                        console.log(`[Background] ⏸ Interrompido na página ${paginaAtual - 1} (requisição concluída)`);
                        break;
                    }

                    // Aguarda um pouco antes de buscar a próxima página (evita sobrecarregar o servidor)
                    await new Promise(resolve => setTimeout(resolve, 200));
                }
            } catch (error) {
                console.error('[Background] Erro ao carregar páginas em background:', error);
            } finally {
                carregandoEmBackground = false;
                requisicaoBackgroundAtual = null;
            }
        }

        /**
         * Aguarda o carregamento em background terminar
         */
        async function aguardarCarregamentoBackground() {
            if (!carregandoEmBackground) return;
            
            console.log('[Background] Aguardando última requisição em background terminar...');
            
            // Marca para interromper novas iterações do loop
            carregandoEmBackground = false;
            
            // Aguarda a requisição atual completar
            if (requisicaoBackgroundAtual) {
                try {
                    await requisicaoBackgroundAtual;
                } catch (error) {
                    // Ignora erros, só queremos aguardar terminar
                }
            }
            
            console.log('[Background] ✓ Última requisição finalizada, prosseguindo com nova busca');
        }

        // =============================
        // SISTEMA DE SELEÇÃO EM BLOCO
        // =============================

        let alunosSelecionados = new Set();
        const containerAcoesBloco = document.getElementById('container-acoes-bloco');
        const contadorSelecionados = document.getElementById('contador-selecionados');
        const btnSelecionarTodos = document.getElementById('btn-selecionar-todos');
        const btnCancelarSelecao = document.getElementById('btn-cancelar-selecao');
        const btnInativarBloco = document.getElementById('btn-inativar-bloco');
        const btnReativarBloco = document.getElementById('btn-reativar-bloco');

        // Função para atualizar a barra de ações
        function atualizarBarraAcoes() {
            const qtdSelecionados = alunosSelecionados.size;
            const todosOsDados = gridAlunos.obterDados();
            const totalAlunos = todosOsDados.length;
            
            if (qtdSelecionados > 0) {
                containerAcoesBloco.classList.remove('hidden');
                containerAcoesBloco.classList.add('flex');
                contadorSelecionados.textContent = qtdSelecionados;
                
                // Atualizar texto do botão selecionar todos
                if (qtdSelecionados === totalAlunos && totalAlunos > 0) {
                    btnSelecionarTodos.innerHTML = '<span class="material-icons-sharp !text-lg">deselect</span>Desmarcar todos';
                } else {
                    btnSelecionarTodos.innerHTML = '<span class="material-icons-sharp !text-lg">select_all</span>Selecionar todos';
                }
            } else {
                containerAcoesBloco.classList.add('hidden');
                containerAcoesBloco.classList.remove('flex');
                btnSelecionarTodos.innerHTML = '<span class="material-icons-sharp !text-lg">select_all</span>Selecionar todos';
            }
        }

        // Função para limpar seleção
        function limparSelecao() {
            alunosSelecionados.clear();
            document.querySelectorAll('.aluno-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            atualizarBarraAcoes();
        }

        // Função para selecionar todos
        function selecionarTodos() {
            const checkboxes = document.querySelectorAll('.aluno-checkbox');
            const todosOsDados = gridAlunos.obterDados();
            const todosEstaoSelecionados = alunosSelecionados.size === todosOsDados.length && todosOsDados.length > 0;
            
            if (todosEstaoSelecionados) {
                // Desmarcar todos
                limparSelecao();
            } else {
                // Selecionar todos
                todosOsDados.forEach(aluno => {
                    alunosSelecionados.add(String(aluno.id));
                });
                
                // Marca os checkboxes
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                
                atualizarBarraAcoes();
            }
        }

        // Event listener para checkboxes
        containerAlunos.addEventListener('change', function(event) {
            if (event.target.classList.contains('aluno-checkbox')) {
                const alunoId = event.target.getAttribute('data-aluno-id');
                
                if (event.target.checked) {
                    alunosSelecionados.add(alunoId);
                } else {
                    alunosSelecionados.delete(alunoId);
                }
                
                atualizarBarraAcoes();
            }
        });

        // Selecionar todos
        if (btnSelecionarTodos) {
            btnSelecionarTodos.addEventListener('click', selecionarTodos);
        }

        // Cancelar seleção
        if (btnCancelarSelecao) {
            btnCancelarSelecao.addEventListener('click', limparSelecao);
        }

        // Inativar em bloco
        if (btnInativarBloco) {
            btnInativarBloco.addEventListener('click', function() {
                if (alunosSelecionados.size === 0) return;

                const alunosArray = Array.from(alunosSelecionados);
                const alunos = gridAlunos.obterDados();
                const alunosSelecionadosData = alunos.filter(a => alunosArray.includes(String(a.id)));

                // Validar se todos os alunos podem ser inativados
                const alunosAtivos = alunosSelecionadosData.filter(a => {
                    return a.matricula && a.matricula.status === 'CURSANDO';
                });

                if (alunosAtivos.length === 0) {
                    notificador.alerta('Nenhum dos alunos selecionados pode ser inativado. Apenas alunos com matrícula ativa (CURSANDO) podem ser inativados.', null, { alvo: 'body' });
                    return;
                }

                if (alunosAtivos.length < alunosSelecionados.size) {
                    const diff = alunosSelecionados.size - alunosAtivos.length;
                    notificador.info(`${diff} aluno(s) não pode(m) ser inativado(s) pois não possui(em) matrícula ativa.`, null, { alvo: 'body' });
                }

                // Confirmar ação
                if (!confirm(`Deseja realmente inativar ${alunosAtivos.length} aluno(s)?`)) return;

                // TODO: Implementar requisição para inativar múltiplos alunos
                console.log('Inativar alunos:', alunosAtivos.map(a => a.id));
                notificador.info('Funcionalidade de inativação em bloco em desenvolvimento.', null, { alvo: 'body' });
                
                limparSelecao();
            });
        }

        // Reativar em bloco
        if (btnReativarBloco) {
            btnReativarBloco.addEventListener('click', function() {
                if (alunosSelecionados.size === 0) return;

                const alunosArray = Array.from(alunosSelecionados);
                const alunos = gridAlunos.obterDados();
                const alunosSelecionadosData = alunos.filter(a => alunosArray.includes(String(a.id)));

                // Validar se todos os alunos podem ser reativados
                const alunosInativos = alunosSelecionadosData.filter(a => {
                    return a.matricula && (a.matricula.status === 'TRANCADO' || a.matricula.status === 'EVADIDO');
                });

                if (alunosInativos.length === 0) {
                    notificador.alerta('Nenhum dos alunos selecionados pode ser reativado. Apenas alunos com matrícula trancada ou evadida podem ser reativados.', null, { alvo: 'body' });
                    return;
                }

                if (alunosInativos.length < alunosSelecionados.size) {
                    const diff = alunosSelecionados.size - alunosInativos.length;
                    notificador.info(`${diff} aluno(s) não pode(m) ser reativado(s) pois não possui(em) matrícula inativa.`, null, { alvo: 'body' });
                }

                // Confirmar ação
                if (!confirm(`Deseja realmente reativar ${alunosInativos.length} aluno(s)?`)) return;

                // TODO: Implementar requisição para reativar múltiplos alunos
                console.log('Reativar alunos:', alunosInativos.map(a => a.id));
                notificador.info('Funcionalidade de reativação em bloco em desenvolvimento.', null, { alvo: 'body' });
                
                limparSelecao();
            });
        }

        // =============================
        // CONFIGURAÇÃO DOS MODAIS
        // =============================

        

        // Modal de Inativar Aluno
        if (templateModalInativarAluno) {
            const cloneModalInativarAluno = templateModalInativarAluno.content.cloneNode(true);
            document.body.appendChild(cloneModalInativarAluno);

            var modalInativarAluno = new Modal('#aluno-modal-inativar');

            document.getElementById('aluno-modal-inativar').addEventListener('fechar', function() {
                modalInativarAluno.limparCampos();
                modalInativarAluno.fechar();
            });

            var formularioInativarAluno = new Formulario('#aluno-form-inativar', {
                onSuccess: function(response) {
                    modalInativarAluno.fechar();
                    gridAlunos.recarregar();
                    notificador.sucesso(`Aluno inativado com sucesso!`, null, { alvo: 'body' });
                },
                notificador: {
                    status: true,
                    alvo: '#aluno-form-inativar'
                }
            });

            templateModalInativarAluno.remove();
        }

        // Modal de Reativar Aluno
        if (templateModalReativarAluno) {
            const cloneModalReativarAluno = templateModalReativarAluno.content.cloneNode(true);
            document.body.appendChild(cloneModalReativarAluno);

            var modalReativarAluno = new Modal('#aluno-modal-reativar');

            document.getElementById('aluno-modal-reativar').addEventListener('fechar', function() {
                modalReativarAluno.limparCampos();
                modalReativarAluno.fechar();
            });

            var formularioReativarAluno = new Formulario('#aluno-form-reativar', {
                onSuccess: function(response) {
                    modalReativarAluno.fechar();
                    gridAlunos.recarregar();
                    notificador.sucesso(`Aluno reativado com sucesso!`, null, { alvo: 'body' });
                },
                notificador: {
                    status: true,
                    alvo: '#aluno-form-reativar'
                }
            });

            templateModalReativarAluno.remove();
        }

        // Modal de Gerenciar Matrículas
        if (templateModalGerenciarMatriculasAluno) {
            const cloneModalGerenciarMatriculasAluno = templateModalGerenciarMatriculasAluno.content.cloneNode(true);
            document.body.appendChild(cloneModalGerenciarMatriculasAluno);

            var modalGerenciarMatriculasAluno = new Modal('#aluno-modal-gerenciar-matriculas');

            document.getElementById('aluno-modal-gerenciar-matriculas').addEventListener('fechar', function() {
                modalGerenciarMatriculasAluno.fechar();
            });

            templateModalGerenciarMatriculasAluno.remove();
        }

        // Botão de limpar filtros
        const btnLimparFiltros = document.getElementById('btn-limpar-filtros');
        if (btnLimparFiltros) {
            btnLimparFiltros.addEventListener('click', function() {
                gridAlunos.limparFiltros();
                gridAlunos.recarregar();
            });
        }

        // Botão de recarregar (em caso de erro)
        const btnRecarregarAlunos = document.getElementById('button-recarregar-alunos');
        if (btnRecarregarAlunos) {
            btnRecarregarAlunos.addEventListener('click', function() {
                gridAlunos.recarregar();
            });
        }

        // Event delegation para ações dos alunos
        if (containerAlunos) {
            containerAlunos.addEventListener('click', async function(event) {

                // EDITAR
                const buttonEditar = event.target.closest('a[data-action="editar"], button[data-action="editar"]');
                if (buttonEditar) {
                    event.preventDefault();
                    const itemAluno = buttonEditar.closest('.aluno-item');
                    if (!itemAluno) return;
                    const alunoId = itemAluno.getAttribute('data-id');

                    try {
                        let alunos = gridAlunos.obterDados();
                        let aluno = alunos.find(a => a.id == alunoId);
                        if (!aluno) throw new Error('Aluno não encontrado.');

                        console.log('Editar aluno:', aluno);
                        notificador.info('Funcionalidade em desenvolvimento.', null, { alvo: '#main-alunos' });
                    } catch (e) {
                        console.error(e);
                        notificador.erro('Erro ao carregar dados.', null, { alvo: '#main-alunos' });
                    }
                    return;
                }

                // ARQUIVAR
                const buttonArquivar = event.target.closest('a[data-action="arquivar"], button[data-action="arquivar"]');
                if (buttonArquivar) {
                    event.preventDefault();
                    const itemAluno = buttonArquivar.closest('.aluno-item');
                    if (!itemAluno) return;
                    const alunoId = itemAluno.getAttribute('data-id');
                    
                    if (!modalArquivarAluno) return;
                    
                    let aluno = gridAlunos.obterDados().find(a => a.id == alunoId);
                    const formAlunoModalArquivar = document.querySelector('#aluno-form-arquivar');
                    
                    formAlunoModalArquivar.attributes['action'].value = `/alunos/${aluno.id}/arquivar`;
                    formAlunoModalArquivar.querySelector('input[name="id"]').value = aluno.id;
                    document.querySelector('#aluno-nome-arquivar').textContent = aluno.nome_social || aluno.nome_civil;
                    
                    modalArquivarAluno.abrir();
                    return;
                }

                // INATIVAR
                const buttonInativar = event.target.closest('a[data-action="inativar"], button[data-action="inativar"]');
                if (buttonInativar) {
                    event.preventDefault();
                    const itemAluno = buttonInativar.closest('.aluno-item');
                    if (!itemAluno) return;
                    const alunoId = itemAluno.getAttribute('data-id');
                    
                    if (!modalInativarAluno) return;
                    
                    let aluno = gridAlunos.obterDados().find(a => a.id == alunoId);
                    const formAlunoModalInativar = document.querySelector('#aluno-form-inativar');
                    
                    formAlunoModalInativar.attributes['action'].value = `/alunos/${aluno.id}/inativar`;
                    formAlunoModalInativar.querySelector('input[name="id"]').value = aluno.id;
                    document.querySelector('#aluno-nome-inativar').textContent = aluno.nome_social || aluno.nome_civil;
                    
                    modalInativarAluno.abrir();
                    return;
                }

                // REATIVAR
                const buttonReativar = event.target.closest('a[data-action="reativar"], button[data-action="reativar"]');
                if (buttonReativar) {
                    event.preventDefault();
                    const itemAluno = buttonReativar.closest('.aluno-item');
                    if (!itemAluno) return;
                    const alunoId = itemAluno.getAttribute('data-id');
                    
                    if (!modalReativarAluno) return;
                    
                    let aluno = gridAlunos.obterDados().find(a => a.id == alunoId);
                    const formAlunoModalReativar = document.querySelector('#aluno-form-reativar');
                    
                    formAlunoModalReativar.attributes['action'].value = `/alunos/${aluno.id}/reativar`;
                    formAlunoModalReativar.querySelector('input[name="id"]').value = aluno.id;
                    document.querySelector('#aluno-nome-reativar').textContent = aluno.nome_social || aluno.nome_civil;
                    
                    modalReativarAluno.abrir();
                    return;
                }

                // GERENCIAR MATRÍCULAS
                const buttonGerenciarMatriculas = event.target.closest('a[data-action="gerenciar-matriculas"], button[data-action="gerenciar-matriculas"]');
                if (buttonGerenciarMatriculas) {
                    event.preventDefault();
                    const itemAluno = buttonGerenciarMatriculas.closest('.aluno-item');
                    if (!itemAluno) return;
                    const alunoId = itemAluno.getAttribute('data-id');
                    
                    if (!modalGerenciarMatriculasAluno) return;
                    
                    let aluno = gridAlunos.obterDados().find(a => a.id == alunoId);
                    document.querySelector('#aluno-nome-gerenciar').textContent = aluno.nome_social || aluno.nome_civil;
                    
                    // TODO: Carregar matrículas do aluno via AJAX
                    
                    modalGerenciarMatriculasAluno.abrir();
                    return;
                }

                // EXCLUIR
                const buttonExcluir = event.target.closest('a[data-action="excluir"], button[data-action="excluir"]');
                if (buttonExcluir) {
                    event.preventDefault();
                    const itemAluno = buttonExcluir.closest('.aluno-item');
                    if (!itemAluno) return;
                    const alunoId = itemAluno.getAttribute('data-id');
                    
                    if (!modalExcluirAluno) return;
                    
                    let aluno = gridAlunos.obterDados().find(a => a.id == alunoId);
                    const formAlunoModalExcluir = document.querySelector('#aluno-form-excluir');
                    
                    formAlunoModalExcluir.attributes['action'].value = `/alunos/${aluno.id}/excluir`;
                    formAlunoModalExcluir.querySelector('input[name="id"]').value = aluno.id;
                    document.querySelector('#aluno-nome-excluir').textContent = aluno.nome_social || aluno.nome_civil;
                    
                    modalExcluirAluno.abrir();
                    return;
                }
            });
        }
    });

</script>