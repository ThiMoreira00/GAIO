class Modal2 {

    modal = null;
    etapaAtual = 0;
    totalEtapas = 0;
    etapas = [];
    formulario = null;
    permitirPular = false;
    etapasPulaveis = [];
    debug = false;
    // (sem sincronização automática de documentos)

    constructor(modalSelector = '.modal2', options = {}) {
        this.modal = document.querySelector(modalSelector);

        if (!this.modal) {
            console.error(`[Modal2] Modal com seletor "${modalSelector}" não encontrado.`);
            return;
        }
        
    // Configurações opcionais
    this.permitirPular = options.permitirPular || false;
    this.etapasPulaveis = options.etapasPulaveis || []; // Array de índices das etapas que podem ser puladas
    // Modo debug: quando true, permite pular etapas clicando no indicador sem validar os campos (útil para depuração)
    this.debug = options.debug || false;
        
        this.iniciar();
    }

    iniciar() {
        if (!this.modal) return;

        // Identifica todas as etapas
        this.etapas = Array.from(this.modal.querySelectorAll('.modal-etapa'));
        this.totalEtapas = this.etapas.length;

        // Identifica o formulário
        this.formulario = this.modal.querySelector('form');

        // Configura botões de navegação
        this.configurarBotoes();

        // Configura botões de fechar
        this.configurarBotoesFechar();

        // Configura clique nos indicadores
        this.configurarIndicadores();

        // Ajusta a linha de fundo após o DOM estar pronto
        setTimeout(() => this.ajustarLinhaFundo(), 100);

        // Inicializa na primeira etapa
        this.irParaEtapa(0);

        // Atualiza o indicador de progresso
        this.atualizarIndicadorProgresso();
    }

    configurarBotoes() {
        const btnVoltar = this.modal.querySelector('.btn-modal-voltar');
        const btnAvancar = this.modal.querySelector('.btn-modal-avancar');
        const btnFinalizar = this.modal.querySelector('.btn-modal-finalizar');

        if (btnVoltar) {
            btnVoltar.addEventListener('click', () => this.voltarEtapa());
        }

        if (btnAvancar) {
            btnAvancar.addEventListener('click', () => this.avancarEtapa());
        }

        if (btnFinalizar) {
            btnFinalizar.addEventListener('click', () => {
                if (this.formulario) {
                    this.formulario.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                }
            });
        }
    }

    configurarBotoesFechar() {
        const botoesFechar = this.modal.querySelectorAll('.button-modal-fechar');
        if (botoesFechar) {
            botoesFechar.forEach(botao => {
                botao.addEventListener('click', () => {
                    this.fechar();
                });
            });
        }
    }

    configurarIndicadores() {
        const indicadores = this.modal.querySelectorAll('.indicador-etapa');
        
        indicadores.forEach((indicador, index) => {
            // Aplica alinhamento automático (primeira à esquerda, última à direita, demais ao centro)
            this.aplicarAlinhamentoAutomatico(indicador, index, indicadores.length);
            
            // Verifica se a etapa pode ser pulada
            const podeSerPulada = this.permitirPular || this.etapasPulaveis.includes(index);

            // Se debug estiver ativado, permitimos clicar em qualquer indicador para pular diretamente (sem validação)
            const clicavel = podeSerPulada || this.debug === true;

            indicador.style.cursor = clicavel ? 'pointer' : 'default';

            if (clicavel) {
                indicador.addEventListener('click', (e) => {
                    if (this.debug) {
                        // Em debug, pular imediatamente sem validação
                        console.warn(`[Modal2] DEBUG ativo: pulando para a etapa ${index} sem validar campos.`);
                        this.irParaEtapa(index);
                        return;
                    }

                    // Comportamento normal: só pula se marcado como pulável
                    if (podeSerPulada) {
                        this.irParaEtapa(index);
                    }
                });

                // Adiciona classe para indicar que é clicável
                indicador.classList.add('etapa-pulavel');
            }
        });
    }

    aplicarAlinhamentoAutomatico(indicador, index, total) {
        const span = indicador.querySelector('span');
        
        if (index === 0) {
            // Primeira etapa - alinhada à esquerda
            indicador.classList.remove('items-center', 'items-end');
            indicador.classList.add('items-start');
            if (span) {
                span.classList.remove('text-center', 'text-right', 'px-1');
                span.classList.add('text-left');
            }
        } else if (index === total - 1) {
            // Última etapa - alinhada à direita
            indicador.classList.remove('items-center', 'items-start');
            indicador.classList.add('items-end');
            if (span) {
                span.classList.remove('text-center', 'text-left', 'px-1');
                span.classList.add('text-right');
            }
        } else {
            // Etapas do meio - centralizadas
            indicador.classList.remove('items-start', 'items-end');
            indicador.classList.add('items-center');
            if (span) {
                span.classList.remove('text-left', 'text-right');
                span.classList.add('text-center', 'px-1');
            }
        }
    }

    ajustarLinhaFundo() {
        const container = this.modal.querySelector('.relative.flex.items-center.justify-between');
        if (!container) return;

        const indicadores = this.modal.querySelectorAll('.indicador-etapa');
        if (indicadores.length < 2) return;

        const linhaFundo = container.querySelector('.absolute.bg-gray-300');
        if (!linhaFundo) return;

        const primeiraEtapa = indicadores[0];
        const ultimaEtapa = indicadores[this.totalEtapas - 1];

        const primeiroBolinha = primeiraEtapa.querySelector('.ponto-etapa');
        const ultimaBolinha = ultimaEtapa.querySelector('.ponto-etapa');

        if (!primeiroBolinha || !ultimaBolinha) return;

        // Calcula as posições relativas ao container
        const containerRect = container.getBoundingClientRect();
        const primeiraBolinhaRect = primeiroBolinha.getBoundingClientRect();
        const ultimaBolinhaRect = ultimaBolinha.getBoundingClientRect();

        // Calcula o offset inicial e final (centro das bolinhas)
        const offsetInicial = primeiraBolinhaRect.left - containerRect.left + (primeiraBolinhaRect.width / 2);
        const offsetFinal = ultimaBolinhaRect.left - containerRect.left + (ultimaBolinhaRect.width / 2);
        const larguraTotal = offsetFinal - offsetInicial;

        // Atualiza a linha de fundo
        linhaFundo.style.left = `${offsetInicial}px`;
        linhaFundo.style.width = `${larguraTotal}px`;
        linhaFundo.style.right = 'auto';
    }

    abrir() {
        if (this.modal) {
            this.modal.classList.remove("hidden");
            this.modal.classList.add("flex");
            // Bloqueia o scroll do body para que apenas o conteúdo do modal seja rolável
            try {
                // guarda valores anteriores para restaurar depois
                this._previousBodyOverflow = document.body.style.overflow || '';
                this._previousBodyPaddingRight = document.body.style.paddingRight || '';

                // evita 'jump' quando a barra de rolagem vertical desaparecer
                const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollbarWidth > 0) {
                    document.body.style.paddingRight = `${scrollbarWidth}px`;
                }

                document.body.style.overflow = 'hidden';
            } catch (e) {
                // se algo falhar, não quebrar o modal
                console.warn('[Modal2] Não foi possível bloquear o scroll do body:', e);
            }

            this.irParaEtapa(0); // Sempre começa na primeira etapa

            // sem sincronização automática de documentos aqui
        }
    }

    fechar() {
        if (this.modal) {
            this.modal.classList.remove("flex");
            this.modal.classList.add("hidden");
            // Restaura o scroll do body e padding-right anterior
            try {
                document.body.style.overflow = this._previousBodyOverflow || '';
                document.body.style.paddingRight = this._previousBodyPaddingRight || '';
                this._previousBodyOverflow = undefined;
                this._previousBodyPaddingRight = undefined;
            } catch (e) {
                console.warn('[Modal2] Não foi possível restaurar o scroll do body:', e);
            }

            // sem listeners adicionais para sincronização de CPF/RG

            this.limparMensagensErro();
        }
    }

    irParaEtapa(numeroEtapa) {
        if (numeroEtapa < 0 || numeroEtapa >= this.totalEtapas) return;

        // Esconde todas as etapas
        this.etapas.forEach(etapa => {
            etapa.classList.add('hidden');
        });

        // Mostra apenas a etapa atual
        this.etapas[numeroEtapa].classList.remove('hidden');
        this.etapaAtual = numeroEtapa;

        // Atualiza os botões de navegação
        this.atualizarBotoesNavegacao();

        // Atualiza o indicador de progresso
        this.atualizarIndicadorProgresso();
    }

    avancarEtapa() {
        if (this.etapaAtual < this.totalEtapas - 1) {
            // Valida os campos da etapa atual antes de avançar
            if (this.validarEtapaAtual()) {
                this.irParaEtapa(this.etapaAtual + 1);
            }
        }
    }

    voltarEtapa() {
        if (this.etapaAtual > 0) {
            this.irParaEtapa(this.etapaAtual - 1);
        }
    }

    validarEtapaAtual() {
        const etapaAtualElement = this.etapas[this.etapaAtual];
        const camposObrigatorios = etapaAtualElement.querySelectorAll('[required]');
        
        // Remove mensagens de erro anteriores
        this.limparMensagensErro();
        
        let valido = true;
        camposObrigatorios.forEach(campo => {
            if (!campo.checkValidity()) {
                campo.classList.add('border-red-500');
                
                // Cria e adiciona a mensagem de erro
                this.adicionarMensagemErro(campo);
                
                valido = false;
            } else {
                campo.classList.remove('border-red-500');
            }
        });

        return valido;
    }

    adicionarMensagemErro(campo) {
        // Verifica se já existe uma mensagem de erro para este campo
        const campoContainer = campo.parentElement.parentElement || campo.parentElement;
        const mensagemExistente = campoContainer.querySelector('.mensagem-erro-campo');
        
        if (mensagemExistente) return;

        // Cria a mensagem de erro
        const mensagemErro = document.createElement('p');
        mensagemErro.className = 'mensagem-erro-campo text-xs mt-1 text-red-600';
        
        // Define a mensagem baseada no tipo de validação
        let textoErro = '';
        
        if (campo.validity.valueMissing) {
            textoErro = 'Este campo é obrigatório.';
        } else if (campo.validity.typeMismatch) {
            if (campo.type === 'email') {
                textoErro = 'Por favor, insira um e-mail válido.';
            } else if (campo.type === 'tel') {
                textoErro = 'Por favor, insira um telefone válido.';
            } else {
                textoErro = 'Por favor, insira um valor válido.';
            }
        } else if (campo.validity.patternMismatch) {
            textoErro = 'O formato inserido não é válido.';
        } else if (campo.validity.tooShort) {
            textoErro = `Mínimo de ${campo.minLength} caracteres.`;
        } else if (campo.validity.tooLong) {
            textoErro = `Máximo de ${campo.maxLength} caracteres.`;
        } else if (campo.validity.rangeUnderflow) {
            textoErro = `Valor mínimo: ${campo.min}.`;
        } else if (campo.validity.rangeOverflow) {
            textoErro = `Valor máximo: ${campo.max}.`;
        } else {
            textoErro = 'Por favor, preencha este campo corretamente.';
        }
        
        mensagemErro.textContent = textoErro;
        
        // Adiciona a mensagem após o campo (ou após o container relativo)
        const campoRelativo = campo.closest('.relative') || campo;
        if (campoRelativo.nextSibling) {
            campoRelativo.parentNode.insertBefore(mensagemErro, campoRelativo.nextSibling);
        } else {
            campoRelativo.parentNode.appendChild(mensagemErro);
        }
    }

    limparMensagensErro() {
        const mensagensErro = this.modal.querySelectorAll('.mensagem-erro-campo');
        mensagensErro.forEach(mensagem => mensagem.remove());
        
        // Remove bordas vermelhas
        const camposComErro = this.modal.querySelectorAll('.border-red-500');
        camposComErro.forEach(campo => campo.classList.remove('border-red-500'));
    }

    atualizarBotoesNavegacao() {
        const btnVoltar = this.modal.querySelector('.btn-modal-voltar');
        const btnAvancar = this.modal.querySelector('.btn-modal-avancar');
        const btnFinalizar = this.modal.querySelector('.btn-modal-finalizar');

        // Botão Voltar
        if (btnVoltar) {
            if (this.etapaAtual === 0) {
                btnVoltar.classList.add('hidden');
            } else {
                btnVoltar.classList.remove('hidden');
            }
        }

        // Botão Avançar
        if (btnAvancar) {
            if (this.etapaAtual === this.totalEtapas - 1) {
                btnAvancar.classList.add('hidden');
            } else {
                btnAvancar.classList.remove('hidden');
            }
        }

        // Botão Finalizar
        if (btnFinalizar) {
            if (this.etapaAtual === this.totalEtapas - 1) {
                btnFinalizar.classList.remove('hidden');
            } else {
                btnFinalizar.classList.add('hidden');
            }
        }
    }

    atualizarIndicadorProgresso() {
        const indicadores = this.modal.querySelectorAll('.indicador-etapa');
        
        // Atualiza os pontos
        indicadores.forEach((indicador, index) => {
            const ponto = indicador.querySelector('.ponto-etapa');

            if (index < this.etapaAtual) {
                // Etapas já concluídas
                if (ponto) {
                    ponto.classList.add('bg-blue-600', 'border-blue-600');
                    ponto.classList.remove('bg-white', 'border-gray-300');
                }
            } else if (index === this.etapaAtual) {
                // Etapa atual
                if (ponto) {
                    ponto.classList.add('bg-blue-600', 'border-blue-600');
                    ponto.classList.remove('bg-white', 'border-gray-300');
                }
            } else {
                // Etapas futuras
                if (ponto) {
                    ponto.classList.remove('bg-blue-600', 'border-blue-600');
                    ponto.classList.add('bg-white', 'border-gray-300');
                }
            }
        });

        // Atualiza a linha de progresso
        this.atualizarLinhaProgresso();
    }

    atualizarLinhaProgresso() {
        if (this.totalEtapas <= 1) return;

        const container = this.modal.querySelector('.relative.flex.items-center.justify-between');
        if (!container) return;

        const indicadores = this.modal.querySelectorAll('.indicador-etapa');
        if (indicadores.length === 0) return;

        // Procura ou cria linha de progresso
        let linhaProgresso = container.querySelector('.linha-progresso-ativa');
        
        if (!linhaProgresso) {
            // Cria nova linha de progresso
            linhaProgresso = document.createElement('div');
            linhaProgresso.className = 'linha-progresso-ativa absolute bg-blue-600 transition-all duration-500 ease-in-out';
            linhaProgresso.style.position = 'absolute';
            linhaProgresso.style.top = '5px'; // Ajustado para coincidir exatamente com a linha cinza
            linhaProgresso.style.left = '0';
            linhaProgresso.style.height = '2px';
            linhaProgresso.style.zIndex = '1';
            linhaProgresso.style.pointerEvents = 'none';
            
            // Insere no container
            container.appendChild(linhaProgresso);
        }

        // Calcula a posição real das bolinhas
        const primeiraEtapa = indicadores[0];
        const ultimaEtapa = indicadores[this.totalEtapas - 1];
        const etapaAtualElement = indicadores[this.etapaAtual];

        if (!primeiraEtapa || !ultimaEtapa || !etapaAtualElement) return;

        // Pega a posição da primeira bolinha (offset left)
        const primeiroBolinha = primeiraEtapa.querySelector('.ponto-etapa');
        const bolinhaDaEtapaAtual = etapaAtualElement.querySelector('.ponto-etapa');

        if (!primeiroBolinha || !bolinhaDaEtapaAtual) return;

        // Calcula as posições relativas ao container
        const containerRect = container.getBoundingClientRect();
        const primeiraBolinhaRect = primeiroBolinha.getBoundingClientRect();
        const bolinhaAtualRect = bolinhaDaEtapaAtual.getBoundingClientRect();

        // Calcula o offset inicial (centro da primeira bolinha)
        const offsetInicial = primeiraBolinhaRect.left - containerRect.left + (primeiraBolinhaRect.width / 2);
        
        // Calcula a largura até o centro da bolinha atual
        const larguraAteBolinhaAtual = bolinhaAtualRect.left - containerRect.left + (bolinhaAtualRect.width / 2);

        // Atualiza a linha de progresso
        requestAnimationFrame(() => {
            linhaProgresso.style.left = `${offsetInicial}px`;
            linhaProgresso.style.width = `${larguraAteBolinhaAtual - offsetInicial}px`;
        });
    }

    limparCampos() {
        if (this.modal) {
            const inputs = this.modal.querySelectorAll("input, textarea, select");
            inputs.forEach(input => {
                input.value = "";
            });
        }
    }

    // sem funções de preenchimento automático de documentos

    resetar() {
        this.limparCampos();
        this.limparMensagensErro();
        this.irParaEtapa(0);
    }

}
