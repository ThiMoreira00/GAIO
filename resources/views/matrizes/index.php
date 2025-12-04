<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Matrizes Curriculares</h1>
    <div class="flex-shrink-0">
        <?php if ($permissoes['cadastrar']): ?>
        <button type="button" class="button-primary inline-flex items-center" data-modal-trigger="modal-matriz-adicionar-form" id="button-matriz-adicionar">
            <span class="material-icons-sharp -ml-1 mr-2">add</span>
            Adicionar nova matriz
        </button>
        <?php endif; ?>
    </div>
</header>

<main id="main-matrizes" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="matrizes-section-heading">
        <h2 id="matrizes-section-heading" class="sr-only">Matrizes Curriculares cadastradas</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-matriz-filtros">
                    <form id="form-filtros-matrizes" action="/matrizes-curriculares/filtrar" method="GET" data-tab-form>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="relative w-full sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-matriz" class="input-search" placeholder="Buscar por curso ou matriz..." data-tab-search>
                            </div>
                        </div>
                    </form>
                </section>

                <div id="container-matrizes" class="space-y-4">
                    <!-- Matrizes agrupadas por curso serão carregadas aqui -->
                </div>

                <div id="loader-matrizes" class="text-center mt-8 py-4" style="display: none;">
                    <span class="material-icons-sharp text-5xl text-gray-400 animate-spin">sync</span>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
    if ($permissoes['cadastrar']) {
        include __DIR__ . '/../templates/matriz-modal-adicionar.php';
    }
    if ($permissoes['editar']) {
        include __DIR__ . '/../templates/matriz-modal-editar.php';
    }
    if ($permissoes['inativar']) {
        include __DIR__ . '/../templates/matriz-modal-inativar.php';
    }
    if ($permissoes['validar']) {
        include __DIR__ . '/../templates/matriz-modal-validar.php';
    }
    include __DIR__ . '/../templates/matriz-lista-item.php';
?>

<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/modal.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script type="text/javascript">
    
    // Permissões do usuário
    const permissoes = {
        visualizar: <?= isset($permissoes['visualizar']) && $permissoes['visualizar'] ? 'true' : 'false' ?>,
        cadastrar: <?= isset($permissoes['cadastrar']) && $permissoes['cadastrar'] ? 'true' : 'false' ?>,
        editar: <?= isset($permissoes['editar']) && $permissoes['editar'] ? 'true' : 'false' ?>,
        inativar: <?= isset($permissoes['inativar']) && $permissoes['inativar'] ? 'true' : 'false' ?>,
        validar: <?= isset($permissoes['validar']) && $permissoes['validar'] ? 'true' : 'false' ?>
    };

    document.addEventListener('DOMContentLoaded', function() {
        
        // Elementos
        const buttonAdicionarMatriz = document.getElementById('button-matriz-adicionar');
        const containerMatrizes = document.getElementById('container-matrizes');
        const loaderMatrizes = document.getElementById('loader-matrizes');
        const buscaInput = document.getElementById('busca-matriz');

        // Templates
        const templateModalAdicionarMatriz = document.getElementById('template-matriz-modal-adicionar');
        const templateModalEditarMatriz = document.getElementById('template-matriz-modal-editar');
        const templateModalInativarMatriz = document.getElementById('template-matriz-modal-inativar');
        const templateModalValidarMatriz = document.getElementById('template-matriz-modal-validar');

        let matrizesData = [];
        let cursosData = [];

        // Função para carregar matrizes
        async function carregarMatrizes() {
            try {
                loaderMatrizes.style.display = 'block';
                const response = await fetch('/matrizes-curriculares/obter', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (data.sucesso) {
                    cursosData = data.cursos;
                    renderizarMatrizes(cursosData);
                } else {
                    notificador.erro('Erro ao carregar matrizes curriculares', null, { alvo: '#main-matrizes' });
                }
            } catch (error) {
                console.error('Erro ao carregar matrizes:', error);
                notificador.erro('Erro ao carregar matrizes curriculares', null, { alvo: '#main-matrizes' });
            } finally {
                loaderMatrizes.style.display = 'none';
            }
        }

        // Função para renderizar matrizes agrupadas por curso (accordion)
        function renderizarMatrizes(cursos) {
            containerMatrizes.innerHTML = '';

            if (cursos.length === 0) {
                containerMatrizes.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhuma matriz curricular encontrada.</p>';
                return;
            }

            cursos.forEach((curso, index) => {
                const cursoDiv = document.createElement('div');
                cursoDiv.className = 'border border-gray-200 rounded-lg overflow-hidden';

                const header = document.createElement('button');
                header.className = 'w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors';
                header.setAttribute('data-accordion-toggle', `accordion-${curso.id}`);
                header.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="material-icons-sharp text-sky-600">school</span>
                        <div class="text-left">
                            <h3 class="font-semibold text-gray-900">${curso.nome}</h3>
                            <p class="text-sm text-gray-500">${curso.matrizes.length} matriz${curso.matrizes.length !== 1 ? 'es' : ''} curricular${curso.matrizes.length !== 1 ? 'es' : ''}</p>
                        </div>
                    </div>
                    <span class="material-icons-sharp text-gray-400 transition-transform duration-200" data-accordion-icon>expand_more</span>
                `;

                const content = document.createElement('div');
                content.id = `accordion-${curso.id}`;
                content.className = 'accordion-content';
                content.style.display = 'none';

                const matrizesHtml = curso.matrizes.map(matriz => {
                    const statusClass = matriz.status === 'Vigente' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                    return `
                        <div class="p-4 border-t border-gray-200 hover:bg-gray-50 transition-colors matriz-item" data-matriz-id="${matriz.id}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-medium text-gray-900">Matriz Curricular #${matriz.id}</span>
                                        <span class="text-xs font-bold inline-flex items-center px-2 py-1 rounded-full ${statusClass}">${matriz.status.toUpperCase()}</span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><span class="material-icons-sharp text-xs align-middle">calendar_today</span> ${matriz.quantidade_periodos} períodos</p>
                                        <p><span class="material-icons-sharp text-xs align-middle">event</span> Criada em ${matriz.created_at || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${permissoes.visualizar ? `
                                    <a href="/matrizes-curriculares/visualizar/${matriz.id}" class="button-secondary-sm">
                                        <span class="material-icons-sharp text-sm">visibility</span>
                                        Visualizar
                                    </a>
                                    ` : ''}
                                    ${matriz.status !== 'Arquivado' && (permissoes.editar || permissoes.validar || permissoes.inativar) ? `
                                        <div class="relative dropdown">
                                            <button type="button" class="button-secondary-sm dropdown-toggle" data-dropdown-toggle>
                                                <span class="material-icons-sharp text-sm">more_vert</span>
                                            </button>
                                            <div class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                                                ${permissoes.editar ? `
                                                <a href="#" class="dropdown-item" data-action="editar" data-matriz-id="${matriz.id}">
                                                    <span class="material-icons-sharp text-sm">edit</span>
                                                    Editar
                                                </a>
                                                ` : ''}
                                                ${permissoes.validar ? `
                                                <a href="#" class="dropdown-item" data-action="validar" data-matriz-id="${matriz.id}">
                                                    <span class="material-icons-sharp text-sm">check_circle</span>
                                                    Validar
                                                </a>
                                                ` : ''}
                                                ${permissoes.inativar ? `
                                                <a href="#" class="dropdown-item text-red-600 hover:bg-red-50" data-action="inativar" data-matriz-id="${matriz.id}">
                                                    <span class="material-icons-sharp text-sm">block</span>
                                                    Inativar
                                                </a>
                                                ` : ''}
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                content.innerHTML = matrizesHtml;

                cursoDiv.appendChild(header);
                cursoDiv.appendChild(content);
                containerMatrizes.appendChild(cursoDiv);

                // Event listener para accordion
                header.addEventListener('click', function() {
                    const isOpen = content.style.display !== 'none';
                    content.style.display = isOpen ? 'none' : 'block';
                    const icon = header.querySelector('[data-accordion-icon]');
                    icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
                });

                // Abrir o primeiro accordion por padrão
                if (index === 0) {
                    content.style.display = 'block';
                    const icon = header.querySelector('[data-accordion-icon]');
                    icon.style.transform = 'rotate(180deg)';
                }
            });

            // Adicionar event listeners para dropdowns
            inicializarDropdowns();
        }

        // Função para inicializar dropdowns
        function inicializarDropdowns() {
            const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
            
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdown = this.closest('.dropdown');
                    const menu = dropdown.querySelector('.dropdown-menu');
                    
                    // Fechar outros dropdowns
                    document.querySelectorAll('.dropdown-menu').forEach(m => {
                        if (m !== menu) m.classList.add('hidden');
                    });
                    
                    menu.classList.toggle('hidden');
                });
            });

            // Fechar dropdown ao clicar fora
            document.addEventListener('click', function() {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            });
        }

        // Busca
        if (buscaInput) {
            buscaInput.addEventListener('input', function() {
                const busca = this.value.toLowerCase();
                const cursosFiltrados = cursosData.filter(curso => {
                    return curso.nome.toLowerCase().includes(busca) || 
                           curso.sigla?.toLowerCase().includes(busca);
                });
                renderizarMatrizes(cursosFiltrados);
            });
        }

        // Modal Adicionar Matriz
        if (templateModalAdicionarMatriz) {
            const cloneModalAdicionarMatriz = templateModalAdicionarMatriz.content.cloneNode(true);
            document.body.appendChild(cloneModalAdicionarMatriz);

            var modalAdicionarMatriz = new Modal('#matriz-modal-adicionar');

            if (buttonAdicionarMatriz) {
                buttonAdicionarMatriz.addEventListener('click', function() {
                    modalAdicionarMatriz.abrir();
                });
            }

            document.getElementById('matriz-modal-adicionar').addEventListener('fechar', function() {
                modalAdicionarMatriz.limparCampos();
                modalAdicionarMatriz.fechar();
            });

            var formularioAdicionarMatriz = new Formulario('#matriz-form-adicionar', {
                onSuccess: function(response) {
                    modalAdicionarMatriz.limparCampos();
                    modalAdicionarMatriz.fechar();
                    carregarMatrizes();
                    notificador.sucesso('Matriz curricular adicionada com sucesso!', null, { alvo: '#main-matrizes' });
                },
                notificador: {
                    status: true,
                    alvo: '#matriz-form-adicionar'
                }
            });

            templateModalAdicionarMatriz.remove();
        }

        // Modal Editar Matriz
        if (templateModalEditarMatriz) {
            const cloneModalEditarMatriz = templateModalEditarMatriz.content.cloneNode(true);
            document.body.appendChild(cloneModalEditarMatriz);

            var modalEditarMatriz = new Modal('#matriz-modal-editar');

            document.getElementById('matriz-modal-editar').addEventListener('fechar', function() {
                modalEditarMatriz.limparCampos();
                modalEditarMatriz.fechar();
            });

            var formularioEditarMatriz = new Formulario('#matriz-form-editar', {
                onSuccess: function(response) {
                    modalEditarMatriz.fechar();
                    carregarMatrizes();
                    notificador.sucesso('Matriz curricular editada com sucesso!', null, { alvo: '#main-matrizes' });
                },
                notificador: {
                    status: true,
                    alvo: '#matriz-form-editar'
                }
            });

            templateModalEditarMatriz.remove();
        }

        // Modal Inativar Matriz
        if (templateModalInativarMatriz) {
            const cloneModalInativarMatriz = templateModalInativarMatriz.content.cloneNode(true);
            document.body.appendChild(cloneModalInativarMatriz);

            var modalInativarMatriz = new Modal('#matriz-modal-inativar');

            document.getElementById('matriz-modal-inativar').addEventListener('fechar', function() {
                modalInativarMatriz.limparCampos();
                modalInativarMatriz.fechar();
            });

            var formularioInativarMatriz = new Formulario('#matriz-form-inativar', {
                onSuccess: function(response) {
                    modalInativarMatriz.fechar();
                    carregarMatrizes();
                    notificador.sucesso('Matriz curricular inativada com sucesso!', null, { alvo: '#main-matrizes' });
                },
                notificador: {
                    status: true,
                    alvo: '#matriz-form-inativar'
                }
            });

            templateModalInativarMatriz.remove();
        }

        // Modal Validar Matriz
        if (templateModalValidarMatriz) {
            const cloneModalValidarMatriz = templateModalValidarMatriz.content.cloneNode(true);
            document.body.appendChild(cloneModalValidarMatriz);

            var modalValidarMatriz = new Modal('#matriz-modal-validar');

            document.getElementById('matriz-modal-validar').addEventListener('fechar', function() {
                modalValidarMatriz.limparCampos();
                modalValidarMatriz.fechar();
            });

            var formularioValidarMatriz = new Formulario('#matriz-form-validar', {
                onSuccess: function(response) {
                    modalValidarMatriz.fechar();
                    carregarMatrizes();
                    notificador.sucesso('Matriz curricular validada com sucesso!', null, { alvo: '#main-matrizes' });
                },
                notificador: {
                    status: true,
                    alvo: '#matriz-form-validar'
                }
            });

            templateModalValidarMatriz.remove();
        }

        // Event listeners para ações
        containerMatrizes.addEventListener('click', async function(event) {
            const buttonEditar = event.target.closest('[data-action="editar"]');
            const buttonInativar = event.target.closest('[data-action="inativar"]');
            const buttonValidar = event.target.closest('[data-action="validar"]');

            if (buttonEditar) {
                event.preventDefault();
                const matrizId = buttonEditar.getAttribute('data-matriz-id');
                
                try {
                    const response = await fetch(`/matrizes-curriculares/${matrizId}/obter`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();

                    if (data.sucesso && modalEditarMatriz) {
                        const form = document.querySelector('#matriz-form-editar');
                        form.action = `/matrizes-curriculares/${matrizId}/editar`;
                        form.querySelector('input[name="id"]').value = data.matriz.id;
                        form.querySelector('input[name="quantidade_periodos"]').value = data.matriz.quantidade_periodos;
                        form.querySelector('#matriz-curso-nome-editar').textContent = data.matriz.curso.nome;

                        modalEditarMatriz.abrir();
                    }
                } catch (error) {
                    console.error('Erro ao carregar matriz:', error);
                    notificador.erro('Erro ao carregar dados da matriz', null, { alvo: '#main-matrizes' });
                }
            }

            if (buttonInativar) {
                event.preventDefault();
                const matrizId = buttonInativar.getAttribute('data-matriz-id');

                try {
                    const response = await fetch(`/matrizes-curriculares/${matrizId}/obter`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();

                    if (data.sucesso && modalInativarMatriz) {
                        const form = document.querySelector('#matriz-form-inativar');
                        form.action = `/matrizes-curriculares/${matrizId}/inativar`;
                        form.querySelector('input[name="id"]').value = data.matriz.id;
                        document.querySelector('#matriz-nome-inativar').textContent = `Matriz #${data.matriz.id} - ${data.matriz.curso.nome}`;

                        modalInativarMatriz.abrir();
                    }
                } catch (error) {
                    console.error('Erro ao carregar matriz:', error);
                    notificador.erro('Erro ao carregar dados da matriz', null, { alvo: '#main-matrizes' });
                }
            }

            if (buttonValidar) {
                event.preventDefault();
                const matrizId = buttonValidar.getAttribute('data-matriz-id');

                try {
                    const response = await fetch(`/matrizes-curriculares/${matrizId}/obter`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();

                    if (data.sucesso && modalValidarMatriz) {
                        const form = document.querySelector('#matriz-form-validar');
                        form.action = `/matrizes-curriculares/${matrizId}/validar`;
                        form.querySelector('input[name="id"]').value = data.matriz.id;
                        document.querySelector('#matriz-nome-validar').textContent = `Matriz #${data.matriz.id} - ${data.matriz.curso.nome}`;

                        modalValidarMatriz.abrir();
                    }
                } catch (error) {
                    console.error('Erro ao carregar matriz:', error);
                    notificador.erro('Erro ao carregar dados da matriz', null, { alvo: '#main-matrizes' });
                }
            }
        });

        // Carregar matrizes ao iniciar
        carregarMatrizes();
    });

</script>
