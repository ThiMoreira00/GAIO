class Formulario {
    constructor({ formId, beforeSubmit, onSuccess, onError, onComplete, notificadorPositionId, redirectUrl, useNotificador = true }) {
        this.form = document.getElementById(formId);

        console.log(this.form + " " + formId);
        this.redirectUrl = redirectUrl || '/';
        this.beforeSubmit = beforeSubmit || null;
        this.onSuccess = onSuccess;
        this.onError = onError;
        this.onComplete = onComplete;
        this.notificadorPositionId = notificadorPositionId || this.form.id;
        this.useNotificador = useNotificador;
        this.init();
    }

    notify(tipo, mensagem) {
        if (!this.useNotificador || typeof notificador === "undefined") return;

        const target = { target: `#${this.notificadorPositionId}` };
        switch (tipo) {
            case "sucesso":
                notificador.sucesso(mensagem, null, target);
                break;
            case "erro":
                notificador.erro(mensagem, null, target);
                break;
            case "aviso":
                notificador.aviso(mensagem, null, target);
                break;
        }
    }

    init() {
        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            const submitButton = this.form.querySelector('button[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
            }

            if (!this.validateRequiredFields()) {
                this.notify("aviso", "Preencha todos os campos obrigatórios.");
                if (submitButton) {
                    submitButton.disabled = false;
                }
                return false;
            }

            const formData = new FormData(this.form);

            $.ajax({
                url: this.form.action,
                type: this.form.method,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: () => {
                    if (this.beforeSubmit) this.beforeSubmit();
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.dataset.originalText = submitButton.innerHTML;
                        submitButton.innerHTML = `<span class="material-icons-sharp text-lg animate-spin">sync</span>`;
                    }
                },
                success: (response) => {
                    console.log('Success response:', response);

                    if (response.status === "erro") {
                        this.notify("erro", response.mensagem);
                        if (this.onError) this.onError(response);
                        return;
                    }

                    this.notify("sucesso", response.mensagem);
                    if (this.onSuccess) this.onSuccess(response);
                },
                error: (xhr, status, error) => {
                    
                    // Tentar parsear JSON da resposta de erro
                    let errorResponse;
                    try {
                        errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.mensagem) {
                            this.notify("erro", errorResponse.mensagem);
                        } else {
                            this.notify("erro", "Ocorreu um erro ao processar a requisição.");
                        }
                    } catch (e) {
                        this.notify("erro", xhr.responseText || "Ocorreu um erro desconhecido.");
                    }
                    
                    if (this.onError) this.onError(xhr, status, error);
                },
                complete: () => {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = submitButton.dataset.originalText || 'Enviar';

                    }
                    if (this.onComplete) this.onComplete();
                }
            });

            return false;
        });
    }

    validateRequiredFields() {
        const camposObrigatorios = this.form.querySelectorAll('[required]');
        for (const campo of camposObrigatorios) {
            // Pular validação de campos de arquivo (file inputs)
            if (campo.type === 'file') {
                continue;
            }
            if (!campo.value.trim()) {
                return false;
            }
        }
        return true;
    }

    exibirErrosFormulario(formSelector, xhr) {
        const form = $(formSelector);
        let response;

        // Tentar parsear a resposta JSON
        try {
            response = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
        } catch (e) {
            response = {};
        }

        // Remove mensagens de erro anteriores
        form.find('.error-message').remove();
        form.find('.form-error').removeClass('form-error');

        // Verificar se houve erro na requisição ou se response.status == 'erro'
        if (xhr.status === 0 || xhr.status >= 500) {
            // Erro de rede ou servidor
            notificador.erro('Erro de conexão. Verifique sua internet e tente novamente.', null, { target: formSelector });
            return;
        }

        if (response.status === 'erro') {
            // Resposta com status de erro
            const mensagem = response.mensagem || 'Ocorreu um erro ao processar a solicitação.';
            notificador.erro(mensagem, null, { target: formSelector });
            
            // Se houver erros específicos de campos, também exibir
            if (response.erros) {
                this.exibirErrosCampos(form, response.erros);
            }
            return;
        }

        // Tratar erros de validação (422, 400, etc.)
        if (response && response.erros) {
            this.exibirErrosCampos(form, response.erros);
            
            // Notificação geral para erros de validação
            notificador.erro('Por favor, corrija os erros destacados no formulário.', null, { target: formSelector });
        } else {
            // Erro genérico
            const errorMsg = response?.mensagem || xhr.statusText || 'Ocorreu um erro ao processar a solicitação.';
            notificador.erro(errorMsg, null, { target: formSelector });
        }
    }
}

