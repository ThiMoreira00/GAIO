<template id="template-lista-item-grupo">
    <a href="#" class="item-grupo flex items-center px-3 py-2 rounded-md hover:bg-gray-100 text-gray-600 hover:text-gray-900 data-[active=true]:bg-sky-100 data-[active=true]:text-sky-800 data-[active=true]:font-semibold group" aria-current="false" data-active="false">
        <span class="dot w-2.5 h-2.5 rounded-full bg-gray-400 mr-3 group-data-[active=true]:bg-sky-500 overflow-hidden"></span>
        <span class="grupo-nome"></span>
        <button type="button" class="btn-excluir ml-auto text-gray-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 p-1 rounded-full hidden">
            <span class="sr-only">Excluir grupo</span>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </a>
</template>