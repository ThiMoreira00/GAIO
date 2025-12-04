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

            <div class="mt-10">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm font-medium">
                        <span class="bg-white px-6 text-gray-500">Ou continue com</span>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <a href="#" class="flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-100">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                            <path fill="#EA4335" d="M12.0003 4.75C13.7703 4.75 15.3553 5.36 16.6053 6.55L20.0303 3.125C17.9503 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.28027 6.61L5.27028 9.705C6.21525 6.86 8.87028 4.75 12.0003 4.75Z" />
                            <path fill="#4285F4" d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L19.945 21.1C22.2 19.01 23.49 15.92 23.49 12.275Z" />
                            <path fill="#FBBC05" d="M5.265 14.295C5.025 13.57 4.885 12.8 4.885 12C4.885 11.2 5.02 10.43 5.265 9.705L1.275 6.61C0.46 8.23 0 10.06 0 12C0 13.94 0.46 15.77 1.28 17.39L5.265 14.295Z" />
                            <path fill="#34A853" d="M12.0004 24C15.2404 24 17.9654 22.935 19.9454 21.095L16.0804 18.095C15.0054 18.82 13.6204 19.245 12.0004 19.245C8.8704 19.245 6.21537 17.135 5.2654 14.29L1.27539 17.385C3.25539 21.31 7.3104 24 12.0004 24Z" />
                        </svg>
                        <span class="text-sm font-semibold">Google</span>
                    </a>
                </div>
            </div>
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