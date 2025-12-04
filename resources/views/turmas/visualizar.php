<div class="min-h-screen bg-gray-50">
    <!-- Header da Turma -->
    <header class="bg-gradient-to-r from-sky-600 to-blue-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="material-icons-sharp text-4xl">school</span>
                        <h1 class="text-3xl font-bold"><?= $disciplina_nome; ?></h1>
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm mt-4">
                        <div class="flex items-center gap-2">
                            <span class="material-icons-sharp text-lg">label</span>
                            <span><?= $turma->obterCodigo(); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-icons-sharp text-lg">calendar_today</span>
                            <span><?= $periodo_nome; ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-icons-sharp text-lg">person</span>
                            <span><?= $professor_nome; ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-icons-sharp text-lg">schedule</span>
                            <span><?= $turma->obterTurno()->value; ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-icons-sharp text-lg">people</span>
                            <span><?= $turma->obterQuantidadeInscricoes(); ?> / <?= $turma->obterCapacidadeMaxima(); ?> alunos</span>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-sm">
                        <?= $turma->obterStatus()->value; ?>
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Navegacao por Abas -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex overflow-x-auto scrollbar-hide">
                <button type="button" data-tab-target="mural" class="tab-nav-button active whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">dashboard</span>
                    Mural
                </button>

                <?php if ($permissoes['visualizar_alunos']): ?>
                <button type="button" data-tab-target="alunos" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">people</span>
                    Alunos
                </button>
                <?php endif; ?>

                <?php if ($permissoes['visualizar_avaliacoes']): ?>
                <button type="button" data-tab-target="avaliacoes" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">assignment</span>
                    Avaliações
                </button>
                <?php endif; ?>

                <?php if ($permissoes['visualizar_frequencias']): ?>
                <button type="button" data-tab-target="frequencias" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">checklist</span>
                    Frequências
                </button>
                <?php endif; ?>

                <?php if ($permissoes['visualizar_conteudos']): ?>
                <button type="button" data-tab-target="conteudos" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">library_books</span>
                    Conteúdos
                </button>
                <?php endif; ?>

                <button type="button" data-tab-target="horarios" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">access_time</span>
                    Horários
                </button>

                <?php if ($permissoes['editar']): ?>
                <button type="button" data-tab-target="configuracoes" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">settings</span>
                    Configurações
                </button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Conteudo das Abas -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?= flash()->exibir(); ?>

        <!-- Aba: Mural -->
        <section id="tab-mural" class="tab-content active">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna Principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informacoes Rapidas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <span class="material-icons-sharp text-sky-600">info</span>
                            Informações da Turma
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Disciplina</p>
                                <p class="font-medium"><?= $disciplina_nome; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Código</p>
                                <p class="font-medium"><?= $turma->obterCodigo(); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Professor</p>
                                <p class="font-medium"><?= $professor_nome; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Período Letivo</p>
                                <p class="font-medium"><?= $periodo_nome; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Modalidade</p>
                                <p class="font-medium"><?= $turma->obterModalidade()->value; ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Turno</p>
                                <p class="font-medium"><?= $turma->obterTurno()->value; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Avisos e Comunicados -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <span class="material-icons-sharp text-amber-600">campaign</span>
                            Avisos e Comunicados
                        </h2>
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-icons-sharp text-5xl text-gray-300">notifications_none</span>
                            <p class="mt-2">Nenhum aviso no momento</p>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-6">
                    <!-- Estatísticas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Estatísticas</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm text-gray-600">Ocupação</span>
                                    <span class="text-sm font-semibold"><?= $turma->calcularPercentualOcupacao(); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-sky-600 h-2 rounded-full" style="width: <?= $turma->calcularPercentualOcupacao(); ?>%"></div>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Alunos inscritos</span>
                                    <span class="text-lg font-bold text-sky-600"><?= $turma->obterQuantidadeInscricoes(); ?></span>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Vagas disponiveis</span>
                                    <span class="text-lg font-bold text-green-600"><?= $turma->obterVagasDisponiveis(); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acoes Rapidas -->
                    <?php if ($permissoes['editar'] || $permissoes['arquivar']): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">Ções</h3>
                        <div class="space-y-2">
                            <?php if ($permissoes['editar']): ?>
                            <button type="button" class="w-full button-secondary text-left" onclick="editarTurma()">
                                <span class="material-icons-sharp">edit</span>
                                Editar turma
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($permissoes['confirmar'] && $turma->obterStatus()->value === 'OFERTADA'): ?>
                            <button type="button" class="w-full button-primary text-left" onclick="confirmarTurma()">
                                <span class="material-icons-sharp">check_circle</span>
                                Confirmar turma
                            </button>
                            <?php endif; ?>

                            <?php if ($permissoes['liberar'] && $turma->obterStatus()->value === 'PLANEJADA'): ?>
                            <button type="button" class="w-full button-primary text-left" onclick="liberarTurma()">
                                <span class="material-icons-sharp">lock_open</span>
                                Liberar para inscrições
                            </button>
                            <?php endif; ?>

                            <?php if ($permissoes['finalizar'] && $turma->obterStatus()->value === 'ATIVA'): ?>
                            <button type="button" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-semibold inline-flex items-center gap-2" onclick="finalizarTurma()">
                                <span class="material-icons-sharp">done_all</span>
                                Finalizar turma
                            </button>
                            <?php endif; ?>

                            <?php if ($permissoes['arquivar']): ?>
                            <button type="button" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-semibold inline-flex items-center gap-2" onclick="arquivarTurma()">
                                <span class="material-icons-sharp">archive</span>
                                Arquivar turma
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Aba: Alunos -->
        <?php if ($permissoes['visualizar_alunos']): ?>
        <section id="tab-alunos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Alunos Matriculados</h2>
                        <?php if ($permissoes['adicionar_alunos']): ?>
                        <button type="button" class="button-primary inline-flex items-center" onclick="adicionarAlunos()">
                            <span class="material-icons-sharp -ml-1 mr-2">person_add</span>
                            Adicionar alunos
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-6" id="container-alunos">
                    <div class="text-center py-8 text-gray-500">
                        <span class="material-icons-sharp text-5xl text-gray-300 animate-spin">sync</span>
                        <p class="mt-2">Carregando alunos...</p>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Aba: Avaliacoes -->
        <?php if ($permissoes['visualizar_avaliacoes']): ?>
        <section id="tab-avaliacoes" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold">Avaliações</h2>
                    <?php if ($permissoes['gerenciar_criterios']): ?>
                    <button type="button" class="button-primary inline-flex items-center">
                        <span class="material-icons-sharp -ml-1 mr-2">add</span>
                        Nova avaliação
                    </button>
                    <?php endif; ?>
                </div>
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons-sharp text-5xl text-gray-300">assignment</span>
                    <p class="mt-2">Nenhuma avaliação cadastrada</p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Aba: Frequencias -->
        <?php if ($permissoes['visualizar_frequencias']): ?>
        <section id="tab-frequencias" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold">Controle de Frequência</h2>
                    <?php if ($permissoes['configurar_frequencias']): ?>
                    <button type="button" class="button-primary inline-flex items-center">
                        <span class="material-icons-sharp -ml-1 mr-2">settings</span>
                        Configurar
                    </button>
                    <?php endif; ?>
                </div>
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons-sharp text-5xl text-gray-300">checklist</span>
                    <p class="mt-2">Nenhum registro de frequência</p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Aba: Conteudos -->
        <?php if ($permissoes['visualizar_conteudos']): ?>
        <section id="tab-conteudos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-6">Conteúdos Lecionados</h2>
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons-sharp text-5xl text-gray-300">library_books</span>
                    <p class="mt-2">Nenhum conteúdo registrado</p>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Aba: Horarios -->
        <section id="tab-horarios" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-6">Horários da Turma</h2>
                <div class="text-center py-8 text-gray-500">
                    <span class="material-icons-sharp text-5xl text-gray-300">access_time</span>
                    <p class="mt-2">Nenhum horário definido</p>
                </div>
            </div>
        </section>

        <!-- Aba: Configuracoes -->
        <?php if ($permissoes['editar']): ?>
        <section id="tab-configuracoes" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-6">Configurações da Turma</h2>
                <form id="form-configuracoes-turma" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="config-codigo" class="form-label required">Código da Turma</label>
                            <input type="text" id="config-codigo" name="codigo" class="form-input" value="<?= $turma->obterCodigo(); ?>" required>
                        </div>
                        <div>
                            <label for="config-capacidade" class="form-label required">Capacidade Máxima</label>
                            <input type="number" id="config-capacidade" name="capacidade_maxima" class="form-input" value="<?= $turma->obterCapacidadeMaxima(); ?>" required>
                        </div>
                        <div>
                            <label for="config-turno" class="form-label required">Turno</label>
                            <select id="config-turno" name="turno" class="form-select" required>
                                <option value="MATUTINO" <?= $turma->obterTurno()->value === 'MATUTINO' ? 'selected' : ''; ?>>Matutino</option>
                                <option value="VESPERTINO" <?= $turma->obterTurno()->value === 'VESPERTINO' ? 'selected' : ''; ?>>Vespertino</option>
                                <option value="NOTURNO" <?= $turma->obterTurno()->value === 'NOTURNO' ? 'selected' : ''; ?>>Noturno</option>
                                <option value="INTEGRAL" <?= $turma->obterTurno()->value === 'INTEGRAL' ? 'selected' : ''; ?>>Integral</option>
                            </select>
                        </div>
                        <div>
                            <label for="config-modalidade" class="form-label required">Modalidade</label>
                            <select id="config-modalidade" name="modalidade" class="form-select" required>
                                <option value="PRESENCIAL" <?= $turma->obterModalidade()->value === 'PRESENCIAL' ? 'selected' : ''; ?>>Presencial</option>
                                <option value="REMOTA" <?= $turma->obterModalidade()->value === 'REMOTA' ? 'selected' : ''; ?>>Remota</option>
                                <option value="HIBRIDA" <?= $turma->obterModalidade()->value === 'HIBRIDA' ? 'selected' : ''; ?>>Hibrida</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" class="button-secondary">Cancelar</button>
                        <button type="submit" class="button-primary">Salvar alteracoes</button>
                    </div>
                </form>
            </div>
        </section>
        <?php endif; ?>
    </main>
</div>

<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const turmaId = <?= $turma->obterId(); ?>;

    // Sistema de navegacao por abas
    const tabButtons = document.querySelectorAll('.tab-nav-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = this.dataset.tabTarget;
            
            // Remove active de todos
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-sky-600', 'border-sky-600');
                btn.classList.add('text-gray-500');
            });
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Adiciona active no clicado
            this.classList.add('active', 'text-sky-600', 'border-sky-600');
            this.classList.remove('text-gray-500');
            document.getElementById('tab-' + target).classList.remove('hidden');

            // Carrega dados da aba se necessario
            if (target === 'alunos') {
                carregarAlunos();
            }
        });
    });

    // Funcao para carregar alunos
    function carregarAlunos() {
        fetch(`/turmas/${turmaId}/alunos`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('container-alunos');
                if (data.status === 'sucesso' && data.data.length > 0) {
                    let html = '<div class="divide-y divide-gray-200">';
                    data.data.forEach(aluno => {
                        html += `
                            <div class="py-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center">
                                        <span class="material-icons-sharp text-sky-600">person</span>
                                    </div>
                                    <div>
                                        <p class="font-medium">${aluno.nome}</p>
                                        <p class="text-sm text-gray-500">Matricula: ${aluno.matricula}</p>
                                    </div>
                                </div>
                                <?php if ($permissoes['remover_alunos']): ?>
                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removerAluno(${aluno.id})">
                                    <span class="material-icons-sharp">person_remove</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-gray-500"><span class="material-icons-sharp text-5xl text-gray-300">people_outline</span><p class="mt-2">Nenhum aluno matriculado</p></div>';
                }
            })
            .catch(error => {
                console.error('Erro ao carregar alunos:', error);
            });
    }

    // Funcoes para acoes
    window.editarTurma = function() {
        window.location.href = '/turmas/<?= $turma->obterId(); ?>/editar';
    };

    window.arquivarTurma = function() {
        if (confirm('Tem certeza que deseja arquivar esta turma?')) {
            // TODO: Implementar
        }
    };

    window.confirmarTurma = function() {
        if (confirm('Tem certeza que deseja confirmar esta turma?')) {
            // TODO: Implementar
        }
    };

    window.finalizarTurma = function() {
        if (confirm('Tem certeza que deseja finalizar esta turma?')) {
            // TODO: Implementar
        }
    };

    window.liberarTurma = function() {
        if (confirm('Tem certeza que deseja liberar esta turma para inscricoes?')) {
            // TODO: Implementar
        }
    };

    window.adicionarAlunos = function() {
        // TODO: Abrir modal para adicionar alunos
        alert('Funcionalidade em desenvolvimento');
    };

    window.removerAluno = function(alunoId) {
        if (confirm('Tem certeza que deseja remover este aluno da turma?')) {
            // TODO: Implementar remocao
        }
    };
});
</script>

<style>
.tab-nav-button.active {
    @apply text-sky-600 border-sky-600;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
