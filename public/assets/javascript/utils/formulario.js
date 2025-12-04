class Formulario {
    constructor(formId, { beforeSubmit, onSuccess, onError, onComplete, notificador }) {
        
        this.form = document.querySelector(formId);

        // Verifica se existe o formulário
        if (!this.form) {
            console.error(`[Formulário] Formulário com seletor "${formId}" não encontrado.`);
            return;
        }

        // Atribui os callbacks ou null
        this.onBeforeSubmit = beforeSubmit || null;

        if (typeof this.onBeforeSubmit !== "function" && this.onBeforeSubmit !== null) {
            console.error("[Formulário] O atributo \"onBeforeSubmit\" deve ser uma função ou null.");
            this.onBeforeSubmit = null;
        }

        this.onSuccess = onSuccess || null;

        if (typeof this.onSuccess !== "function" && this.onSuccess !== null) {
            console.error("[Formulário] O atributo \"onSuccess\" deve ser uma função ou null.");
            this.onSuccess = null;
        }

        this.onError = onError || null;

        if (typeof this.onError !== "function" && this.onError !== null) {
            console.error("[Formulário] O atributo \"onError\" deve ser uma função ou null.");
            this.onError = null;
        }

        this.onComplete = onComplete || null;
        if (typeof this.onComplete !== "function" && this.onComplete !== null) {
            console.error("[Formulário] O atributo \"onComplete\" deve ser uma função ou null.");
            this.onComplete = null;
        }

        this.notificador = notificador || null;
        if (this.notificador && (typeof this.notificador !== "object" || !this.notificador.alvo || !this.notificador.status)) {
            console.error("[Formulário] O atributo \"notificador\" deve ser um objeto válido com uma propriedade \"alvo\" e \"status\".");
            this.notificador = null;
        }

        this.iniciar();

    }

    /**
     * Valida se todos os campos obrigatórios estão preenchidos
     * 
     * @return {boolean} - true se todos os campos obrigatórios estiverem preenchidos, false caso contrário
     */
    validarCamposObrigatorios() {
        const camposObrigatorios = this.form.querySelectorAll('[required]');

        for (let campo of camposObrigatorios) {
            if (!campo.value || campo.value.trim() === '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Limpa todos os campos do formulário
     * 
     * @returns {void}
     */
    limparCampos() {
        this.form.reset();
    }

    /**
     * Inicia o listener de submit do formulário
     * 
     * @returns {void}
     */
    iniciar() {

        this.form.addEventListener('submit', (event) => {
            event.preventDefault();

            // Botão de submit
            const botaoSubmit = this.form.querySelector('button[type="submit"]');

            if (botaoSubmit) {
                botaoSubmit.disabled = true;
            }

            // Validação de campos obrigatórios
            if (!this.validarCamposObrigatorios()) {
                this.notificar("aviso", "Por favor, preencha todos os campos obrigatórios.");
                if (botaoSubmit) {
                    botaoSubmit.disabled = false;
                }
                return; // Impedir envio se validação falhar
            }

            // Requisição pelo jQuery
            $.ajax({
                url: this.form.action,
                type: this.form.method,
                data: $(this.form).serialize(),
                dataType: 'json',
                beforeSend: () => {
                    if (this.beforeSubmit) this.beforeSubmit();
                },
                success: (response) => {
                    if (response.status === "erro") {
                        this.notificar("erro", response.mensagem);
                        if (this.onError) this.onError(response);
                        return;
                    }

                    if (this.notificar) this.notificar("sucesso", response.mensagem);
                    if (this.onSuccess) this.onSuccess(response);
                    if (botaoSubmit) botaoSubmit.disabled = false;
                },
                error: (error) => {
                    this.notificar("erro", error && error.responseJSON && error.responseJSON.mensagem ? error.responseJSON.mensagem : "Ocorreu um erro ao processar o formulário. Por favor, tente novamente.");
                    if (this.onError) this.onError(error);
                },
                complete: () => {
                    if (botaoSubmit) botaoSubmit.disabled = false;
                    if (this.onComplete) this.onComplete();
                }
            });

        });

    }

    /**
     * Notifica o usuário sobre o status da operação
     * 
     * @param {string} tipo 
     * @param {string} mensagem 
     * @returns {null}
     */
    notificar(tipo, mensagem) {

        // Verifica se o notificador está definido
        if (!this.notificador || typeof this.notificador === "undefined" || !this.notificador.alvo) return;

        // Verifica o alvo
        const alvo = { alvo: this.notificador.alvo };

        if (!alvo) return;

        switch (tipo) {
            case "sucesso":
                notificador.sucesso(mensagem, null, alvo);
                break;
            case "erro":
                notificador.erro(mensagem, null, alvo);
                break;
            case "aviso":
                notificador.aviso(mensagem, null, alvo);
                break;
            case "info":
                notificador.info(mensagem, null, alvo);
                break;
        }
        return null;
    }
}