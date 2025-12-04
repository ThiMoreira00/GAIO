class NotificadorFlash {
    constructor() {
        this.configTipos = {
            erro: {
                divClasses: 'bg-red-100 p-4 rounded-lg mb-6',
                iconeClasses: 'h-5 w-5 text-red-400',
                iconeSvgPath: 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z',
                tituloClasses: 'text-sm font-medium text-red-800',
                mensagemClasses: 'mt-2 text-sm text-red-700',
                tituloPadrao: 'Oops!'
            },
            sucesso: {
                divClasses: 'bg-green-100 p-4 rounded-lg mb-6',
                iconeClasses: 'h-5 w-5 text-green-400',
                iconeSvgPath: 'M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4Z',
                tituloClasses: 'text-sm font-medium text-green-800',
                mensagemClasses: 'mt-2 text-sm text-green-700',
                tituloPadrao: 'Sucesso!'
            },
            aviso: {
                divClasses: 'bg-yellow-100 p-4 rounded-lg mb-6',
                iconeClasses: 'h-5 w-5 text-yellow-400',
                iconeSvgPath: 'M8.485 2.495c.646-1.113 2.384-1.113 3.03 0l6.28 10.875c.646 1.113-.273 2.505-1.515 2.505H3.72c-1.242 0-2.161-1.392-1.515-2.505l6.28-10.875ZM10 6a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z',
                tituloClasses: 'text-sm font-medium text-yellow-800',
                mensagemClasses: 'mt-2 text-sm text-yellow-700',
                tituloPadrao: 'Atenção!'
            },
            info: {
                divClasses: 'bg-blue-100 p-4 rounded-lg mb-6',
                iconeClasses: 'h-5 w-5 text-blue-400',
                iconeSvgPath: 'M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15h.506a1.75 1.75 0 0 0 1.74-1.934l-.459-2.066a.25.25 0 0 1 .244-.304H13a.75.75 0 0 0 0-1.5H9Z',
                tituloClasses: 'text-sm font-medium text-blue-800',
                mensagemClasses: 'mt-2 text-sm text-blue-700',
                tituloPadrao: 'Informação'
            }
        };
    }

    /**
     * Exibe um alerta na tela.
     * @param {string} tipo - 'sucesso', 'erro', 'aviso', 'info'
     * @param {string} mensagem - A mensagem a ser exibida
     * @param {string|null} titulo - O título do alerta. Se nulo, usa o padrão
     * @param {object} options - Opções adicionais
     * @param {string|HTMLElement|null} options.target - Seletor de CSS ou elemento DOM onde o alerta deve aparecer. Se nulo, o alerta será flutuante
     */
    mostrarAlerta(tipo, mensagem, titulo = null, options = {}) {
        const config = this.configTipos[tipo] || this.configTipos.info;
        const tituloFinal = titulo || config.tituloPadrao;
        let targetElement = null;
        let style = '';

        // Verifica se o alvo foi especificado
        if (options.target) {
            if (typeof options.target === 'string') {
                targetElement = document.querySelector(options.target);
            } else if (options.target instanceof HTMLElement) {
                targetElement = options.target;
            }

            if (!targetElement) {
                console.error(`NotificadorFlash: Não foi possível identificar o elemento de destino para o alerta "${tipo}".`);
                return;
            }
        }

        // Se nenhum alvo for especificado (ou se não for encontrado), o alerta será flutuante
        if (!targetElement) {
            style = 'position: fixed; top: 1.5rem; right: 1.5rem; z-index: 9999; max-width: 400px;';
        }

        const alertaHTML = `<div style="${style}" class="flex ${config.divClasses}" role="alert" id="mensagem-flash">
            <div class="flex-shrink-0">
                <svg class="${config.iconeClasses}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="${config.iconeSvgPath}" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="${config.tituloClasses}">${this.escapeHTML(tituloFinal)}</h3>
                <div class="${config.mensagemClasses}">${this.escapeHTML(mensagem)}</div>
            </div>
        </div>`;

        this.removerAlertasAnteriores();

        // Insere o alerta no local correto
        if (targetElement) {
            targetElement.insertAdjacentHTML('afterbegin', alertaHTML);
        } else {
            document.body.insertAdjacentHTML('beforeend', alertaHTML);
        }

        // Auto-remover após 5 segundos
        setTimeout(() => {
            const alerta = document.getElementById('mensagem-flash');
            if (alerta) {
                alerta.style.transition = 'opacity 0.5s ease-out';
                alerta.style.opacity = '0';
                setTimeout(() => alerta.remove(), 500);
            }
        }, 5000);
    }

    removerAlertasAnteriores() {
        const alertasAntigos = document.querySelectorAll('#mensagem-flash');
        alertasAntigos.forEach(alerta => alerta.remove());
    }

    escapeHTML(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Métodos de atalho atualizados para aceitar o objeto de opções
    sucesso(mensagem, titulo = null, options = {}) { this.mostrarAlerta('sucesso', mensagem, titulo, options); }
    erro(mensagem, titulo = null, options = {}) { this.mostrarAlerta('erro', mensagem, titulo, options); }
    info(mensagem, titulo = null, options = {}) { this.mostrarAlerta('info', mensagem, titulo, options); }
    aviso(mensagem, titulo = null, options = {}) { this.mostrarAlerta('aviso', mensagem, titulo, options); }
}

// A instância global permanece a mesma
const notificador = new NotificadorFlash();