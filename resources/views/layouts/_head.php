<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?= obterURL('/assets/css/tailwindcss-output.css'); ?>" rel="stylesheet">
<link rel="icon" href="<?= obterURL('/assets/img/gaio-icone-azul.ico'); ?>" type="image/x-icon">
<link rel="stylesheet" href="<?= obterURL('/assets/css/pace-flash.css'); ?>">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/lib/marked.umd.js"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
<style>
    /* fallback */
    @font-face {
        font-family: 'Material Icons Sharp';
        font-style: normal;
        font-weight: 400;
        src: url(<?= obterURL('/assets/fonts/google-material-icons-sharp.woff2') ?>) format('woff2');
    }

    .material-icons-sharp {
        font-family: 'Material Icons Sharp', serif;
        font-weight: normal;
        font-style: normal;
        font-size: 20px;
        line-height: 1;
        letter-spacing: normal;
        text-transform: none;
        display: inline-block;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        -webkit-font-feature-settings: 'liga';
        -webkit-font-smoothing: antialiased;
    }
</style>