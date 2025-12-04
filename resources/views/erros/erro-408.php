<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro | GAIO</title>
    <link href="<?= obterURL('/assets/css/tailwindcss-output.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= obterURL('/assets/css/style.css') ?>">
    <link rel="icon" href="<?= obterURL('/assets/img/gaio-icone-azul.ico') ?>" type="image/x-icon">
</head>
<body class="h-full">
    <main class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
        <div class="text-center">
            <img src="<?= obterURL('/assets/img/gaio-icone-azul.png') ?>" alt="GAIO" class="mx-auto h-16 w-auto my-4">
            <p class="text-base font-semibold text-sky-600">408</p>
            <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Tempo de requisição esgotado</h1>
            <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">O servidor demorou demais para receber sua solicitação. Tente novamente.</p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="<?= obterURL('/') ?>" class="rounded-md bg-sky-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-sky-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">Voltar</a>
                <a href="mailto:thiago.moreira@sistemagaio.com.br" class="text-sm font-semibold text-gray-900">Contatar suporte <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </main>
</body>
</html>