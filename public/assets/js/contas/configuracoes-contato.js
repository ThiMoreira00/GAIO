/*
======================================
    CONFIGURAÇÕES - Seção CONTATO
=========================================
 */

// Declaração de variáveis

const informacoesContato = {
    cep: document.querySelector('#formulario-informacoes-contato #cep').value,
    endereco: document.querySelector('#formulario-informacoes-contato #endereco').value,
    numero: document.querySelector('#formulario-informacoes-contato #numero').value,
    complemento: document.querySelector('#formulario-informacoes-contato #complemento').value,
    bairro: document.querySelector('#formulario-informacoes-contato #bairro').value,
    cidade: document.querySelector('#formulario-informacoes-contato #cidade').value,
    uf: document.querySelector('#formulario-informacoes-contato #uf').value,
    telefone_fixo: document.querySelector('#formulario-informacoes-contato #telefone-fixo').value,
    telefone_celular: document.querySelector('#formulario-informacoes-contato #telefone-celular').value,
};


document.addEventListener('DOMContentLoaded', function () {
    const formularioInformacoesContato = document.querySelector('#formulario-informacoes-contato');
    const mensagemAlteracoes = document.createElement('p');

    // Configura a mensagem de alterações não salvas
    mensagemAlteracoes.className = 'alteracoes-nao-salvas text-sm text-gray-500 mx-4 text-orange-400';
    mensagemAlteracoes.textContent = 'Você possui alterações não salvas.';
    mensagemAlteracoes.style.display = 'none';

    // Adiciona a mensagem antes do botão de submit
    const containerBotaoSubmit = formularioInformacoesContato.querySelector('#formulario-informacoes-contato-rodape');
    containerBotaoSubmit.insertBefore(mensagemAlteracoes, containerBotaoSubmit.firstChild);

    // Função para verificar alterações
    function verificarAlteracoes() {
        const camposAtuais = {
            cep: formularioInformacoesContato.querySelector('#cep').value,
            endereco: formularioInformacoesContato.querySelector('#endereco').value,
            numero: formularioInformacoesContato.querySelector('#numero').value,
            complemento: formularioInformacoesContato.querySelector('#complemento').value,
            bairro: formularioInformacoesContato.querySelector('#bairro').value,
            cidade: formularioInformacoesContato.querySelector('#cidade').value,
            uf: formularioInformacoesContato.querySelector('#uf').value,
            telefone_fixo: formularioInformacoesContato.querySelector('#telefone-fixo').value,
            telefone_celular: formularioInformacoesContato.querySelector('#telefone-celular').value
        };

        const possuiAlteracoes = Object.keys(camposAtuais).some(campo =>
            camposAtuais[campo] !== informacoesContato[campo]
        );

        console.log(possuiAlteracoes);

        mensagemAlteracoes.style.display = possuiAlteracoes ? 'block' : 'none';
    }

    formularioInformacoesContato.querySelector('button#pesquisar-cep').addEventListener('click', async function () {
        const cep = formularioInformacoesContato.querySelector('#cep').value.replace(/\D/g, '');

        if (cep.length !== 8) {
            notificador.mostrarAlerta('aviso', 'CEP inválido! Insira 8 dígitos.', null, { target: '#formulario-informacoes-contato' });
            return;
        }

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (data.erro) {
                notificador.mostrarAlerta('aviso', 'CEP não encontrado.', null, { target: '#formulario-informacoes-contato' });
                return;
            }

            formularioInformacoesContato.querySelector('#endereco').value = data.logradouro || '';
            formularioInformacoesContato.querySelector('#complemento').value = data.complemento || '';
            formularioInformacoesContato.querySelector('#bairro').value = data.bairro || '';
            formularioInformacoesContato.querySelector('#cidade').value = data.localidade || '';
            formularioInformacoesContato.querySelector('#uf').value = data.uf || '';

            verificarAlteracoes();

        } catch (error) {
            console.error(error);
        }
    });

    // Adiciona listeners para os campos do formulário
    ['cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'uf', 'telefone-fixo', 'telefone-celular', 'email-pessoal'].forEach(campo => {
        const elemento = document.querySelector(`#formulario-informacoes-contato #${campo}`);
        if (elemento) {
            elemento.addEventListener('input', verificarAlteracoes);
            elemento.addEventListener('change', verificarAlteracoes);
        }
    });

    // Formatador de CEP
    const cepInput = document.querySelector('#cep');
    cepInput.addEventListener('change', function () {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            this.value = cep.replace(/(\d{5})(\d)/, '$1-$2');
        }
    });



});



