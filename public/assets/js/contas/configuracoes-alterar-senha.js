document.addEventListener('DOMContentLoaded', function () {
    const inputSenhaNova = document.getElementById('senha-nova');
    const inputSenhaConfirmacao = document.getElementById('senha-confirmacao');
    const mensagemValidadorSenha = document.getElementById('password-match-msg');

    const requirements = {
        length: document.getElementById('req-length'),
        case: document.getElementById('req-case'),
        special: document.getElementById('req-special'),
    };

    // Objeto para armazenar o estado de validação de cada requisito
    const validationState = {
        length: false,
        case: false,
        special: false
    };

    // Função para atualizar a UI de um requisito
    const updateRequirementUI = (element, isValid) => {
        const iconeTextoSpan = element.querySelector('span');

        // Cores baseadas em Tailwind CSS
        const validColor = 'text-green-600';
        const invalidColor = 'text-red-600';
        const neutralColor = 'text-gray-500';

        // Remove as classes de cor existentes
        element.classList.remove(validColor, invalidColor, neutralColor);
        iconeTextoSpan.classList.remove(validColor, invalidColor, neutralColor);

        // Adiciona a classe de cor apropriada
        const colorClass = isValid ? validColor : invalidColor;
        element.classList.add(colorClass);
        iconeTextoSpan.classList.add(colorClass);
    };

    // Função para resetar a UI para o estado neutro
    const resetRequirementsUI = () => {
        Object.values(requirements).forEach(element => {
            const iconeTextoSpan = element.querySelector('span');
            const validColor = 'text-green-600';
            const invalidColor = 'text-red-600';
            const neutralColor = 'text-gray-500';

            element.classList.remove(validColor, invalidColor);
            iconeTextoSpan.classList.remove(validColor, invalidColor);

            element.classList.add(neutralColor);
            iconeTextoSpan.classList.add(neutralColor);
        });
    };

    // Event listener para o campo "Nova senha"
    inputSenhaNova.addEventListener('input', () => {
        const pass = inputSenhaNova.value;

        // Se o campo estiver vazio, reseta tudo para o estado neutro
        if (pass.length === 0) {
            resetRequirementsUI();
            checkPasswordMatch(); // Limpa a mensagem de confirmação também
            return;
        }

        // 1. Validação de Comprimento
        validationState.length = pass.length >= 8 && pass.length <= 32;
        updateRequirementUI(requirements.length, validationState.length);

        // 2. Validação de Maiúscula/Minúscula
        validationState.case = /[a-z]/.test(pass) && /[A-Z]/.test(pass);
        updateRequirementUI(requirements.case, validationState.case);

        // 3. Validação de Caractere Especial
        validationState.special = /[!@#?]/.test(pass);
        updateRequirementUI(requirements.special, validationState.special);

        // Checa a confirmação de senha toda vez que a senha principal muda
        checkPasswordMatch();
    });

    // Função para checar se as senhas coincidem
    const checkPasswordMatch = () => {
        const newPass = inputSenhaNova.value;
        const confirmPass = inputSenhaConfirmacao.value;

        if (confirmPass.length === 0 && newPass.length === 0) {
            mensagemValidadorSenha.textContent = '';
            return;
        }

        if (newPass === confirmPass) {
            mensagemValidadorSenha.textContent = 'As senhas coincidem.';
            mensagemValidadorSenha.className = 'text-sm mt-2 text-green-600';
        } else {
            mensagemValidadorSenha.textContent = 'As senhas não coincidem.';
            mensagemValidadorSenha.className = 'text-sm mt-2 text-red-600';
        }
    };

    // Event listener para o campo "Confirmar nova senha"
    inputSenhaConfirmacao.addEventListener('input', checkPasswordMatch);


    // ---- Funcionalidade do botão de visibilidade ----
    const togglePasswordButtons = document.querySelectorAll('.js-password-toggle');

    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', () => {
            const input = button.parentElement.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        });
    });

});