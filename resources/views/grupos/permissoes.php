<?php

use App\Models\Usuario;
use App\Helper\DataFormatador;

?>

<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Permissões dos Grupos</h1>
</header>

<main id="main-permissoes-grupos">
    <section class="bg-white p-8 border-b border-gray-200" aria-labelledby="permissoes-grupos-section-heading">
        <h2 id="permissoes-grupos-section-heading" class="sr-only">Permissões dos Grupos</h2>
        <div class="relative overflow-y-auto sm:rounded-lg">
            <?= flash()->exibir(); ?>

            <?php if (empty($grupos) || count($grupos) === 0): ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <span class="material-icons-sharp !text-6xl text-gray-300">group_off</span>
                    <p class="mt-4 text-gray-600 text-lg">O sistema não possui grupos cadastrados.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col md:flex-row md:h-[calc(100vh-12rem)]">
                    <aside id="grupos-coluna" class="w-full md:w-1/3 lg:w-1/4 border-b md:border-b-0 md:border-r border-gray-200 max-h-[40vh] md:max-h-none md:overflow-y-auto">
                        <div class="p-4 md:p-6">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3">Grupos</h3>
                            <button type="button" class="button-secondary w-full mb-4 text-sm md:text-base" id="criar-grupo-button">
                                <svg class="-ml-1 mr-2 h-4 w-4 md:h-5 md:w-5 text-gray-500" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Criar novo grupo
                            </button>
                            <nav id="grupos-lista" class="space-y-1 overflow-y-auto max-h-[25vh] md:max-h-none" aria-label="Grupos">
                                <!-- Grupos serão renderizados via JS -->
                            </nav>
                        </div>
                    </aside>

                    <div id="permissoes-coluna" class="flex-1 opacity-25 flex flex-col">
                        <div class="px-4 md:px-6 pt-4 md:pt-6 pb-3 md:pb-4 bg-white sticky top-0 z-10 border-b border-gray-200">
                            <h3 id="titulo-permissoes" class="text-base md:text-lg font-semibold text-gray-900">
                                Permissões do grupo <span id="grupo-nome"></span>
                            </h3>
                        </div>
                        <div class="p-4 md:p-6 pt-3 md:pt-4 flex-1 overflow-y-auto">
                            <form id="form-permissoes" action="/grupos/permissoes/salvar" method="POST">
                                <input type="hidden" name="grupo_id" id="grupo-id-input" value="">
                                <div id="container-permissoes">
                                    <p class="text-gray-500 text-sm md:text-base">Carregando permissões...</p>
                                </div>
                            </form>
                        </div>
                        <div id="acoes-permissoes" class="px-4 md:px-6 py-3 md:py-4 border-t border-gray-200 bg-gray-50 hidden sticky bottom-0">
                            <div class="flex justify-end items-center">
                                <button type="submit" form="form-permissoes" class="button-primary">
                                    Salvar alterações
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/../templates/modal-grupo-criar.php';
    include __DIR__ . '/../templates/modal-grupo-excluir.php';
    include __DIR__ . '/../templates/lista-item-grupo.php';
    include __DIR__ . '/../templates/permissao-lista-item.php';
?>

<!-- Scripts -->
<script src="<?= obterURL('/assets/js/notificador-flash.js'); ?>"></script>
<script src="<?= obterURL('/assets/js/formulario.js'); ?>"></script>
<script src="<?= obterURL('/assets/js/sair-sem-salvar.js'); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gruposLista = document.getElementById('grupos-lista');
        const containerPermissoes = document.getElementById('container-permissoes');
        const colunaPermissoes = document.getElementById('permissoes-coluna');
        const inputGrupoId = document.getElementById('grupo-id-input');
        const grupoNomeSpan = document.getElementById('grupo-nome');
        const acoesPermissoes = document.getElementById('acoes-permissoes');

        // TEMPLATES
        const templateItemGrupo = document.getElementById('template-lista-item-grupo');
        const templateItemPermissao = document.getElementById('template-permissao-lista-item');
        const templateModalGrupoCriar = document.getElementById('template-modal-grupo-criar');
        const templateModalGrupoExcluir = document.getElementById('template-modal-grupo-excluir');

        // Adiciona o modal ao body
        document.body.appendChild(templateModalGrupoCriar.content.cloneNode(true));
        document.body.appendChild(templateModalGrupoExcluir.content.cloneNode(true));

        const modalGrupoExcluir = document.getElementById('modal-excluir-grupo');
        const grupoExcluirNome = document.getElementById('grupo-excluir-nome');
        const formExcluir = document.getElementById('form-excluir-grupo');
        const inputGrupoExcluirId = document.getElementById('grupo-excluir-id');

        // Modal Excluir
        function abrirmodalGrupoExcluir(grupoId, grupoNome) {
            inputGrupoExcluirId.value = grupoId;
            grupoExcluirNome.textContent = grupoNome;
            formExcluir.action = `/grupos/${grupoId}/excluir`;

            modalGrupoExcluir.style.display = 'flex';
        }

        function fecharmodalGrupoExcluir() {
            modalGrupoExcluir.style.display = 'none';
            inputGrupoExcluirId.value = '';
        }

        function fecharModalCriar() {
            document.getElementById('modal-container').style.display = 'none';
            document.getElementById('grupo-nome').value = '';
            document.getElementById('grupo-descricao').value = '';
        }

        document.getElementById('btn-cancelar-exclusao')?.addEventListener('click', fecharmodalGrupoExcluir);

        // Inicializar Formulario para exclusão
        new Formulario({
            formId: 'form-excluir-grupo',
            notificador: false,
            onError: function(erro) {
                notificador.erro(erro.mensagem || 'Erro ao excluir grupo.', null, { target: '#form-excluir-grupo' });
            },
            onSuccess: function(teste) {
                fecharmodalGrupoExcluir();
                notificador.sucesso(teste.mensagem, null, { target: '#main-permissoes-grupos' });
                carregarGrupos(); // Recarrega a lista de grupos
            }
        });

        // Modifica a função renderizarGrupos para adicionar o evento de exclusão
        function renderizarGrupos(grupos) {
            gruposLista.innerHTML = '';
            
            // Ordenar: primeiro grupos padrão, depois não padrão, ambos alfabeticamente
            const gruposOrdenados = grupos.sort((a, b) => {
                if (a.padrao && !b.padrao) return -1;
                if (!a.padrao && b.padrao) return 1;
                return a.nome.localeCompare(b.nome);
            });
            
            gruposOrdenados.forEach(grupo => {
                const clone = templateItemGrupo.content.cloneNode(true);
                const link = clone.querySelector('.item-grupo');
                const nome = clone.querySelector('.grupo-nome');
                const excluirBtn = clone.querySelector('.btn-excluir');

                link.dataset.grupoId = grupo.id;
                nome.textContent = grupo.nome;

                if (!grupo.padrao) {
                    excluirBtn.classList.remove('hidden');
                    excluirBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        abrirmodalGrupoExcluir(grupo.id, grupo.nome);
                    });
                }

                link.addEventListener('click', e => {
                    e.preventDefault();
                    carregarPermissoes(grupo.id);
                    marcarComoSelecionado(link);
                });

                gruposLista.appendChild(clone);
            });

            // Seleciona primeiro grupo automaticamente
            const primeiro = gruposLista.querySelector('.item-grupo');
            if (primeiro) {
                marcarComoSelecionado(primeiro);
                carregarPermissoes(primeiro.dataset.grupoId);
            }
        }


        // Renderizar permissões
        function renderizarPermissoes(permissoes) {
            containerPermissoes.innerHTML = '';
            
            // Agrupar permissões por categoria
            const categorias = {};
            permissoes.forEach(permissao => {
                const categoria = permissao.categoria || 'Outros';
                if (!categorias[categoria]) {
                    categorias[categoria] = [];
                }
                categorias[categoria].push(permissao);
            });
            
            // Renderizar categorias
            Object.keys(categorias).sort().forEach((categoria, index) => {
                const categoriaDiv = document.createElement('div');
                categoriaDiv.className = index > 0 ? 'mt-4' : '';
                
                // Ordenar permissões: "gerenciar" primeiro, depois alfabeticamente
                const permissoesOrdenadas = categorias[categoria].sort((a, b) => {
                    const aGerenciar = a.nome.toLowerCase().includes('gerenciar');
                    const bGerenciar = b.nome.toLowerCase().includes('gerenciar');
                    if (aGerenciar && !bGerenciar) return -1;
                    if (!aGerenciar && bGerenciar) return 1;
                    return a.nome.localeCompare(b.nome);
                });
                
                // Header da categoria com link "selecionar todos"
                const categoriaHeader = document.createElement('div');
                categoriaHeader.className = 'flex items-center justify-between mb-1.5 pb-1 border-b border-gray-200';
                
                const categoriaTitulo = document.createElement('h4');
                categoriaTitulo.className = 'text-xs font-semibold text-gray-700 uppercase tracking-wide';
                categoriaTitulo.textContent = categoria;
                
                const selecionarTodosLink = document.createElement('a');
                selecionarTodosLink.href = '#';
                selecionarTodosLink.className = 'text-xs text-sky-600 hover:text-sky-800 font-medium';
                selecionarTodosLink.textContent = 'Selecionar todos';
                selecionarTodosLink.dataset.categoria = categoria;
                
                categoriaHeader.appendChild(categoriaTitulo);
                categoriaHeader.appendChild(selecionarTodosLink);
                
                const permissoesContainer = document.createElement('div');
                permissoesContainer.className = 'space-y-1';
                permissoesContainer.dataset.categoria = categoria;
                
                let checkboxGerenciar = null;
                const checkboxesOutras = [];
                
                // Adicionar permissões da categoria
                permissoesOrdenadas.forEach(permissao => {
                    const clone = templateItemPermissao.content.cloneNode(true);
                    const nomeElem = clone.querySelector('.permissao-nome');
                    const descElem = clone.querySelector('.permissao-desc');
                    const checkbox = clone.querySelector('.permissao-checkbox');
                    
                    nomeElem.textContent = permissao.nome;
                    descElem.textContent = permissao.descricao;
                    checkbox.value = permissao.codigo;
                    checkbox.checked = permissao.status;
                    checkbox.dataset.categoria = categoria;
                    
                    // Se for permissão de gerenciar
                    if (permissao.nome.toLowerCase().includes('gerenciar')) {
                        checkboxGerenciar = checkbox;
                        
                        // Adiciona aviso vermelho
                        const aviso = document.createElement('p');
                        aviso.className = 'text-xs text-red-600 font-medium mt-1';
                        aviso.textContent = 'Esta permissão sobrepõe todas as outras desta categoria. Tenha cuidado ao habilitá-la.';
                        descElem.insertAdjacentElement('afterend', aviso);
                    } else {
                        checkboxesOutras.push(checkbox);
                    }
                    
                    permissoesContainer.appendChild(clone);
                });
                
                // Event listener para o checkbox de gerenciar
                if (checkboxGerenciar) {
                    checkboxGerenciar.addEventListener('change', function() {
                        checkboxesOutras.forEach(cb => {
                            cb.disabled = this.checked;
                            if (this.checked) {
                                // cb.checked = false;
                                cb.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
                            } else {
                                cb.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        });
                    });
                    
                    // Aplicar estado inicial se gerenciar estiver marcado
                    if (checkboxGerenciar.checked) {
                        checkboxesOutras.forEach(cb => {
                            cb.disabled = true;
                            // cb.checked = false;
                            cb.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
                        });
                    }
                }
                
                // Event listener para "selecionar todos"
                selecionarTodosLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const checkboxes = permissoesContainer.querySelectorAll('.permissao-checkbox:not(:disabled)');
                    const todosChecked = Array.from(checkboxes).every(cb => cb.checked);
                    
                    checkboxes.forEach(cb => {
                        cb.checked = !todosChecked;
                    });
                    
                    this.textContent = todosChecked ? 'Selecionar todos' : 'Desmarcar todos';
                });
                
                categoriaDiv.appendChild(categoriaHeader);
                categoriaDiv.appendChild(permissoesContainer);
                containerPermissoes.appendChild(categoriaDiv);
            });
            
            acoesPermissoes.classList.remove('hidden');
        }

        // Marcar grupo ativo
        function marcarComoSelecionado(elem) {
            gruposLista.querySelectorAll('.item-grupo').forEach(e => {
                e.setAttribute('data-active', 'false');
            });
            elem.setAttribute('data-active', 'true');
        }

        // AJAX: carregar grupos
        function carregarGrupos() {
            $.get('/grupos/obter')
                .done(function(response) {
                    if (response.grupos.length === 0) {
                        gruposLista.innerHTML = '<p class="text-gray-500">Nenhum grupo encontrado.</p>';
                        return;
                    }
                    renderizarGrupos(response.grupos);
                })
                .fail(function() {
                   gruposLista.innerHTML = '<p class="text-red-500">Erro ao carregar grupos.</p>';
                });
        }

        // AJAX: carregar permissões
        function carregarPermissoes(grupoId) {
            colunaPermissoes.classList.add('opacity-25');
            $.get(`/grupos/${grupoId}/permissoes/obter`)
                .done(function(response) {
                    colunaPermissoes.classList.remove('opacity-25');
                    grupoNomeSpan.textContent = response.grupo.nome;
                    inputGrupoId.value = response.grupo.id;
                    renderizarPermissoes(response.grupo.permissoes);
                })
                .fail(function() {
                    colunaPermissoes.classList.remove('opacity-25');
                    containerPermissoes.innerHTML = '<p class="text-red-500">Erro ao carregar permissões.</p>';
                    acoesPermissoes.classList.add('hidden');
                });
        }

        // Inicialização
        carregarGrupos();
        new Formulario({
            formId: 'form-criar-grupo',
            notificador: false,
            onError: function(erro) {
                notificador.erro(erro.mensagem || 'Erro ao criar grupo.', null, { target: '#form-criar-grupo' });
            },
            onSuccess: function(response) {
                fecharModalCriar();
                notificador.sucesso(response.mensagem, null, { target: '#main-permissoes-grupos' });
                carregarGrupos();
            }
        });
        new Formulario({ formId: 'form-permissoes', notificador: true });


        document.getElementById('criar-grupo-button')?.addEventListener('click', () => {
            document.getElementById('modal-container').style.display = 'flex';
            document.getElementById('grupo-nome').focus();
        });
        document.getElementById('button-cancelar')?.addEventListener('click', () => {
            document.getElementById('modal-container').style.display = 'none';
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.getElementById('modal-container').style.display = 'none';
            }
        });
        document.querySelectorAll('.button-close').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('modal-container').style.display = 'none';
            });
        })
    });

</script>