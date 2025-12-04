class AlteracoesNaoSalvas {
    constructor({ formulario, camposOriginais }) {
        this.formulario = (typeof formulario === 'string')
            ? document.querySelector(formulario)
            : formulario;

        if (!this.formulario) {
            throw new Error('Formulário não encontrado.');
        }

        this.camposOriginais = { ...camposOriginais };
        this.mensagem = this.criarMensagem();
        this.formModificado = false;

        this.inicializarListeners();
        this.registrarBeforeUnload();
    }

    // Cria a mensagem visual de alterações não salvas
    criarMensagem() {
        const mensagem = document.createElement('p');
        mensagem.className = 'text-sm text-gray-500 mx-4 text-orange-400';
        mensagem.textContent = 'Você possui alterações não salvas.';
        mensagem.style.display = 'none';

        const containerBotaoSubmit = this.formulario.querySelector('.flex.justify-end.items-center');
        if (containerBotaoSubmit) {
            containerBotaoSubmit.insertBefore(mensagem, containerBotaoSubmit.firstChild);
        }

        return mensagem;
    }

    // Captura os valores atuais com base nas chaves definidas
    obterCamposAtuais() {
        const campos = {};

        for (const chave in this.camposOriginais) {
            const elemento = this.formulario.querySelector(`#${chave}`)
                || document.querySelector(`#${chave}`); // fallback fora do form
            if (!elemento) continue;

            if (elemento.tagName === 'INPUT' || elemento.tagName === 'TEXTAREA' || elemento.tagName === 'SELECT') {
                if (elemento.type === 'checkbox' || elemento.type === 'radio') {
                    campos[chave] = elemento.checked;
                } else {
                    campos[chave] = elemento.value;
                }
            } else if (elemento.tagName === 'IMG') {
                campos[chave] = elemento.src;
            } else {
                campos[chave] = elemento.textContent;
            }
        }

        return campos;
    }

    // Verifica se houve alteração
    verificarAlteracoes() {
        const camposAtuais = this.obterCamposAtuais();

        const possuiAlteracoes = Object.keys(camposAtuais).some(campo =>
            camposAtuais[campo] !== this.camposOriginais[campo]
        );

        this.formModificado = possuiAlteracoes;
        this.mensagem.style.display = possuiAlteracoes ? 'block' : 'none';

        return possuiAlteracoes;
    }

    // Liga os listeners apenas nos campos que foram declarados
    inicializarListeners() {
        for (const chave in this.camposOriginais) {
            const elemento = this.formulario.querySelector(`#${chave}`)
                || document.querySelector(`#${chave}`);
            if (!elemento) continue;

            elemento.addEventListener('input', () => this.verificarAlteracoes());
            elemento.addEventListener('change', () => this.verificarAlteracoes());
        }

        // Resetar flag no submit
        this.formulario.addEventListener('submit', () => {
            this.formModificado = false;
        });
    }

    // Previne saída da página sem salvar
    registrarBeforeUnload() {
        window.addEventListener('beforeunload', (e) => {
            if (this.formModificado) {
                e.preventDefault();
            }
        });
    }

    // Atualizar estado original (após salvar via submit ou AJAX)
    atualizarEstadoOriginal() {
        this.camposOriginais = this.obterCamposAtuais();
        this.verificarAlteracoes();
    }
}