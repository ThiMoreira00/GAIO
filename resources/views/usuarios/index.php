<header>
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl mb-4">Usuários</h1>
</header>

<main>
    <section class="bg-white p-8 border-b border-gray-200 min-h-max" aria-labelledby="usuarios-section-heading">
        <h2 id="usuarios-section-heading" class="sr-only">Gestão de Usuários</h2>
        <div class="relative overflow-x-auto sm:rounded-lg">
            <div class="p-4 flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <label for="seletor-registros" class="text-sm text-gray-600">Exibir registros por página:</label>
                        <select id="seletor-registros" class="border-gray-200 rounded-lg text-sm focus:border-sky-500 focus:ring-sky-500" aria-label="Número de registros a exibir por página">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="relative">
                        <button id="btn-abrir-filtros" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" aria-haspopup="dialog" aria-expanded="false" aria-controls="modal-filtros">
                            <svg class="mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.59L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                            </svg>
                            Filtros
                            <span id="contador-filtros" class="hidden ml-2 items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full" aria-live="polite"></span>
                        </button>

                        <div id="modal-filtros" class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-gray-400/70 p-4" role="dialog" aria-modal="true" aria-labelledby="modal-filtros-titulo">
                            <h3 id="modal-filtros-titulo" class="sr-only">Opções de Filtro</h3>
                            <div id="modal-filtros-corpo" class="max-h-80 overflow-y-auto mb-4">
                            </div>
                            <div class="flex justify-end space-x-3 mt-4 border-t border-gray-200 pt-4">
                                <button id="btn-limpar-filtros" class="px-3 py-1 border border-gray-300 rounded-md text-gray-700 text-sm hover:bg-gray-100">Limpar Filtros</button>
                                <button id="btn-aplicar-filtros" class="px-3 py-1 bg-sky-600 text-white rounded-md text-sm hover:bg-sky-700">Aplicar Filtros</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative max-w-xs w-full">
                    <label class="sr-only" for="campo-pesquisa-geral">Pesquisar em tudo</label>
                    <input type="search" id="campo-pesquisa-geral" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg sm:text-sm" placeholder="Pesquisar em tudo..." aria-label="Campo de pesquisa geral" />
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3" aria-hidden="true">
                        <svg class="size-4 text-gray-400" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M11 19A8 8 0 1 0 11 3a8 8 0 0 0 0 16"/></svg>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="tabela-usuarios" aria-describedby="usuarios-table-description">
                    <caption id="usuarios-table-description" class="sr-only">Tabela de gerenciamento de usuários com opções de filtro e pesquisa.</caption>
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3 px-4 pe-0">
                            <input id="checkbox-selecionar-todos" type="checkbox" aria-label="Selecionar todos os usuários visíveis">
                        </th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase w-200">Nome</th>
                        <th scope="col" class="hidden sm:table-cell px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= ($usuario->obterNomeSocial()) ? $usuario->obterNomeSocial() . " (" . $usuario->obterNomeCivil() . ")" : $usuario->obterNomeCivil() ?></td>
                            <td>
                                <?php switch ($usuario->status) {
                                    case '1':
                                        echo 'Ativo';
                                        break;
                                    case '0':
                                        echo 'Inativo';
                                        break;
                                    default:
                                        echo 'Desconhecido';
                                }
                                ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3">
                            <div class="flex items-center justify-between" aria-live="polite" aria-atomic="true">
                                <div class="text-sm text-gray-700">Mostrando <span class="font-semibold" id="inicio-intervalo">1</span> até <span class="font-semibold" id="final-intervalo">10</span> de <span class="font-semibold" id="total-registros">0</span> registros</div>
                                <nav aria-label="Navegação da Tabela de Usuários">
                                    <ul class="flex items-center space-x-2">
                                        <li>
                                            <button id="btn-pagina-anterior" class="px-3 py-1 border rounded-md disabled:opacity-50" aria-label="Página anterior">&lt;</button>
                                        </li>
                                        <li>
                                            <span class="text-sm">Página <span class="font-semibold" id="pagina-atual">1</span> de <span class="font-semibold" id="total-paginas">1</span></span>
                                        </li>
                                        <li>
                                            <button id="btn-pagina-proxima" class="px-3 py-1 border rounded-md disabled:opacity-50" aria-label="Próxima página">&gt;</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</main>

<script src="<?= obterURL('/assets/js/tabela-administrativa.js'); ?>"></script>