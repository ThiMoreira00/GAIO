<template id="template-matriz-modal-validar">
    <div id="matriz-modal-validar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                    <span class="material-icons-sharp text-green-600">check_circle</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Validar matriz curricular</h3>
                    <p class="mt-2 text-sm text-gray-600">Deseja validar a matriz curricular <strong id="matriz-nome-validar"></strong>?</p>
                    <p class="mt-2 text-sm text-yellow-600 font-medium">Após validada, a matriz não poderá mais ser editada.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="matriz-form-validar" action="/matrizes-curriculares/1/validar" method="POST">
                    <input type="hidden" name="id" value="">
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">Validar Matriz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
