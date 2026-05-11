<?php

use App\Models\Usuario;
use App\Services\AuthService;
use App\Services\NotificacaoService;

$obterUsuarioAutenticado = AuthService::obterUsuarioAutenticado();

if ($obterUsuarioAutenticado) {
    

    $nomeReduzidoUsuario = $obterUsuarioAutenticado->obterNomeReduzido();
    $emailUsuario = $obterUsuarioAutenticado->obterEmailInstitucional() ?? $obterUsuarioAutenticado->obterEmailPessoal();
    $caminhoFotoUsuario = $obterUsuarioAutenticado->obterCaminhoFoto();
    $urlFotoExibicao = empty($caminhoFotoUsuario) ? obterURL('/assets/img/usuario-padrao.png') : obterURL('/' . $_ENV['SISTEMA_IMAGENS_PERFIL'] . $caminhoFotoUsuario);

    $totalNotificacoesNaoLidas = NotificacaoService::contarNaoLidas($obterUsuarioAutenticado);
} else {
    $nomeReduzidoUsuario = 'Usuário';
    $emailUsuario = '';
    $urlFotoExibicao = obterURL('/assets/img/usuario-padrao.png');
    $totalNotificacoesNaoLidas = 0;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?= $titulo ?? 'Painel' ?> | GAIO</title>
    <?php require_once __DIR__ . '/_head.php'; ?>
</head>

<body class="bg-slate-100 text-slate-800">
    <div class="relative min-h-screen lg:flex">
        <div class="flex-1 flex flex-col">
            <?php require_once __DIR__ . '/_menu.php'; ?>
            <?php require_once __DIR__ . '/_header.php'; ?>
            <main id="main-content" class="main-content">
                <?php
                    require_once __DIR__ . '/_breadcrumbs.php';

                    if (isset($breadcrumbs) && !empty($breadcrumbs)) {
                        renderizarBreadcrumbs($breadcrumbs);
                    }
                ?>
                <noscript>É necessário JavaScript para carregar a página.</noscript>
                <?= $conteudo ?>
            </main>
        </div>
        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/75 hidden lg:hidden transition-opacity duration-300 ease-in-out print:hidden" aria-hidden="true"></div>
    </div>
    <?php /* require_once __DIR__ . '/_footer.php'; */ ?>
    <script src="<?= obterURL('/assets/javascript/utils/app.js'); ?>"></script>
    <script src="https://static.getwcag.com/widget.js" config-url="https://static.getwcag.com/configs/sEjVpAmS4uJKO3xTRmRM/config.json" use-important="false"></script>
</body>
</html>