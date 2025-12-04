/**
 * Notificador.js
 * 
 * Classe para exibir notificações na interface do usuário.
 * 
 * Tipos de notificações suportados:
 * - erro
 * - sucesso
 * - aviso
 * - info
 * 
 */
class Notificador {
    constructor() {
        this.tipos = {
            erro: {
                icone: 'error',
                titulo: 'Oops!',
                classes: {
                    container: 'bg-red-100 p-4',
                    icone: 'h-5 w-5 text-red-400',
                    titulo: 'text-sm font-medium text-red-800 !text-left',
                    mensagem: 'mt-2 text-sm text-red-700 !text-left'
                }
            },
            sucesso: {
                icone: 'check_circle',
                titulo: 'Sucesso!',
                classes: {
                    container: 'bg-green-100 p-4',
                    icone: 'h-5 w-5 text-green-400',
                    titulo: 'text-sm font-medium text-green-800 !text-left',
                    mensagem: 'mt-2 text-sm text-green-700 !text-left'
                }
            },
            aviso: {
                icone: 'warning',
                titulo: 'Atenção!',
                classes: {
                    container: 'bg-yellow-100 p-4',
                    icone: 'h-5 w-5 text-yellow-400',
                    titulo: 'text-sm font-medium text-yellow-800 !text-left',
                    mensagem: 'mt-2 text-sm text-yellow-700 !text-left'
                }
            },
            info: {
                icone: 'info',
                titulo: 'Informação',
                classes: {
                    container: 'bg-blue-100 p-4',
                    icone: 'h-5 w-5 text-blue-400',
                    titulo: 'text-sm font-medium text-blue-800 !text-left',
                    mensagem: 'mt-2 text-sm text-blue-700 !text-left'
                }
            }
        };
    }

    /**
     * Exibe uma notificação na tela.
     * 
     * @param {string} tipo - 'erro', 'sucesso', 'aviso', 'info'
     * @param {string} mensagem - A mensagem a ser exibida
     * @param {string|null} titulo - O título da notificação. Se nulo, usa o padrão
     * @param {object} opcoes - Opções adicionais
     * @param {string|HTMLElement|null} opcoes.alvo - Seletor de CSS ou elemento DOM onde a notificação deve aparecer. Se nulo, a notificação será flutuante
     * @param {number} opcoes.duracao - Duração em milissegundos antes de desaparecer. Padrão é 5000ms. Se 0, não desaparece automaticamente.
     * @returns {void}
     */
    exibir(tipo, mensagem, titulo = null, opcoes = {}) {

        // Configurações padrão
        const { alvo = null, duracao = 5000, iconeFechar = false } = opcoes;
        const configuracao = this.tipos[tipo] || this.tipos.info;
        titulo ??= configuracao.titulo;
        var elementoAlvo = null;

        // Verifica o elemento alvo para a notificação
        if (alvo) {
            elementoAlvo = typeof alvo === 'string' ? document.querySelector(alvo) : (alvo instanceof HTMLElement ? alvo : null);

            if (!elementoAlvo) {
                console.error(`[Notificador] Não foi possível identificar o elemento de destino para a notificação "${tipo}".`);
            }
        }

        // Se nenhum alvo for especificado (ou se não for encontrado), a notificação será flutuante
        const estilo = elementoAlvo ? '' : 'position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; max-width: 400px;';

        const notificacaoHTML = `
        <div style="${estilo}" class="flex ${configuracao.classes.container} rounded-md mb-4" role="alert" id="notificacao">
            <div class="flex-shrink-0">
                <span class="material-icons-sharp ${configuracao.classes.icone}" aria-hidden="true">${configuracao.icone}</span>
            </div>
            <div class="ml-3">
                <h3 class="${configuracao.classes.titulo}">${this.escaparHTML(titulo)}</h3>
                <div class="${configuracao.classes.mensagem}">${this.escaparHTML(mensagem)}</div>
            </div>
            ${iconeFechar ? `
            <div class="ml-auto pl-3">
                <button type="button" class="-mx-1.5 -my-1.5 bg-transparent rounded-md p-1.5 inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-600" aria-label="Fechar notificação">
                    <span class="material-icons-sharp h-5 w-5 text-gray-400" aria-hidden="true">close</span>
                </button>
            </div>
            ` : ''}
        </div>`;

        // Remove notificações anteriores
        this.removerNotificacoesAnteriores();

        // Insere a notificação no local correto
        elementoAlvo ? elementoAlvo.insertAdjacentHTML('afterbegin', notificacaoHTML) : document.body.insertAdjacentHTML('beforeend', notificacaoHTML);

        const notificacaoElemento = document.getElementById('notificacao');
        if (!notificacaoElemento) return;

        // Adiciona um listener para o botão de fechar
        const botaoFechar = notificacaoElemento.querySelector('button[aria-label="Fechar notificação"]');
        if (botaoFechar) {
            botaoFechar.addEventListener('click', () => {
                this.fechar(notificacaoElemento);
            });
        }

        // Se a duração for maior que 0, configura o timer para fechar a notificação automaticamente
        if (duracao > 0) {
            setTimeout(() => {
                this.fechar(notificacaoElemento);
            }, duracao);
        }
    }

    /**
     * Remove todas as notificações da tela.
     * 
     * @returns {void}
     */
    removerNotificacoesAnteriores() {

        if (this.verificarNotificacoesAtivas()) {
            // Remove sem animação
            const notificacoesAtivas = document.querySelectorAll('#notificacao');
            notificacoesAtivas.forEach(notificacao => {
                notificacao.remove();
            });
        }

        const notificacoes = document.querySelectorAll('#notificacao');
        notificacoes.forEach(notificacao => {
            this.fechar(notificacao);
        });
    }

    /**
     * Fecha uma notificação.
     * 
     * @param {HTMLElement} notificacao - O elemento da notificação a ser fechado
     * @returns {void}
     */
    fechar(notificacao) {
        if (notificacao) {

            // Se tiver notificação, remove o fade-out e remove o elemento após a animação
            notificacao.classList.add('fade-out');
            setTimeout(() => {
                notificacao.remove();
            }, 300); // Tempo para a animação de fade-out
        }
    }

    /**
     * Escapa caracteres especiais em uma string para evitar vulnerabilidades XSS.
     * @param {string} str - A string a ser escapada
     * @returns {string} - A string escapada
     * 
     * Fonte: https://stackoverflow.com/a/6234804
    */
    escaparHTML(str) {
        if (!str) return '';
        return str.replace(/[&<>"'`=\/]/g, function (s) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            }[s];
        });
    }

    /**
     * Exibe uma notificação de erro.
     * @param {string} mensagem - A mensagem a ser exibida
     * @param {string|null} titulo - O título da notificação. Se nulo, usa o padrão
     * @param {object} opcoes - Opções adicionais
     * @param {string|HTMLElement|null} opcoes.alvo - Seletor de CSS ou elemento DOM onde a notificação deve aparecer. Se nulo, a notificação será flutuante
     * @param {number} opcoes.duracao - Duração em milissegundos antes de desaparecer. Padrão é 5000ms. Se 0, não desaparece automaticamente.
     * @returns {void}
     */
    erro(mensagem, titulo = null, opcoes = {}) {
        this.exibir('erro', mensagem, titulo, opcoes);
    }

    /**
     * Exibe uma notificação de sucesso.
     * @param {string} mensagem - A mensagem a ser exibida
     * @param {string|null} titulo - O título da notificação. Se nulo, usa o padrão
     * @param {object} opcoes - Opções adicionais
     * @param {string|HTMLElement|null} opcoes.alvo - Seletor de CSS ou elemento DOM onde a notificação deve aparecer. Se nulo, a notificação será flutuante
     * @param {number} opcoes.duracao - Duração em milissegundos antes de desaparecer. Padrão é 5000ms. Se 0, não desaparece automaticamente.
     * @returns {void}
     */
    sucesso(mensagem, titulo = null, opcoes = {}) {
        this.exibir('sucesso', mensagem, titulo, opcoes);
    }

    /**
     * Exibe uma notificação de aviso.
     * @param {string} mensagem - A mensagem a ser exibida
     * @param {string|null} titulo - O título da notificação. Se nulo, usa o padrão
     * @param {object} opcoes - Opções adicionais
     * @param {string|HTMLElement|null} opcoes.alvo - Seletor de CSS ou elemento DOM onde a notificação deve aparecer. Se nulo, a notificação será flutuante
     * @param {number} opcoes.duracao - Duração em milissegundos antes de desaparecer. Padrão é 5000ms. Se 0, não desaparece automaticamente.
     * @returns {void}
    */
    aviso(mensagem, titulo = null, opcoes = {}) {
        this.exibir('aviso', mensagem, titulo, opcoes);
    }

    /**
     * Exibe uma notificação de informação.
     * @param {string} mensagem - A mensagem a ser exibida
     * @param {string|null} titulo - O título da notificação. Se nulo, usa o padrão
     * @param {object} opcoes - Opções adicionais
     * @param {string|HTMLElement|null} opcoes.alvo - Seletor de CSS ou elemento DOM onde a notificação deve aparecer. Se nulo, a notificação será flutuante
     * @param {number} opcoes.duracao - Duração em milissegundos antes de desaparecer. Padrão é 5000ms. Se 0, não desaparece automaticamente.
     * @returns {void}
    */
    info(mensagem, titulo = null, opcoes = {}) {
        this.exibir('info', mensagem, titulo, opcoes);
    }

    /**
     * Verifica se há notificações ativas na tela.
     * @return {boolean} - true se houver notificações, false caso contrário
     */
    verificarNotificacoesAtivas() {
        return document.querySelectorAll('#notificacao').length > 0;
    }

}

const notificador = new Notificador();
window.notificador = notificador;