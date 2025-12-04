/*
======================================
    CONFIGURAÇÕES - Seção CONTA
=========================================
 */

// Declaração de variáveis
const formularios = document.getElementsByTagName('form');

const informacoesConta = {
    nome_civil: document.querySelector('#formulario-informacoes-conta #nome-civil').value,
    nome_social: document.querySelector('#formulario-informacoes-conta #nome-social').value,
    email_pessoal: document.querySelector('#formulario-informacoes-conta #email-pessoal').value,
    email_institucional: document.querySelector('#formulario-informacoes-conta #email-institucional').value,
    foto: document.querySelector('#foto').src
};

// Configurações para manipulação de foto
const configuracoesFoto = {
    inputFoto: document.querySelector('#imagem-perfil'),
    previewFoto: document.querySelector('#foto'),
    botaoAlterar: document.querySelector('#alterarFotoButton'),
    botaoRemover: document.querySelector('#removerFotoButton'),
    urlFotoPadrao: '/assets/img/usuario-padrao.png',
    fotoPadrao: true
};

// Configurações para alteração de senha
const configuracoesSenha = {
    formulario: document.querySelector('#formulario-senha'),
    campoSenhaAtual: document.querySelector('#senha-atual'),
    campoNovaSenha: document.querySelector('#senha-nova'),
    campoConfirmarSenha: document.querySelector('#senha-confirmacao'),
    mensagemSenha: document.querySelector('#password-match-msg'),
    botoesAlternarVisualizacao: document.querySelectorAll('.js-password-toggle'),
    requisitos: {
        comprimento: document.querySelector('#req-length'),
        caixas: document.querySelector('#req-case'),
        especial: document.querySelector('#req-special'),
        anterior: document.querySelector('#req-previous')
    }
};


document.addEventListener('DOMContentLoaded', function () {
    const formularioInformacoesConta = document.querySelector('#formulario-informacoes-conta');
    const mensagemAlteracoes = document.createElement('p');

    // Configura a mensagem de alterações não salvas
    mensagemAlteracoes.className = 'text-sm text-gray-500 mx-4 text-orange-400';
    mensagemAlteracoes.textContent = 'Você possui alterações não salvas.';
    mensagemAlteracoes.style.display = 'none';

    // Adiciona a mensagem antes do botão de submit
    const containerBotaoSubmit = formularioInformacoesConta.querySelector('.flex.justify-end.items-center');
    containerBotaoSubmit.insertBefore(mensagemAlteracoes, containerBotaoSubmit.firstChild);

    // Função para verificar alterações
    function verificarAlteracoes() {
        const camposAtuais = {
            nome: document.querySelector('#formulario-informacoes-conta #nome-civil').value,
            nome_social: document.querySelector('#formulario-informacoes-conta #nome-social').value,
            email_pessoal: document.querySelector('#formulario-informacoes-conta #email-pessoal').value,
            email_institucional: document.querySelector('#formulario-informacoes-conta #email-institucional').value,
            foto: document.querySelector('#foto').src
        };

        const possuiAlteracoes = Object.keys(camposAtuais).some(campo =>
            camposAtuais[campo] !== informacoesConta[campo]
        );

        console.log(possuiAlteracoes);

        mensagemAlteracoes.style.display = possuiAlteracoes ? 'block' : 'none';
    }

    // Funções para manipulação de foto
    function inicializarManipulacaoFoto() {
        // Verifica se a foto atual é a padrão
        configuracoesFoto.fotoPadrao = configuracoesFoto.previewFoto.src.includes('usuario-padrao.png');

        // Atualiza a visibilidade do botão de remover
        configuracoesFoto.botaoRemover.style.display = configuracoesFoto.fotoPadrao ? 'none' : 'inline-block';
    }

    function alterarFoto() {
        configuracoesFoto.inputFoto.click();
    }

    function removerFoto() {
        configuracoesFoto.previewFoto.src = configuracoesFoto.urlFotoPadrao;
        configuracoesFoto.inputFoto.value = '';
        configuracoesFoto.fotoPadrao = true;
        configuracoesFoto.botaoRemover.style.display = 'none';
        verificarAlteracoes();
    }

    function visualizarFoto(evento) {
        const arquivo = evento.target.files[0];
        if (arquivo) {
            const leitor = new FileReader();

            leitor.onload = function(e) {
                configuracoesFoto.previewFoto.src = e.target.result;
                configuracoesFoto.fotoPadrao = false;
                configuracoesFoto.botaoRemover.style.display = 'inline-block';
                verificarAlteracoes();
            };

            leitor.readAsDataURL(arquivo);
        }
    }

    // Adiciona listeners para os campos do formulário
    ['nome', 'nome-social', 'email-pessoal', 'email-institucional', 'foto'].forEach(campo => {
        const elemento = document.querySelector(`#formulario-informacoes-conta #${campo}`);
        if (elemento) {
            elemento.addEventListener('input', verificarAlteracoes);
            elemento.addEventListener('change', verificarAlteracoes);
        }
    });

    // Listeners para manipulação de foto
    configuracoesFoto.botaoAlterar.addEventListener('click', alterarFoto);
    configuracoesFoto.botaoRemover.addEventListener('click', removerFoto);
    configuracoesFoto.inputFoto.addEventListener('change', visualizarFoto);

    // Inicializa a manipulação de foto
    inicializarManipulacaoFoto();

    // Funções para alteração de senha
    function alternarVisualizacaoSenha(evento) {
        const botao = evento.currentTarget;
        const campoSenha = botao.closest('.relative').querySelector('input');

        if (campoSenha.type === 'password') {
            campoSenha.type = 'text';
            botao.setAttribute('aria-label', 'Esconder senha');
        } else {
            campoSenha.type = 'password';
            botao.setAttribute('aria-label', 'Mostrar senha');
        }
    }

    function validarSenha(senha) {
        const regras = {
            comprimento: senha.length >= 8 && senha.length <= 32,
            caixas: /(?=.*[a-z])(?=.*[A-Z])/.test(senha),
            especial: /[!@#?]/.test(senha)
        };

        // Atualiza visualmente os requisitos
        Object.keys(regras).forEach(regra => {
            const elemento = configuracoesSenha.requisitos[regra];
            if (elemento) {
                if (regras[regra]) {
                    elemento.classList.add('text-green-500');
                    elemento.classList.remove('text-gray-500');
                } else {
                    elemento.classList.add('text-gray-500');
                    elemento.classList.remove('text-green-500');
                }
            }
        });

        return Object.values(regras).every(rule => rule === true);
    }

    function verificarSenhasCorrespondem() {
        const novaSenha = configuracoesSenha.campoNovaSenha.value;
        const confirmacaoSenha = configuracoesSenha.campoConfirmarSenha.value;

        if (confirmacaoSenha) {
            if (novaSenha === confirmacaoSenha) {
                configuracoesSenha.mensagemSenha.textContent = 'As senhas correspondem';
                configuracoesSenha.mensagemSenha.className = 'text-sm mt-2 text-green-500';
                return true;
            } else {
                configuracoesSenha.mensagemSenha.textContent = 'As senhas não correspondem';
                configuracoesSenha.mensagemSenha.className = 'text-sm mt-2 text-red-500';
                return false;
            }
        }
        return false;
    }

    // Event Listeners para senha
    configuracoesSenha.botoesAlternarVisualizacao.forEach(botao => {
        botao.addEventListener('click', alternarVisualizacaoSenha);
    });

    configuracoesSenha.campoNovaSenha.addEventListener('input', function() {
        validarSenha(this.value);
        verificarSenhasCorrespondem();
    });

    configuracoesSenha.campoConfirmarSenha.addEventListener('input', verificarSenhasCorrespondem);

});



