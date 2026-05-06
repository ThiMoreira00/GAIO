<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <?php include_once '../../layouts/_head.php'; ?>
    <title>Alterar senha | GAIO</title>
</head>

<body class="flex flex-col justify-center items-center h-dvh"
    style="background-image: linear-gradient(135deg, #e0f0ff, #1a6aad); background-size: contain;">
    <header class="sm:mx-auto sm:w-full sm:max-w-sm mb-8">
        <img src="<?= obterURL('/assets/img/gaio-logo-colorida.png'); ?>" alt="Logotipo do Sistema GAIO"
            class="mx-auto mt-4" width="250px" height="auto">
    </header>

    <main class="w-100 md:w-2/3 lg:w-1/2 xl:w-lg bg-white shadow-md rounded mx-auto">
        <section class="flex min-h-full flex-col justify-center px-8 py-4 lg:px-8">

            <div class="my-8 sm:mx-auto sm:w-full sm:max-w-sm">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 mb-4">
                    <span class="material-icons-sharp text-amber-600">lock_reset</span>
                </div>

                <h2 class="pb-2 text-center text-2xl/9 font-semibold text-gray-900">Alterar senha</h2>
                <p class="text-center text-sm text-gray-500 mb-6">Você está utilizando a senha padrão. Por segurança,
                    crie uma nova senha para continuar.</p>

                <?= flash()->exibir() ?>

                <div id="mensagem-container"></div>

                <form id="formulario-alterar-senha" class="space-y-6" action="/alterar-senha" method="POST">

                    <fieldset>
                        <label for="nova_senha" class="form-label mb-2 required">
                            Nova senha
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="material-icons-sharp h-5 w-5 text-gray-400">lock</span>
                            </div>
                            <input type="password" name="nova_senha" id="nova_senha" class="form-input pl-10"
                                autocomplete="new-password" placeholder="Mínimo de 8 caracteres" required minlength="8"
                                maxlength="32">
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <button type="button"
                                    class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500"
                                    aria-label="Mostrar ou esconder a senha">
                                    <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="currentColor">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path
                                            d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Deve conter pelo menos 8 caracteres e um caractere
                            especial.</p>
                    </fieldset>

                    <fieldset>
                        <label for="confirmar_nova_senha" class="form-label mb-2 required">
                            Confirmar nova senha
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="material-icons-sharp h-5 w-5 text-gray-400">lock</span>
                            </div>
                            <input type="password" name="confirmar_nova_senha" id="confirmar_nova_senha"
                                class="form-input pl-10" autocomplete="new-password" placeholder="Repita a nova senha"
                                required minlength="8" maxlength="32">
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <button type="button"
                                    class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500"
                                    aria-label="Mostrar ou esconder a senha">
                                    <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px"
                                        viewBox="0 0 24 24" width="24px" fill="currentColor">
                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                        <path
                                            d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </fieldset>

                    <input type="hidden" name="token_csrf" value="<?= htmlspecialchars($token_csrf) ?>">
                    <input type="hidden" name="token_alteracao" value="<?= htmlspecialchars($token_alteracao) ?>">
                    <button type="submit" class="button-primary w-full" id="btn-alterar-senha">
                        Alterar senha
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="<?= obterURL('/login') ?>" class="form-link text-sm">
                        <span class="material-icons-sharp text-sm align-middle mr-1">arrow_back</span>
                        Voltar para o login
                    </a>
                </div>
            </div>
        </section>
    </main>
    <script>
        // Toggle de visibilidade de senha
        document.querySelectorAll('.js-password-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const input = button.parentElement.previousElementSibling;
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });

        // Submissão via AJAX
        document.getElementById('formulario-alterar-senha').addEventListener('submit', async function (e) {
            e.preventDefault();

            const form = this;
            const btn = document.getElementById('btn-alterar-senha');
            const container = document.getElementById('mensagem-container');

            btn.disabled = true;
            btn.textContent = 'Alterando...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(formData)
                });

                const data = await response.json();

                if (data.status === 'sucesso') {
                    container.innerHTML = `
                    <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm font-medium flex items-center gap-2">
                        <span class="material-icons-sharp !text-lg">check_circle</span>
                        ${data.mensagem}
                    </div>`;
                    form.style.display = 'none';
                    setTimeout(() => { window.location.href = '/login'; }, 2000);
                } else {
                    container.innerHTML = `
                    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm font-medium flex items-center gap-2">
                        <span class="material-icons-sharp !text-lg">error</span>
                        ${data.mensagem}
                    </div>`;
                }
            } catch (err) {
                container.innerHTML = `
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm font-medium flex items-center gap-2">
                    <span class="material-icons-sharp !text-lg">error</span>
                    Erro ao alterar a senha. Tente novamente.
                </div>`;
            } finally {
                btn.disabled = false;
                btn.textContent = 'Alterar senha';
            }
        });
    </script>
</body>

</html>