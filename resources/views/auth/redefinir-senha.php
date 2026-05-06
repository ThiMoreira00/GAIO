<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include_once __DIR__ . '/../layouts/_head.php'; ?>
    <title>Redefinir senha | GAIO</title>
</head>

<body class="flex flex-col justify-center items-center h-dvh" style="background-image: linear-gradient(134deg, #acd7ff, #0f3f67); background-size: contain;">
<header class="sm:mx-auto sm:w-full sm:max-w-sm mb-8">
    <img src="<?= obterURL('/assets/img/gaio-logo-colorida.png'); ?>" alt="Logotipo do Sistema GAIO" class="mx-auto mt-4" width="250px" height="auto">
</header>

<main class="w-100 md:w-2/3 lg:w-1/2 xl:w-lg bg-white shadow-md rounded mx-auto">
    <section class="flex min-h-full flex-col justify-center px-6 py-4 lg:px-8">

        <div class="my-8 sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="text-center text-2xl/9 font-semibold text-gray-900">Redefinir senha</h2>
            <div class="bg-gray-50 p-4 my-4 rounded-lg h-fit">
                <h3 class="text-md font-semibold text-gray-800">Requisitos de senha</h3>
                <p class="text-sm text-gray-600 mt-2 mb-4">Certifique-se de que estes requisitos sejam atendidos:</p>

                <ul class="space-y-3">
                    <li id="req-length" class="flex items-center text-sm text-gray-500">
                        <svg class="h-5 w-5 mr-2 flex-shrink-0 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0L10 14.17l6.88-6.88c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-7.59 7.59c-.38.39-1.02.39-1.41 0z"/></svg>
                        <span class="transition-colors duration-300">Pelo menos 8 caracteres (e até 32)</span>
                    </li>
                    <li id="req-case" class="flex items-center text-sm text-gray-500">
                        <svg class="h-5 w-5 mr-2 flex-shrink-0 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0L10 14.17l6.88-6.88c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-7.59 7.59c-.38.39-1.02.39-1.41 0z"/></svg>
                        <span class="transition-colors duration-300">Pelo menos uma letra minúscula e uma maiúscula</span>
                    </li>
                    <li id="req-special" class="flex items-center text-sm text-gray-500">
                        <svg class="h-5 w-5 mr-2 flex-shrink-0 transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM9.29 16.29 5.7 12.7c-.39-.39-.39-1.02 0-1.41.39-.39 1.02-.39 1.41 0L10 14.17l6.88-6.88c.39-.39 1.02-.39 1.41 0 .39.39.39 1.02 0 1.41l-7.59 7.59c-.38.39-1.02.39-1.41 0z"/></svg>
                        <span class="transition-colors duration-300">Inclusão de pelo menos um caractere especial (!@#?)</span>
                    </li>
                </ul>
            </div>

            <form id="formulario-redefinir-senha" class="space-y-6" action="/redefinir-senha" method="POST" novalidate>
                <fieldset>
                    <label for="nova_senha" class="form-label mb-2 required">
                        Nova senha
                    </label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-icons-sharp h-5 w-5 text-gray-400">password</span>
                        </div>
                        <input type="password" name="nova_senha" id="nova_senha" class="form-input pl-10" autocomplete="nova_senha" placeholder="************" required>
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <button type="button" class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Mostrar ou esconder a senha">
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </button>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <label for="confirmar_nova_senha" class="form-label mb-2 required">
                        Confirme nova senha
                    </label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-icons-sharp h-5 w-5 text-gray-400">password</span>
                        </div>
                        <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha" class="form-input pl-10" autocomplete="confirmar_nova_senha" placeholder="************" required>
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <button type="button" class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Mostrar ou esconder a senha">
                                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            </button>
                        </div>
                    </div>
                    <p id="password-match-msg" class="text-sm mt-2"></p>
                </fieldset>

                <input type="hidden" name="token_redefinicao" value="<?= htmlspecialchars($token_redefinicao) ?>">
                <input type="hidden" name="token_csrf" value="<?= htmlspecialchars($token_csrf) ?>">
                <button type="submit" class="button-primary w-full">
                    Redefinir senha
                </button>
            </form>

            <div class="text-center my-4">
                <a href="/login" class="form-link underline  text-sm">Voltar para login</a>
            </div>
        </div>
    </section>
</main>
<script src="<?= obterURL('/assets/javascript/utils/notificador.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>"></script>
<script src="<?= obterURL('/assets/javascript/auth/redefinir-senha.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        new Formulario({
            formId: 'formulario-redefinir-senha',
            notificador: {
                status: true,
                alvo: '#formulario-redefinir-senha',
            },
            onSuccess: function(response) {
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000)
            }
        });
    });
</script>
</body>

</html>