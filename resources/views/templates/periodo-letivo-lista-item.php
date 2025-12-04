<template id="template-lista-item-periodo-letivo">
    <div class="periodo-letivo-item flex items-start gap-4 p-4 rounded-lg border border-gray-200">
        <div class="card-icon-container w-10 h-10 rounded-full flex-shrink-0 flex bg-sky-600 items-center justify-center text-white">
            <span class="material-icons-sharp !text-xl card-icon-name">date_range</span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row justify-between items-start">
                <a href="#" class="periodo-letivo-sigla text-base font-bold text-gray-800 hover:text-sky-600 hover:underline break-words">{{sigla}}</a>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200/60 grid grid-cols-1 sm:grid-cols-4 gap-x-4 gap-y-4 text-sm">
                <div>
                    <p class="text-gray-500 mb-1">Data de início</p>
                    <p class="periodo-letivo-data-inicio font-medium text-gray-800">{{data_inicio}}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Data de término</p>
                    <p class="periodo-letivo-data-termino font-medium text-gray-800">{{data_termino}}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Status</p>
                    <span class="periodo-letivo-status text-xs font-bold inline-flex items-center px-3 py-1 rounded-full uppercase">{{status}}</span>
                </div>
            </div>
        </div>
        <div class="relative">
            <button type="button" class="inline-flex items-center p-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 rounded-md periodo-letivo-dropdown-trigger" aria-label="Menu de ações">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>

            <!-- Menu de Ações -->
            <div class="hidden absolute right-0 top-full z-[9999] mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-gray-400/70 py-1 periodo-letivo-dropdown-menu">
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors" data-action="visualizar">
                    <span class="material-icons-sharp !text-lg">visibility</span> Visualizar
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors" data-action="editar">
                    <span class="material-icons-sharp !text-lg">edit</span> Editar
                </a>
                <div class="border-t border-gray-100 my-1 border-acoes"></div>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors" data-action="arquivar">
                    <span class="material-icons-sharp !text-lg">archive</span> Arquivar
                </a>
            </div>
        </div>
    </div>
</template>