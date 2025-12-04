<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= obterURL('/assets/img/gaio-icone-azul.ico'); ?>" type="image/x-icon">
    <link href="<?= obterURL('/assets/css/tailwindcss-output.css'); ?>" rel="stylesheet">
    <link href="<?= obterURL('/assets/css/google-material-icons-sharp.css'); ?>" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <title>Login | GAIO</title>
</head>

<body class="flex flex-col justify-center items-center h-dvh" style="background-image: linear-gradient(135deg, #e0f0ff, #1a6aad); background-size: contain;"> <!--  --> <!-- background-image: linear-gradient(134deg, #acd7ff, #0f3f67); -->
<header class="sm:mx-auto sm:w-full sm:max-w-sm mb-8">
     <img src="<?= obterURL('/assets/img/gaio-logo-colorida.png'); ?>" alt="Logotipo do Sistema GAIO" class="mx-auto mt-4" width="250px" height="auto">
</header>

<main class="w-100 md:w-2/3 lg:w-1/2 xl:w-lg bg-white shadow-md rounded mx-auto">
    <section class="flex min-h-full flex-col justify-center px-8 py-4 lg:px-8">

        <div class="my-8 sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="pb-8 text-center text-2xl/9 font-semibold text-gray-900">Faça login</h2>

            <form id="formulario-login" class="space-y-6" action="/login" method="POST" novalidate>
                <fieldset>
                    <label for="nome_acesso" class="form-label mb-2 required">
                        Nome de acesso
                    </label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-icons-sharp h-5 w-5 text-gray-400">person</span>
                        </div>
                        <input type="text" name="nome_acesso" id="nome_acesso" class="form-input pl-10" autocomplete="nome_acesso" placeholder="nome.sobrenome" required>
                    </div>
                </fieldset>

                <fieldset>
                    <label for="senha" class="form-label mb-2 required">
                        Senha
                    </label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-icons-sharp h-5 w-5 text-gray-400">password</span>
                        </div>
                        <input type="password" name="senha" id="senha" class="form-input pl-10" autocomplete="senha" placeholder="************" required>
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <button type="button" class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Mostrar ou esconder a senha">
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </button>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="manter_conectado" name="manter_conectado" type="checkbox" class="form-checkbox">
                        <label for="manter_conectado" class="form-label ml-3">Manter conectado</label>
                    </div>
                    <div class="text-sm">
                        <a href="<?= obterURL('/esqueci-senha') ?>" class="form-link">Esqueceu sua senha?</a>
                    </div>
                </fieldset>

                <input type="hidden" name="token_csrf" value="<?= htmlspecialchars($token_csrf) ?>">
                <button type="submit" class="button-primary w-full">
                    Entrar
                </button>
            </form>
        </div>
    </section>
</main>
<script src="<?= obterURL('/assets/js/notificador-flash.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/js/formulario.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        new Formulario({
            formId: 'formulario-login',
            notificador: true,
            onSuccess: function(response) {
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500)
            }
        });
    });

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
</script>
</body>

</html>