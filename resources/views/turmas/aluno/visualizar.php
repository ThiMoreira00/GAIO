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

                <button type="button" data-tab-target="notas" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">assignment</span>
                    Notas
                </button>

                <button type="button" data-tab-target="frequencias" class="tab-nav-button whitespace-nowrap px-6 py-4 text-sm font-medium border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700 transition-colors flex items-center gap-2">
                    <span class="material-icons-sharp">checklist</span>
                    Frequências
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
                            <span class="material-icons-sharp text-sky-600">info</span>
                            Informações da turma
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

                    <!-- Próximas Avaliações -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <span class="material-icons-sharp text-purple-600">assignment</span>
                            Próximas avaliações
                        </h2>
                        <div class="text-center py-8 text-gray-500">
                            <span class="material-icons-sharp text-5xl text-gray-300">event_available</span>
                            <p class="mt-2">Nenhuma avaliação agendada</p>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-6">
                    <!-- Estatísticas Pessoais -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Meu desempenho</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Frequência</span>
                                <span class="font-semibold text-green-600">---%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                            
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-sm text-gray-600">Média parcial</span>
                                <span class="font-semibold text-blue-600">---</span>
                            </div>
                        </div>
                    </div>

                    <!-- Atalhos Rápidos -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4">Atalhos Rápidos</h2>
                        <div class="space-y-2">
                            <button type="button" data-tab-target="notas" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 flex items-center gap-3 transition-colors">
                                <span class="material-icons-sharp text-blue-600">assignment</span>
                                <span class="text-sm font-medium">Ver Notas</span>
                            </button>
                            <button type="button" data-tab-target="frequencias" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 flex items-center gap-3 transition-colors">
                                <span class="material-icons-sharp text-green-600">checklist</span>
                                <span class="text-sm font-medium">Ver Frequências</span>
                            </button>
                            <button type="button" data-tab-target="conteudos" class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 flex items-center gap-3 transition-colors">
                                <span class="material-icons-sharp text-purple-600">library_books</span>
                                <span class="text-sm font-medium">Conteúdos</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Minhas Notas -->
        <section id="tab-minhas-notas" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-blue-600">assignment</span>
                        Minhas Notas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">grade</span>
                        <p class="mt-4 text-lg">Nenhuma nota lançada</p>
                        <p class="text-sm">Suas notas aparecerão aqui quando forem lançadas pelo professor</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Minhas Frequências -->
        <section id="tab-minhas-frequencias" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-green-600">checklist</span>
                        Minhas Frequências
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">event_available</span>
                        <p class="mt-4 text-lg">Nenhuma frequência registrada</p>
                        <p class="text-sm">Seu histórico de frequências aparecerá aqui</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aba: Conteúdos -->
        <section id="tab-conteudos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="material-icons-sharp text-purple-600">library_books</span>
                        Conteúdos da Disciplina
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">article</span>
                        <p class="mt-4 text-lg">Nenhum conteúdo disponível</p>
                        <p class="text-sm">Materiais e recursos serão disponibilizados pelo professor</p>
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
                        Horários das Aulas
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center py-12 text-gray-500">
                        <span class="material-icons-sharp text-6xl text-gray-300">schedule</span>
                        <p class="mt-4 text-lg">Horários não configurados</p>
                        <p class="text-sm">A grade de horários será exibida aqui quando configurada</p>
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
                    btn.classList.remove('active', 'border-sky-600', 'text-sky-600');
                    btn.classList.add('text-gray-500');
                });
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });

                // Adicionar active ao botão clicado e ao conteúdo correspondente
                this.classList.add('active', 'border-sky-600', 'text-sky-600');
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
            tabButtons[0].classList.add('border-sky-600', 'text-sky-600');
            tabButtons[0].classList.remove('text-gray-500');
        }
    });
</script>
