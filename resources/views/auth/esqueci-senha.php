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
    <title>Esqueci minha senha | GAIO</title>
</head>

<body class="flex flex-col justify-center items-center h-dvh" style="background-image: linear-gradient(134deg, #acd7ff, #0f3f67); background-size: contain;">
<header class="sm:mx-auto sm:w-full sm:max-w-sm mb-8">
    <img src="<?= obterURL('/assets/img/gaio-logo-colorida.png'); ?>" alt="Logotipo do Sistema GAIO" class="mx-auto mt-4" width="250px" height="auto">
</header>

<main class="w-100 md:w-2/3 lg:w-1/2 xl:w-lg bg-white shadow-md rounded mx-auto">
    <section class="flex min-h-full flex-col justify-center px-6 py-4 lg:px-8">

        <div class="my-8 sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="pb-8 text-center text-2xl/9 font-semibold text-gray-900">Esqueci minha senha</h2>

            <form id="formulario-esqueci-senha" class="space-y-6" action="/esqueci-senha" method="POST" novalidate>
                <fieldset>
                    <label for="email" class="form-label mb-2 required">
                        E-mail (pessoal / institucional)
                    </label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="material-icons-sharp h-5 w-5 text-gray-400">alternate_email</span>
                        </div>
                        <input type="email" name="email" id="email" class="form-input pl-10" autocomplete="email" placeholder="email@email.com" required>
                    </div>
                </fieldset>

                <input type="hidden" name="token_csrf" value="<?= htmlspecialchars($token_csrf) ?>">
                <button type="submit" class="button-primary w-full">
                    Enviar link de recuperação
                </button>
            </form>

            <div class="text-center my-4">
                <a href="/login" class="form-link underline  text-sm">Voltar para login</a>
            </div>
        </div>
    </section>
</main>
<script src="<?= obterURL('/assets/js/notificador-flash.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/js/formulario.js') ?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        new Formulario({
            formId: 'formulario-esqueci-senha',
            notificador: true
        });
    });
</script>
</body>

</html>