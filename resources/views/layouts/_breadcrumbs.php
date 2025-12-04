<?php

/**
 * Renderiza um componente breadcrumb.
 *
 * @param array $breadcrumbs Um array de arrays, onde cada sub-array representa um item do breadcrumb
 * e deve conter 'label' (texto exibido) e 'url' (URL do link).
 * O último item do array será tratado como a página atual (sem link).
 */
function renderizarBreadcrumbs(array $breadcrumbs): void
{
    if (empty($breadcrumbs)) {
        return; // Não renderiza nada se não houver breadcrumbs
    }
    ?>
    <nav class="flex mb-6" aria-label="Navegação de migalhas de pão">
        <ol class="flex items-center space-x-2">

            <li>
                <a href="/" class="text-gray-400 hover:text-gray-500 flex align-center" aria-label="Ir para a página inicial">
                    <span class="material-icons-sharp shrink-0">home</span>
                    <span class="sr-only">Início</span>
                </a>
            </li>
            <?php 
            $totalBreadcrumbs = count($breadcrumbs);
            foreach ($breadcrumbs as $index => $link): ?>
                <li>
                    <div class="flex items-center">
                        <span class="material-icons-sharp shrink-0 text-gray-400">chevron_right</span>
                        <?php if ($index === $totalBreadcrumbs - 1) : ?>
                            <span class="breadcrumbs-item" aria-current="page"><?= sanitizar($link['label']) ?></span>
                        <?php else : ?>
                            <a href="<?= sanitizar($link['url']) ?>" class="breadcrumbs-item"><?= sanitizar($link['label']) ?></a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php } ?> 