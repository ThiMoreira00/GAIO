<template id="template-membro-grupo">
    <div class="item-membro-grupo flex items-center bg-gray-50 rounded-md px-3 py-2 mb-2">
        <img class="w-8 h-8 rounded-full mr-3 membro-avatar" src="" alt="">
        <p>
            <span class="text-base font-medium text-gray-900 membro-nome block"></span>
            <span class="text-sm text-gray-600 membro-email"></span>
        </p>
        <button type="button" class="ml-auto text-gray-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 p-1 rounded-full btn-remover-membro">
            <span class="sr-only">Remover usuário</span>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>