<template id="template-turma-modal-finalizar">
    <div id="turma-modal-finalizar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">done_all</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Finalizar turma</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Tem certeza que deseja finalizar a turma <strong id="turma-codigo-finalizar"></strong>?</p>
                    <p class="mt-2 text-sm text-gray-500">Após a finalização, a turma será marcada como concluída.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="turma-form-finalizar" action="/turmas/{id}/finalizar" method="POST" class="space-y-4">
                    <input type="hidden" name="id" value="">
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-semibold">Finalizar turma</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
