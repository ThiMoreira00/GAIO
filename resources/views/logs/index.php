<header class="flex flex-col sm:flex-row items-start sm:items-center justify-between py-4 gap-4">
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl">Logs</h1>
</header>

<main id="main-logs">
    <section class="bg-white p-8 border-b border-gray-200 min-h-1/2" aria-labelledby="logs-section-heading">
        <h2 id="logs-section-heading" class="sr-only">Logs registrados</h2>
        <div class="relative sm:rounded-lg">
            <?= flash()->exibir(); ?>
            <div class="bg-white rounded-lg p-6 relative">
                <section class="mb-8">
                    <form id="form-filtros-logs" action="/logs/filtrar" method="GET">
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <div class="relative w-full sm:flex-grow">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">search</span>
                                </div>
                                <input type="search" name="busca" id="busca-logs" class="block w-full rounded-lg border-0 bg-gray-100 py-2.5 pl-10 pr-4 text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm" placeholder="Buscar logs...">
                            </div>
                        </div>
                    </form>
                </section>

                <div id="container-logs" class="space-y-4">
                </div>

                <div id="loader-logs" class="text-center mt-8 py-4" style="display: none;">
                    <span class="material-icons-sharp text-5xl text-gray-400 animate-spin">sync</span>
                </div>
            </div>
        </div>
    </section>
</main>