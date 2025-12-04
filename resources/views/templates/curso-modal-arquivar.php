<template id="template-curso-modal-arquivar">
    <div id="curso-modal-arquivar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <span class="material-icons-sharp text-red-600">archive</span>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <div class="mt-3 mb-8 text-center" id="info-arquivar">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Arquivar curso</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Tem certeza que deseja arquivar o curso <span class="font-semibold" id="curso-nome-arquivar">{{nome}}</span>? Esta ação não pode ser desfeita e o curso ficará apenas para consulta.</p>
                </div>
                <form id="curso-form-arquivar" method="POST" action="/cursos/arquivar">
                    <input type="hidden" name="id" class="curso-id-input">
                    <div class="mt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-danger">Sim, arquivar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>