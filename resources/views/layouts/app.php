<?php

use App\Models\Usuario;
use App\Services\AutenticacaoService;
use App\Services\NotificacaoService;

$usuarioAutenticado = AutenticacaoService::usuarioAutenticado();
$nomeReduzidoUsuario = $usuarioAutenticado->obterNomeReduzido();
$emailUsuario = $usuarioAutenticado->obterEmailInstitucional() ?? $usuarioAutenticado->obterEmailPessoal();
$caminhoFotoUsuario = $usuarioAutenticado->obterCaminhoFoto();
$urlFotoExibicao = empty($caminhoFotoUsuario) ? obterURL('/assets/img/usuario-icone.png') : obterURL('/' . $_ENV['SISTEMA_IMAGENS_PERFIL'] . $caminhoFotoUsuario);

$totalNotificacoesNaoLidas = NotificacaoService::contarNaoLidas($usuarioAutenticado);

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
    <script src="<?= obterURL('/assets/javascript/utils/main.js'); ?>"></script>
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
        <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
</body>
</html>