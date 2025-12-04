<template id="template-matriz-modal-inativar">
    <div id="matriz-modal-inativar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <span class="material-icons-sharp text-red-600">block</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Inativar matriz curricular</h3>
                    <p class="mt-2 text-sm text-gray-600">Tem certeza que deseja inativar a matriz curricular <strong id="matriz-nome-inativar"></strong>?</p>
                    <p class="mt-2 text-sm text-red-600 font-medium">Esta ação não poderá ser desfeita.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="matriz-form-inativar" action="/matrizes-curriculares/1/inativar" method="POST">
                    <input type="hidden" name="id" value="">
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-danger">Inativar Matriz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
