<div class="min-h-screen bg-gray-50">
    <!-- Header da Turma -->
    <header class="bg-gradient-to-r from-emerald-600 to-green-700 text-white shadow-lg">
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

    <!-- Navegação por Abas -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex overflow-x-auto scrollbar-hide">
                <button type="button" data-tab-target="mural" class="tab-nav-button active whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">dashboard</span>
                    Mural
                </button>

                <button type="button" data-tab-target="alunos" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">people</span>
                    Alunos
                </button>

                <button type="button" data-tab-target="frequencias" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">checklist</span>
                    Frequências
                </button>

                <button type="button" data-tab-target="notas" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">assignment</span>
                    Notas
                </button>

                <button type="button" data-tab-target="pauta" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">description</span>
                    Pauta Eletrônica
                </button>

                <button type="button" data-tab-target="conteudos" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">library_books</span>
                    Conteúdos
                </button>

                <button type="button" data-tab-target="horarios" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">access_time</span>
                    Horários
                </button>
            </div>
        </div>
    </nav>

    <!-- Conteúdo das Abas -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?= flash()->exibir(); ?>

        <!-- Aba: Mural -->
        <section id="tab-mural" class="tab-content active">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna Principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informações da Turma -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <span class="material-icons-sharp text-emerald-600">info</span>
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
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Alunos Matriculados</p>
                                <p class="font-medium"><?= $turma->obterQuantidadeInscricoes(); ?> / <?= $turma->obterCapacidadeMaxima(); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <span class="material-icons-sharp text-blue-600">bolt</span>
                            Ações Rápidas
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" data-tab-target="frequencias" class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-all">
                                <span class="material-icons-sharp text-3xl text-emerald-600 mb-2">checklist</span>
                                <span class="text-sm font-medium">Lançar Frequência</span>
                            </button>
                            <button type="button" data-tab-target="notas" class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all">
                                <span class="material-icons-sharp text-3xl text-blue-600 mb-2">assignment</span>
                                <span class="text-sm font-medium">Lançar Notas</span>
                            </button>
                            <button type="button" data-tab-target="alunos" class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition-all">
                                <span class="material-icons-sharp text-3xl text-purple-600 mb-2">people</span>
                                <span class="text-sm font-medium">Gerenciar Alunos</span>
                            </button>
                            <button type="button" data-tab-target="pauta" class="flex flex-col items-center p-4 rounded-lg border-2 border-gray-200 hover:border-amber-500 hover:bg-amber-50 transition-all">
                                <span class="material-icons-sharp text-3xl text-amber-600 mb-2">description</span>
                                <span class="text-sm font-medium">Ver Pauta</span>
                            </button>
                        </div>
                    </div>

                    <!-- Avisos Importantes -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <span class="material-icons-sharp text-amber-600">campaign</span>
                            Avisos Importantes
                        </h2>
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-icons-sharp text-5xl text-gray-300">notifications_none</span>
                            <p class="mt-2">Nenhum aviso no momento</p>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-6">
                    <!-- Estatísticas da Turma -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Estatísticas</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Taxa de Ocupação</span>
                                <span class="font-semibold text-emerald-600"><?= number_format($turma->calcularPercentualOcupacao(), 1); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-emerald-600 h-2 rounded-full" style="width: <?= $turma->calcularPercentualOcupacao(); ?>%"></div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Alunos Matriculados</span>
                                    <span class="font-semibold"><?= $turma->obterQuantidadeInscricoes(); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Vagas Disponíveis</span>
                                    <span class="font-semibold"><?= $turma->obterVagasDisponiveis(); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Próximas Atividades -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Próximas Atividades</h2>
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-icons-sharp text-5xl text-gray-300">event</span>
                            <p class="mt-2 text-sm">Nenhuma atividade agendada</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Alunos -->
        <section id="tab-alunos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-purple-600">people</span>
                        Lista de Alunos
                    </h2>
                    <?php if ($permissoes['adicionar_alunos']): ?>
                    <button type="button" class="button-primary inline-flex items-center gap-2">
                        <span class="material-icons-sharp">person_add</span>
                        Adicionar Aluno
                    </button>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">groups</span>
                        <p class="mt-4 text-lg">Nenhum aluno matriculado</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Frequências -->
        <section id="tab-frequencias" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-green-600">checklist</span>
                        Controle de Frequências
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">event_available</span>
                        <p class="mt-4 text-lg">Nenhuma frequência registrada</p>
                        <p class="text-sm">Inicie o lançamento de frequências para esta turma</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Notas -->
        <section id="tab-notas" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-blue-600">assignment</span>
                        Lançamento de Notas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">grade</span>
                        <p class="mt-4 text-lg">Nenhuma avaliação configurada</p>
                        <p class="text-sm">Configure os critérios de avaliação para iniciar</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Pauta Eletrônica -->
        <section id="tab-pauta" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-amber-600">description</span>
                        Pauta Eletrônica
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">article</span>
                        <p class="mt-4 text-lg">Pauta não disponível</p>
                        <p class="text-sm">A pauta será gerada quando houver registros de notas e frequências</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Conteúdos -->
        <section id="tab-conteudos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-purple-600">library_books</span>
                        Conteúdos Lecionados
                    </h2>
                    <button type="button" class="button-primary inline-flex items-center gap-2">
                        <span class="material-icons-sharp">add</span>
                        Adicionar Conteúdo
                    </button>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">article</span>
                        <p class="mt-4 text-lg">Nenhum conteúdo registrado</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Horários -->
        <section id="tab-horarios" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-indigo-600">access_time</span>
                        Grade de Horários
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">schedule</span>
                        <p class="mt-4 text-lg">Horários não configurados</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Gerenciar navegação entre abas
        const tabButtons = document.querySelectorAll('.tab-nav-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const target = this.getAttribute('data-tab-target');

                // Remover active de todos os botões e conteúdos
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-emerald-600', 'text-emerald-600');
                    btn.classList.add('text-gray-500');
                });
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });

                // Adicionar active ao botão clicado e ao conteúdo correspondente
                this.classList.add('active', 'border-emerald-600', 'text-emerald-600');
                this.classList.remove('text-gray-500');
                
                const targetContent = document.getElementById('tab-' + target);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                    targetContent.classList.add('active');
                }
            });
        });

        // Ativar primeira aba por padrão
        if (tabButtons.length > 0) {
            tabButtons[0].classList.add('border-emerald-600', 'text-emerald-600');
            tabButtons[0].classList.remove('text-gray-500');
        }
    });
</script>
