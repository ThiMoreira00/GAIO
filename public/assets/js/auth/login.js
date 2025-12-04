const nomeFormulario = 'formulario-login'
const formularioLogin = document.getElementById(nomeFormulario);

formularioLogin.addEventListener('submit', function (event) {

    event.preventDefault();

    const formData = new FormData(formularioLogin);
    const submitButton = formularioLogin.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    const camposObrigatorios = formularioLogin.querySelectorAll('[required]');
    for (const campo of camposObrigatorios) {
        if (!campo.value.trim()) {
            notificador.aviso('Preencha todos os campos obrigatórios.', null, { target: `#${nomeFormulario}` });
            submitButton.disabled = false;
            return;
        }
    }

    // Enviar os dados do formulário via jQuery AJAX
    $.ajax({
        url: formularioLogin.action,
        type: formularioLogin.method,
        data: $(formularioLogin).serialize(),
        dataType: 'json',
        success: function (response) {
            notificador.sucesso(response.mensagem, null, { target: `#${nomeFormulario}` });
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);
        },
        error: function (xhr, status, error) {

            // Verifica se a resposta é JSON
            if (xhr.responseJSON && xhr.responseJSON.mensagem) {
                notificador.erro(xhr.responseJSON.mensagem, null, { target: `#${nomeFormulario}` });
            } else {
                notificador.erro(xhr.responseText || 'Ocorreu um erro desconhecido.', null, { target: `#${nomeFormulario}` });
            }

            console.error('Erro na requisição:', xhr.responseText, error);
        },
        complete: function () {
            submitButton.disabled = false;
        }
    });

    // Continuar na mesma página
    return false;
});