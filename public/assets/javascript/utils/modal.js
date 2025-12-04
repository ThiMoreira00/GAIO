class Modal {

    modal = null;

    constructor(modalSelector = '.modal') {
        this.modal = document.querySelector(modalSelector);

        if (!this.modal) {
            console.error(`[Modal] Modal com seletor "${modalSelector}" não encontrado.`);
            return;
        }
        
        this.iniciar();
    }

    iniciar() {
        if (this.modal) {
            // Botões com data-modal-esconder ou .button-modal-fechar
            const botoesFechar = this.modal.querySelectorAll('[data-modal-esconder], .button-modal-fechar');
            if (botoesFechar) {
                botoesFechar.forEach(botao => {
                    botao.addEventListener('click', () => {
                        this.fechar();
                    });
                });
            }
            // Fechar ao pressionar ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal.classList.contains('flex')) {
                    this.fechar();
                }
            });
        }
    }

    abrir() {
        if (this.modal) {
            this.modal.classList.remove("hidden");
            this.modal.classList.add("flex");
            document.body.style.overflow = "hidden"; // Impede o scroll do body
        }
    }

    fechar() {
        if (this.modal) {
            this.modal.classList.remove("flex");
            this.modal.classList.add("hidden");
            document.body.style.overflow = ""; // Libera o scroll do body
        }
    }

    limparCampos() {
        if (this.modal) {
            const inputs = this.modal.querySelectorAll("input, textarea, select");
            inputs.forEach(input => {
                input.value = "";
            });
        }
    }

}