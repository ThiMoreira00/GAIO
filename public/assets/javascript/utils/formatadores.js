/**
 * Funções de formatação para campos de formulário
 */

/**
 * Formata campo de CPF (000.000.000-00)
 */
function formatarCPF(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length <= 11) {
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    
    input.value = valor;
}

/**
 * Formata campo de RG (00.000.000-0)
 */
function formatarRG(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length <= 9) {
        valor = valor.replace(/(\d{2})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d{1})$/, '$1-$2');
    }
    
    input.value = valor;
}

/**
 * Formata campo de CEP (00000-000)
 */
function formatarCEP(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length <= 8) {
        valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
    }
    
    input.value = valor;
}

/**
 * Formata telefone fixo (00) 0000-0000
 */
function formatarTelefoneFixo(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length <= 10) {
        valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
        valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
    }
    
    input.value = valor;
}

/**
 * Formata telefone celular (00) 00000-0000
 */
function formatarTelefoneCelular(input) {
    let valor = input.value.replace(/\D/g, '');
    
    if (valor.length <= 11) {
        valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
        valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
    }
    
    input.value = valor;
}

/**
 * Busca endereço pelo CEP usando ViaCEP API
 */
async function buscarCEP() {
    const cepInput = document.getElementById('cep');
    const cep = cepInput.value.replace(/\D/g, '');
    
    // Valida CEP
    if (cep.length !== 8) {
        alert('Por favor, digite um CEP válido com 8 dígitos.');
        cepInput.focus();
        return;
    }
    
    // Campos que serão preenchidos
    const enderecoInput = document.getElementById('endereco');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const ufInput = document.getElementById('uf');
    const complementoInput = document.getElementById('complemento');
    
    try {
        // Mostra loading no botão
        const botaoBuscar = event.target;
        const textoOriginal = botaoBuscar.innerHTML;
        botaoBuscar.disabled = true;
        botaoBuscar.innerHTML = '<span class="material-icons-sharp text-sm animate-spin">refresh</span> Buscando...';
        
        // Faz requisição para ViaCEP
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const dados = await response.json();
        
        // Restaura botão
        botaoBuscar.disabled = false;
        botaoBuscar.innerHTML = textoOriginal;
        
        // Verifica se CEP foi encontrado
        if (dados.erro) {
            alert('CEP não encontrado. Por favor, verifique e tente novamente.');
            cepInput.focus();
            return;
        }
        
        // Preenche os campos
        if (dados.logradouro) {
            enderecoInput.value = dados.logradouro;
            enderecoInput.classList.remove('border-red-500');
        }
        
        if (dados.bairro) {
            bairroInput.value = dados.bairro;
            bairroInput.classList.remove('border-red-500');
        }
        
        if (dados.localidade) {
            cidadeInput.value = dados.localidade;
            cidadeInput.classList.remove('border-red-500');
        }
        
        if (dados.uf) {
            ufInput.value = dados.uf;
            ufInput.classList.remove('border-red-500');
        }
        
        if (dados.complemento) {
            complementoInput.value = dados.complemento;
        }
        
        // Foca no campo número
        document.getElementById('numero').focus();
        
        // Notificação de sucesso
        const notificacao = document.createElement('div');
        notificacao.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50';
        notificacao.innerHTML = `
            <span class="material-icons-sharp text-sm">check_circle</span>
            <span>Endereço encontrado com sucesso!</span>
        `;
        document.body.appendChild(notificacao);
        
        setTimeout(() => {
            notificacao.remove();
        }, 3000);
        
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
        alert('Erro ao buscar CEP. Por favor, tente novamente.');
        
        // Restaura botão em caso de erro
        const botaoBuscar = event.target;
        botaoBuscar.disabled = false;
        botaoBuscar.innerHTML = '<span class="material-icons-sharp text-sm">search</span> Buscar';
    }
}