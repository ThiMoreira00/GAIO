<header class="bg-white text-gray-800 flex items-center justify-between sticky top-0 z-30 shadow-sm px-4 h-16 print:hidden">
    <div class="flex items-center">
        <button id="mobile-menu-button" aria-label="Abrir menu" class="p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500 rounded lg:hidden">
            <span class="material-icons-sharp">menu</span>
        </button>
        <a href="<?= obterURL('/'); ?>" class="block lg:hidden ml-2">
            <img src="<?= obterURL('/assets/img/gaio-icone-azul.png'); ?>" alt="Logotipo Sistema GAIO" class="h-8 w-auto">
        </a>
    </div>

    <div class="flex items-center space-x-4">

        <button type="button" class="relative rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
            <a href="<?= obterURL('/notificacoes'); ?>" class="relative">
                <span class="material-icons-sharp">notifications</span>

                <?php if ($totalNotificacoesNaoLidas > 0): ?>
                    <span class="absolute top-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs font-bold text-red-100 transform translate-x-1/2 -translate-y-1/2" data-total-notificacoes="<?= $totalNotificacoesNaoLidas ?>" id="total-notificacoes-nao-lidas"><?= ($totalNotificacoesNaoLidas > 9) ? '9+' : $totalNotificacoesNaoLidas; ?></span>
                <?php endif; ?>
            </a>
        </button>

        <div class="relative">
            <button id="user-menu-button" class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                <img src="<?= $urlFotoExibicao ?>" alt="Foto de <?= $nomeReduzidoUsuario ?>" class="h-8 w-8 rounded-full object-cover">
                <span class="text-sm font-semibold text-gray-900 hidden lg:block">
                    <?= $nomeReduzidoUsuario ?>
                </span>
                <span class="material-icons-sharp text-gray-600 hidden lg:block">expand_more</span>
            </button>

            <div id="user-menu-dropdown" class="hidden opacity-0 scale-95 absolute right-0 mt-2 w-60 origin-top-right bg-white rounded-md shadow-lg ring ring-black/30 z-40 transition-all duration-100 ease-out">
                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-semibold text-gray-800 truncate" role="none"><?= $nomeReduzidoUsuario ?></p>
                        <p class="text-xs text-gray-500 truncate" role="none"><?= $emailUsuario ?></p>
                    </div>
                    <a href="<?= obterURL('/configuracoes') ?>" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                        <span class="material-icons-sharp text-gray-500">settings</span>
                        Configurações
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <a href="<?= obterURL('/sair') ?>" class="flex items-center gap-3 w-full text-start px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700" role="menuitem">
                        <span class="material-icons-sharp">logout</span>
                        Sair
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>