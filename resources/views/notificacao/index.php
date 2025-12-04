<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Minhas notificações</h1>
    <div class="flex-shrink-0">
        <button type="button" class="text-sky-600 hover:text-sky-800 text-sm font-medium underline" id="button-notificacao-marcar-todas-lidas">
            Marcar todas como lidas
        </button>
    </div>
</header>

<main id="main-notificacoes" class="tab">
    <section class="bg-white sm:p-6 lg:p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="notificacoes-section-heading">
        <h2 id="notificacoes-section-heading" class="sr-only">Minhas notificações</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8" id="tab-notificacoes-filtros">
                    <form id="form-filtros-notificacoes" action="/notificacoes/filtrar" method="GET" data-tab-form>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-shrink-0">
                                <input type="hidden" name="status" id="status-input" value="" data-tab-status-input>
                                <div class="bg-gray-100 p-1 rounded-lg sm:rounded-full flex items-center justify-center flex-wrap gap-1">
                                    <button type="button" class="filter-btn-notificacao tab-item active" data-tab-status="todas" data-grau="" data-tab-button>Todos</button>
                                    <button type="button" class="filter-btn-notificacao tab-item" data-tab-status="nao_lidas" data-grau="" data-tab-button>Não lidas</button>
                                    <button type="button" class="filter-btn-notificacao tab-item" data-tab-status="lidas" data-grau="" data-tab-button>Lidas</button>
                                </div>
                            </div>
                            <div class="relative w-full sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-notificacoes" class="input-search" placeholder="Buscar notificações..." data-tab-search>
                            </div>
                        </div>
                    </form>
                </section>
                <article id="container-notificacoes" class="space-y-4"></article>
            </div>
        </div>
    </section>
</main>

<?php
    include __DIR__ . '/../templates/notificacao-lista-item.php';
?>

<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/tab2.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script type="text/javascript" src="<?= obterURL('/assets/javascript/utils/notificador.js') ?>"></script>

<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // Templates
        const templateListaItemNotificacao = document.getElementById('template-lista-item-notificacao');

        // Containers
        const containerNotificacoes = document.getElementById('main-notificacoes');

        // Botões
        const buttonMarcarTodasLidas = document.getElementById('button-notificacao-marcar-todas-lidas');

        // Elementos
        const elementoContadorNotificacoes = document.getElementById('total-notificacoes-nao-lidas');

        // Inicialização do Tab
        const tab = new Tab2('#main-notificacoes', {
            prefixo: 'notificacao',
            seletorConteudo: '#container-notificacoes',
            seletorTemplateRegistros: '#template-lista-item-notificacao',
            url: '/notificacoes/filtrar',
            metodo: 'GET',
            onItemRender: (dadosElemento, elemento, index) => {

                if (!dadosElemento || !elemento) {
                    return;
                }

                // Verifica se a notificação já foi lida
                if (dadosElemento.lida) {
                    const botaoLer = elemento.querySelector('.notificacao-ler');
                    if (botaoLer) {
                        botaoLer.remove();
                    }
                } else {
                    // Adiciona evento ao botão "Marcar como lida"
                    const botaoLer = elemento.querySelector('.notificacao-ler button');
                    if (botaoLer) {
                        botaoLer.addEventListener('click', function() {
                            marcarNotificacaoComoLida(dadosElemento.id, elemento);
                        });
                    }

                    // Altera o cor do fundo para sky-100
                    const detalhesNotificacao = elemento.querySelector('#detalhes-notificacao-' + dadosElemento.id);
                    if (detalhesNotificacao) {
                        detalhesNotificacao.classList.add('bg-sky-50');
                    }
                }


                // Renderiza o conteúdo Markdown da mensagem
                const mensagemElemento = elemento.querySelector('#mensagem-notificacao-' + dadosElemento.id);
                if (mensagemElemento) {
                    var mensagem = marked.parse(dadosElemento.mensagem);
                    mensagem = mensagem.replace(/<strong>/g, '<strong class="font-semibold">');
                    mensagem = mensagem.replace(/<b>/g, '<b class="font-semibold">');
                    mensagemElemento.innerHTML = mensagem;
                }
            }
        });

        // Botão "Marcar todas como lidas"
        if (buttonMarcarTodasLidas) {
            buttonMarcarTodasLidas.addEventListener('click', function() {
                $.ajax({
                    url: '/notificacoes/ler-todas',
                    method: 'POST',
                    success: function(response) {
                        if (response.status === 'sucesso') {
                            // Remove todos os botões "Marcar como lida"
                            const botoesLer = document.querySelectorAll('.notificacao-ler');
                            botoesLer.forEach(function(botaoLer) {
                                botaoLer.remove();
                            });

                            // Remove o destaque de todas as notificações
                            const detalhesNotificacoes = document.querySelectorAll('[id^="detalhes-notificacao-"]');
                            detalhesNotificacoes.forEach(function(detalhesNotificacao) {
                                detalhesNotificacao.classList.remove('bg-sky-50');
                            });

                            // Atualizar contador de notificações não lidas (no cabeçalho)
                            const contadorNotificacoes = document.querySelector('#total-notificacoes-nao-lidas');
                            if (contadorNotificacoes) {
                                contadorNotificacoes.remove();
                            }

                            buttonMarcarTodasLidas.remove();

                            notificador.sucesso(response.mensagem, null, { alvo: '#main-notificacoes' });
                        } else {
                            notificador.erro(response.mensagem, null, { alvo: '#main-notificacoes' });
                        }
                    },
                    error: function() {
                        Notificador.erro('Erro ao marcar todas as notificações como lidas.', null, { alvo: '#main-notificacoes' });
                    }
                });
            });
        }

        if (elementoContadorNotificacoes.textContent === '0') {
            buttonMarcarTodasLidas.remove();
        }

        /** =========================================================================
         * FUNÇÕES DE NOTIFICAÇÃO
        ==========================================================================*/

        function marcarNotificacaoComoLida(notificacaoId, elementoNotificacao) {

            notificacaoId = parseInt(notificacaoId);

            if (isNaN(notificacaoId) || notificacaoId <= 0) {
                Notificador.erro('ID de notificação inválido.');
                return;
            }

            $.ajax({
                url: `/notificacoes/${notificacaoId}/ler`,
                method: 'POST',
                success: function(response) {

                    if (response.status === 'sucesso') {
                        // Remove o botão "Marcar como lida"
                        const botaoLer = elementoNotificacao.querySelector('.notificacao-ler');
                        if (botaoLer) {
                            botaoLer.remove();
                        }
                        
                        const detalhesNotificacao = elementoNotificacao.querySelector('#detalhes-notificacao-' + notificacaoId);
                        if (detalhesNotificacao) {
                            detalhesNotificacao.classList.remove('bg-sky-50');
                        }

                        // Atualizar contador de notificações não lidas (no cabeçalho)
                        if (elementoContadorNotificacoes) {
                            let totalNaoLidas = document.querySelectorAll('.notificacao-ler').length;
                            totalNaoLidas = Math.max(0, totalNaoLidas);

                            if (totalNaoLidas === 0) {
                                elementoContadorNotificacoes.remove();
                                buttonMarcarTodasLidas.remove();
                            } else if (totalNaoLidas > 9) {
                                elementoContadorNotificacoes.textContent = '9+';
                            } else {
                                elementoContadorNotificacoes.textContent = totalNaoLidas;
                            }
                        }

                        notificador.sucesso(response.mensagem, null, { alvo: '#main-notificacoes' });
                    } else {
                        notificador.erro(response.mensagem, null, { alvo: '#main-notificacoes' });
                    }
                },
                error: function() {
                    Notificador.erro('Erro ao marcar a notificação como lida.', null, { alvo: '#main-notificacoes' });
                }
            });
        }
    });

</script>