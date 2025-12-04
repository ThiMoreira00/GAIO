/**
 * Formata o valor do CPF enquanto o usuário digita
 * Formato: 000.000.000-00
 * Limita a entrada a 11 dígitos numéricos
 * 
 * @param {HTMLInputElement} input - O campo de entrada do CPF
 * @returns {string} - O valor formatado do CPF
 */
function formatarCPF(input) {
    let valor = input.value.replace(/\D/g, '');
    
    // Limita a 11 dígitos
    if (valor.length > 11) {
        valor = valor.substring(0, 11);
    }
    
    // Aplica a formatação progressiva
    if (valor.length > 0) {
        if (valor.length <= 3) {
            valor = valor;
        } else if (valor.length <= 6) {
            valor = valor.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        } else if (valor.length <= 9) {
            valor = valor.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        } else {
            valor = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        }
    }
    
    input.value = valor;
    return input.value;
}

/**
 * Formata o valor do RG enquanto o usuário digita
 * Formato: 00.000.000-0
 * Limita a entrada a 9 dígitos numéricos
 * Caso tenha 11 dígitos, formata como CPF automaticamente
 * 
 * @param {HTMLInputElement} input - O campo de entrada do RG
 * @returns {string} - O valor formatado do RG
 */
function formatarRG(input) {
    let valor = input.value.replace(/\D/g, '');
    
    // Limita a 9 dígitos (mas aceita 11 para CPF)
    if (valor.length > 11) {
        valor = valor.substring(0, 11);
    }
    
    // Se tiver 11 caracteres, formata como CPF
    if (valor.length === 11) {
        input.value = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        return input.value;
    }
    
    // Aplica a formatação progressiva do RG
    if (valor.length > 0) {
        if (valor.length <= 2) {
            valor = valor;
        } else if (valor.length <= 5) {
            valor = valor.replace(/(\d{2})(\d{1,3})/, '$1.$2');
        } else if (valor.length <= 8) {
            valor = valor.replace(/(\d{2})(\d{3})(\d{1,3})/, '$1.$2.$3');
        } else {
            valor = valor.replace(/(\d{2})(\d{3})(\d{3})(\d{1})/, '$1.$2.$3-$4');
        }
    }
    
    input.value = valor;
    return input.value;
}

/**
 * Formata o valor do CEP enquanto o usuário digita
 * Formato: 00000-000
 * Limita a entrada a 8 dígitos numéricos
 * 
 * @param {HTMLInputElement} input - O campo de entrada do CEP
 * @returns {string} - O valor formatado do CEP
 */
function formatarCEP(input) {
    let valor = input.value.replace(/\D/g, '');
    
    // Limita a 8 dígitos
    if (valor.length > 8) {
        valor = valor.substring(0, 8);
    }
    
    // Aplica a formatação
    if (valor.length > 5) {
        valor = valor.replace(/(\d{5})(\d{1,3})/, '$1-$2');
    }
    
    input.value = valor;
    return input.value;
}

/**
 * Formata o valor do telefone fixo enquanto o usuário digita
 * Formato: (00) 0000-0000
 * Limita a entrada a 10 dígitos numéricos
 * 
 * @param {HTMLInputElement} input - O campo de entrada do telefone fixo
 * @returns {string} - O valor formatado do telefone
 */
function formatarTelefoneFixo(input) {
    let valor = input.value.replace(/\D/g, '');
    
    // Limita a 10 dígitos
    if (valor.length > 10) {
        valor = valor.substring(0, 10);
    }
    
    // Aplica a formatação
    if (valor.length > 0) {
        if (valor.length <= 2) {
            valor = valor.replace(/(\d{1,2})/, '($1');
        } else if (valor.length <= 6) {
            valor = valor.replace(/(\d{2})(\d{1,4})/, '($1) $2');
        } else {
            valor = valor.replace(/(\d{2})(\d{4})(\d{1,4})/, '($1) $2-$3');
        }
    }
    
    input.value = valor;
    return input.value;
}

/**
 * Formata o valor do telefone celular enquanto o usuário digita
 * Formato: (00) 00000-0000
 * Limita a entrada a 11 dígitos numéricos
 * 
 * @param {HTMLInputElement} input - O campo de entrada do telefone celular
 * @returns {string} - O valor formatado do telefone
 */
function formatarTelefoneCelular(input) {
    let valor = input.value.replace(/\D/g, '');
    
    // Limita a 11 dígitos
    if (valor.length > 11) {
        valor = valor.substring(0, 11);
    }
    
    // Aplica a formatação
    if (valor.length > 0) {
        if (valor.length <= 2) {
            valor = valor.replace(/(\d{1,2})/, '($1');
        } else if (valor.length <= 7) {
            valor = valor.replace(/(\d{2})(\d{1,5})/, '($1) $2');
        } else {
            valor = valor.replace(/(\d{2})(\d{5})(\d{1,4})/, '($1) $2-$3');
        }
    }
    
    input.value = valor;
    return input.value;
}

/**
 * Busca endereço pelo CEP usando a API ViaCEP
 * Preenche automaticamente os campos: endereço, bairro, cidade, UF e complemento
 * Exibe notificações de sucesso ou erro para o usuário
 * 
 * @returns {Promise<void>}
 */
async function buscarCEP(cepInput) {
    const cep = cepInput.value.replace(/\D/g, '');
    
    // Valida CEP
    if (cep.length !== 8) {
        notificador.aviso('Por favor, insira um CEP válido com 8 dígitos.', null, { alvo: cepInput });
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
        botaoBuscar.innerHTML = '<span class="material-icons-sharp text-sm animate-spin">refresh</span>';
        
        // Faz requisição para ViaCEP
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const dados = await response.json();
        
        // Restaura botão
        botaoBuscar.disabled = false;
        botaoBuscar.innerHTML = textoOriginal;
        
        // Verifica se CEP foi encontrado
        if (dados.erro) {
            notificador.erro('CEP não encontrado. Por favor, verifique e tente novamente.', null, { alvo: cepInput });
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
        notificador.sucesso('Endereço encontrado com sucesso!', null, { alvo: enderecoInput });
        
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
        notificador.erro('Erro ao buscar CEP. Por favor, tente novamente.', null, { alvo: cepInput });

        // Restaura botão em caso de erro
        const botaoBuscar = event.target;
        botaoBuscar.disabled = false;
        botaoBuscar.innerHTML = '<span class="material-icons-sharp text-sm">search</span> Buscar';
    }
}