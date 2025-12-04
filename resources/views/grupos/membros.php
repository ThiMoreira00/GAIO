<?php

use App\Models\Usuario;
use App\Helper\DataFormatador;

?>

<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Membros dos Grupos</h1>
</header>

<main id="main-membros-grupos">
    <section class="bg-white p-8 border-b border-gray-200 min-h-max" aria-labelledby="membros-grupos-section-heading">
        <h2 id="membros-grupos-section-heading" class="sr-only">Membros dos Grupos</h2>
        <div class="relative overflow-x-auto sm:rounded-lg">
            <?= flash()->exibir(); ?>

            <?php if (empty($grupos) || count($grupos) === 0): ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <span class="material-icons-sharp !text-6xl text-gray-300">group_off</span>
                    <p class="mt-4 text-gray-600 text-lg">O sistema não possui grupos cadastrados.</p>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-6 overflow-hidden md:flex">
                    <aside id="grupos-coluna" class="w-full md:w-1/3 lg:w-1/4 border-r border-gray-200">
                        <div class="pr-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Grupos</h3>
                            <button type="button" class="button-secondary w-full mb-4" id="criar-grupo-button">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Criar novo grupo
                            </button>
                            <nav id="grupos-lista" class="space-y-1" aria-label="Grupos">
                            </nav>
                        </div>
                    </aside>

                    <div id="membros-coluna" class="flex-1 opacity-25 pl-8">
                        <h3 id="titulo-membros" class="text-lg font-semibold text-gray-900 mb-4">
                            Membros do grupo <span id="grupo-nome" class="font-bold"></span>
                        </h3>

                        <div id="cabecalho-membros" class="mb-4">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="relative flex-grow">
                                    <span class="material-icons-sharp text-gray-400 absolute inset-y-0 left-0 pl-3 !flex items-center">search</span>
                                    <input type="text" id="buscar-membros-input" class="form-input pl-10 w-full" placeholder="Buscar membros no grupo...">
                                </div>
                                <button type="button" id="btn-abrir-modal-adicionar" class="button-primary flex items-center">
                                    <span class="material-icons-sharp">person_add</span>
                                    Adicionar membros
                                </button>
                            </div>
                        </div>
                        <div id="container-membros" class="min-h-[500px] overflow-y-auto max-h-[600px]">
                            <p class="text-gray-500">Selecione um grupo para ver os membros.</p>
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
    include __DIR__ . '/../templates/modal-membro-adicionar.php';
    include __DIR__ . '/../templates/modal-membro-remover.php';
    include __DIR__ . '/../templates/lista-item-grupo.php';
    include __DIR__ . '/../templates/lista-item-membro.php';
    include __DIR__ . '/../templates/lista-item-membro-consulta.php';
?>

<script src="<?= obterURL('/assets/js/notificador-flash.js'); ?>"></script>
<script src="<?= obterURL('/assets/js/formulario.js'); ?>"></script>
<script src="<?= obterURL('/assets/js/sair-sem-salvar.js'); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Elementos DOM ---
        const gruposLista = document.getElementById('grupos-lista');
        const containerMembros = document.getElementById('container-membros');
        const colunaMembros = document.getElementById('membros-coluna');
        const cabecalhoMembros = document.getElementById('cabecalho-membros');
        const grupoNomeSpan = document.getElementById('grupo-nome');

        // --- Templates ---
        const templateModalGrupoCriar = document.getElementById('template-modal-grupo-criar');
        const templateModalGrupoExcluir = document.getElementById('template-modal-grupo-excluir');


        const templateItemGrupo = document.getElementById('template-lista-item-grupo');
        const templateMembroGrupo = document.getElementById('template-membro-grupo');
        const templateModalRemoverMembro = document.getElementById('template-modal-remover-membro');
        const templateModalAdicionarMembros = document.getElementById('template-modal-adicionar-membros');
        const templateMembroDisponivel = document.getElementById('template-membro-disponivel');

        // --- Adiciona modais ao body ---
        document.body.appendChild(templateModalGrupoCriar.content.cloneNode(true));
        document.body.appendChild(templateModalGrupoExcluir.content.cloneNode(true));

        document.body.appendChild(templateItemGrupo.content.cloneNode(true));
        document.body.appendChild(templateModalAdicionarMembros.content.cloneNode(true));
        document.body.appendChild(templateModalRemoverMembro.content.cloneNode(true));

        // --- Variáveis de estado ---
        let grupoIdAtual = null;
        let paginaAtual = 1;
        let carregandoMembros = false;
        let temMaisMembros = true;
        let termoBuscaAtual = '';
        let timeoutBusca = null;
        const MEMBROS_POR_PAGINA = 20;

        // --- Funções Auxiliares ---

        function abrirModal(selector) {
            const modal = document.querySelector(selector);
            if (modal) modal.style.display = 'flex';
        }

        function fecharModal(selector) {
            const modal = document.querySelector(selector);
            if (modal) modal.style.display = 'none';
        }

        // --- Carregamento de Dados (AJAX) ---

        function carregarGrupos() {
            $.get('/grupos/obter')
                .done(response => {
                    if (response.status === 'sucesso' && response.grupos.length > 0) {
                        renderizarGrupos(response.grupos);
                    } else {
                        gruposLista.innerHTML = '<p class="text-gray-500 px-3 py-2">Nenhum grupo encontrado.</p>';
                    }
                })
                .fail(() => {
                    gruposLista.innerHTML = '<p class="text-red-500 px-3 py-2">Erro ao carregar grupos.</p>';
                });
        }

        function carregarMembros(grupoId, resetar = true) {
            if (resetar) {
                grupoIdAtual = grupoId;
                paginaAtual = 1;
                temMaisMembros = true;
                termoBuscaAtual = '';
                containerMembros.innerHTML = '';
                document.getElementById('buscar-membros-input').value = '';
            }

            if (carregandoMembros || !temMaisMembros) return;

            carregandoMembros = true;
            
            if (resetar) {
                colunaMembros.classList.add('opacity-25');
                containerMembros.innerHTML = '';
            } else {
                // Adiciona loader no final da lista apenas se houver mais membros
                const loader = document.createElement('div');
                loader.className = 'text-center py-4';
                loader.id = 'loader-membros';
                loader.innerHTML = '<span class="material-icons-sharp text-3xl text-gray-400 animate-spin">sync</span><p class="text-sm text-gray-500 mt-2">Carregando mais membros...</p>';
                containerMembros.appendChild(loader);
            }

            $.get(`/grupos/${grupoId}/membros/obter`, {
                pagina: paginaAtual,
                por_pagina: MEMBROS_POR_PAGINA,
                busca: termoBuscaAtual
            })
                .done(response => {
                    if (response.status === 'sucesso') {
                        if (resetar) {
                            grupoNomeSpan.textContent = response.grupo.nome;
                            cabecalhoMembros.querySelector('#btn-abrir-modal-adicionar').disabled = response.grupo.padrao === true;
                        }

                        const membros = response.grupo.membros || [];
                        
                        if (membros.length === 0 && paginaAtual === 1) {
                            containerMembros.innerHTML = `
                                <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center min-h-[500px]">
                                    <span class="material-icons-sharp mx-auto !text-5xl text-gray-400">person_search</span>
                                    <h3 class="mt-2 font-medium text-gray-900">Nenhum membro encontrado.</h3>
                                    <p class="mt-1 text-gray-500">Comece <button type="button" class="form-link js-adicionar-membros">adicionando membros</button> a este grupo.</p>
                                </div>`;
                            temMaisMembros = false;
                        } else {
                            membros.forEach(membro => renderizarMembro(membro, response.grupo.padrao));
                            
                            // Verifica se há mais membros para carregar
                            if (membros.length < MEMBROS_POR_PAGINA) {
                                temMaisMembros = false;
                                
                                // Adiciona mensagem de fim da lista se não for a primeira página
                                if (paginaAtual > 1) {
                                    const fimLista = document.createElement('div');
                                    fimLista.className = 'text-center py-4 text-sm text-gray-500 border-t border-gray-200 mt-4';
                                    fimLista.textContent = `Todos os ${containerMembros.querySelectorAll('.item-membro-grupo').length} membros foram carregados.`;
                                    containerMembros.appendChild(fimLista);
                                }
                            } else {
                                temMaisMembros = true;
                            }
                            
                            paginaAtual++;
                        }
                    } else {
                        if (resetar) {
                            containerMembros.innerHTML = `<p class="text-red-500">${response.mensagem || 'Erro ao carregar membros.'}</p>`;
                        }
                        temMaisMembros = false;
                    }
                })
                .fail(() => {
                    if (resetar) {
                        containerMembros.innerHTML = '<p class="text-red-500">Erro de comunicação ao carregar membros.</p>';
                    }
                    temMaisMembros = false;
                })
                .always(() => {
                    carregandoMembros = false;
                    colunaMembros.classList.remove('opacity-25');
                    document.getElementById('loader-membros')?.remove();
                });
        }

        // --- Renderização ---

        function renderizarMembro(membro, grupoPadrao = false) {
            const clone = templateMembroGrupo.content.cloneNode(true);
            const item = clone.querySelector('.item-membro-grupo');
            item.dataset.nome = membro.nome.toLowerCase();
            item.dataset.email = membro.email.toLowerCase();

            clone.querySelector('.membro-avatar').src = membro.foto_perfil;
            clone.querySelector('.membro-avatar').alt = `Avatar de ${membro.nome}`;
            clone.querySelector('.membro-nome').textContent = membro.nome;
            clone.querySelector('.membro-email').textContent = membro.email;

            if (grupoPadrao) {
                clone.querySelector('.btn-remover-membro')?.remove();
            } else {
                const btnRemover = clone.querySelector('.btn-remover-membro');
                if (btnRemover) {
                    btnRemover.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const grupo = document.querySelector('.item-grupo[data-active="true"]');
                        document.getElementById('remover-membro-membro-id').value = grupo.dataset.grupoId;
                        document.getElementById('remover-membro-grupo-nome').textContent = grupo.querySelector('.grupo-nome').textContent;
                        document.getElementById('remover-membro-nome').textContent = membro.nome;
                        document.getElementById('form-remover-membro').action = `/grupos/${grupo.dataset.grupoId}/membros/${membro.id}/remover`;
                        abrirModal('#modal-remover-membro');
                    });
                }
            }
            
            // Animação de entrada
            item.style.opacity = '0';
            containerMembros.appendChild(clone);
            setTimeout(() => {
                item.style.transition = 'opacity 0.3s';
                item.style.opacity = '1';
            }, 50);
        }

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
                link.dataset.grupoId = grupo.id;
                clone.querySelector('.grupo-nome').textContent = grupo.nome;

                if (!grupo.padrao) {
                    const excluirBtn = clone.querySelector('.btn-excluir');
                    excluirBtn.classList.remove('hidden');
                    excluirBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        document.getElementById('grupo-excluir-id').value = grupo.id;
                        document.getElementById('grupo-excluir-nome').textContent = grupo.nome;
                        document.getElementById('form-excluir-grupo').action = `/grupos/${grupo.id}/excluir`;
                        abrirModal('#modal-excluir-grupo');
                    });
                }

                link.addEventListener('click', e => {
                    e.preventDefault();
                    marcarComoSelecionado(link);
                    carregarMembros(grupo.id, true);
                });

                gruposLista.appendChild(clone);
            });

            const primeiro = gruposLista.querySelector('.item-grupo');
            if (primeiro) {
                marcarComoSelecionado(primeiro);
                carregarMembros(primeiro.dataset.grupoId, true);
            }
        }

        // --- Scroll Infinito ---
        function configurarScrollInfinito() {
            containerMembros.addEventListener('scroll', function() {
                if (carregandoMembros || !temMaisMembros || !grupoIdAtual) return;

                const scrollTop = containerMembros.scrollTop;
                const scrollHeight = containerMembros.scrollHeight;
                const clientHeight = containerMembros.clientHeight;

                // Carrega mais quando chegar a 80% do scroll
                if (scrollTop + clientHeight >= scrollHeight * 0.8) {
                    carregarMembros(grupoIdAtual, false);
                }
            });
        }

        function marcarComoSelecionado(elem) {
            gruposLista.querySelectorAll('.item-grupo').forEach(e => {
                e.setAttribute('data-active', 'false');
            });
            elem.setAttribute('data-active', 'true');
        }

        // --- Lógica do Modal "Adicionar Membros" ---

        function abrirModalAdicionarMembros() {
            if (!grupoIdAtual) return;
            const lista = document.getElementById('lista-membros-disponiveis');
            lista.innerHTML = '<p class="text-gray-500 text-center py-4">Buscando usuários...</p>';
            abrirModal('#modal-adicionar-membros');

            $.get(`/grupos/${grupoIdAtual}/membros/disponiveis`)
                .done(response => {

                    if (response.status === 'sucesso' && response.usuarios.length > 0) {
                        lista.innerHTML = '';
                        response.usuarios.forEach(usuario => {
                            const clone = templateMembroDisponivel.content.cloneNode(true);
                            const item = clone.querySelector('.item-membro-disponivel');
                            item.dataset.nome = usuario.nome.toLowerCase();
                            clone.querySelector('input[type="checkbox"]').value = usuario.id;
                            clone.querySelector('.membro-avatar').src = usuario.foto_perfil;
                            clone.querySelector('.membro-avatar').alt = `Avatar de ${usuario.nome}`;
                            clone.querySelector('.membro-nome').textContent = usuario.nome;
                            clone.querySelector('.membro-email').textContent = usuario.email;
                            lista.appendChild(clone);
                        });
                    } else {
                        lista.innerHTML = '<p class="text-gray-500 text-center py-4">Nenhum usuário novo para adicionar.</p>';
                    }
                })
                .fail(() => {
                    lista.innerHTML = '<p class="text-red-500 text-center py-4">Erro ao buscar usuários.</p>';
                });
        }

        function adicionarMembrosSelecionados() {
            const form = document.getElementById('form-adicionar-membros');
            const membrosSelecionados = Array.from(form.querySelectorAll('input:checked')).map(input => input.value);

            if (membrosSelecionados.length === 0) {
                notificador.aviso('Nenhum membro selecionado.', null, { target: '#form-adicionar-membros' });
                return;
            }

            $.post(`/grupos/${grupoIdAtual}/membros/adicionar`, { membros: membrosSelecionados })
                .done(response => {
                    if (response.status === 'sucesso') {
                        fecharModal('#modal-adicionar-membros');
                        notificador.sucesso(response.mensagem, null, { target: '#main-membros-grupos' });
                        // Recarrega a lista de membros
                        paginaAtual = 1;
                        temMaisMembros = true;
                        containerMembros.innerHTML = '';
                        carregarMembros(grupoIdAtual, false);
                    } else {
                        notificador.erro(response.mensagem || 'Erro ao adicionar membros.', null, { target: '#form-adicionar-membros' });
                    }
                })
                .fail(() => {
                    notificador.erro('Erro de comunicação ao adicionar membros.', null, { target: '#form-adicionar-membros' });
                });
        }

        // --- Event Listeners ---

        document.getElementById('criar-grupo-button')?.addEventListener('click', () => abrirModal('#modal-criar-grupo'));
        document.getElementById('btn-abrir-modal-adicionar')?.addEventListener('click', abrirModalAdicionarMembros);
        document.getElementById('btn-confirmar-adicionar-membros')?.addEventListener('click', adicionarMembrosSelecionados);

        document.addEventListener('click', function(event) {
            if (event.target.matches('.js-adicionar-membros')) {
                event.preventDefault();
                abrirModalAdicionarMembros();
            }
        });


        document.querySelectorAll('.modal-fechar').forEach(btn => {
            btn.addEventListener('click', (e) => fecharModal(`#${e.target.closest('.modal, .hidden').id}`));
        });

        // Filtro de busca para membros do grupo
        document.getElementById('buscar-membros-input').addEventListener('input', function() {
            clearTimeout(timeoutBusca);
            termoBuscaAtual = this.value.trim();
            
            timeoutBusca = setTimeout(() => {
                if (grupoIdAtual) {
                    paginaAtual = 1;
                    temMaisMembros = true;
                    containerMembros.innerHTML = '';
                    carregarMembros(grupoIdAtual, false);
                }
            }, 800); // 800ms após parar de digitar
        });

        document.getElementById('buscar-membros-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                clearTimeout(timeoutBusca);
                termoBuscaAtual = this.value.trim();
                if (grupoIdAtual) {
                    paginaAtual = 1;
                    temMaisMembros = true;
                    containerMembros.innerHTML = '';
                    carregarMembros(grupoIdAtual, false);
                }
            }
        });

        // Filtro de busca para membros disponíveis no modal
        document.getElementById('buscar-membros-disponiveis-input').addEventListener('keyup', function() {
            const termo = this.value.toLowerCase();
            document.querySelectorAll('#lista-membros-disponiveis .item-membro-disponivel').forEach(item => {
                const nome = item.dataset.nome || '';
                item.style.display = nome.includes(termo) ? '' : 'none';
            });
        });

        // --- Inicialização dos Formulários ---
        new Formulario({
            formId: 'form-criar-grupo',
            onSuccess: (response) => {
                fecharModal('#modal-criar-grupo');
                notificador.sucesso(response.mensagem, null, { target: '#main-membros-grupos' });
                carregarGrupos();
            }
        });

        new Formulario({
            formId: 'form-excluir-grupo',
            onError: (response) => {
                fecharModal('#modal-excluir-grupo');
                notificador.erro(response.mensagem, null, { target: '#main-membros-grupos' });
            },
            onSuccess: (response) => {
                fecharModal('#modal-excluir-grupo');
                notificador.sucesso(response.mensagem, null, { target: '#main-membros-grupos' });
                carregarGrupos();
            }
        });

        new Formulario({
            formId: 'form-remover-membro',
            onError: (response) => {
                fecharModal('#modal-remover-membro');
                notificador.erro(response.mensagem, null, { target: '#main-membros-grupos' });
            },
            onSuccess: (response) => {
                fecharModal('#modal-remover-membro');
                notificador.sucesso(response.mensagem, null, { target: '#main-membros-grupos' });
                // Recarrega a lista de membros
                paginaAtual = 1;
                temMaisMembros = true;
                containerMembros.innerHTML = '';
                carregarMembros(grupoIdAtual, false);
            }
        });

        // --- Início ---
        configurarScrollInfinito();
        carregarGrupos();
    });
</script>