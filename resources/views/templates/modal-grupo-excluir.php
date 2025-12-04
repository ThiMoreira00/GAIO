<template id="template-modal-grupo-excluir">
    <div id="modal-excluir-grupo" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <span class="material-icons-sharp text-red-600">group_off</span>
                </div>
                <button type="button" class="modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Excluir grupo</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Tem certeza que deseja excluir o grupo "<span id="grupo-excluir-nome" class="font-medium"></span>"?
                        <br>
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
                <form id="form-excluir-grupo" method="POST" class="space-y-4">
                    <input type="hidden" name="grupo_id" id="grupo-excluir-id">
                    <div class="mt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" id="btn-cancelar-exclusao" class="button-secondary modal-fechar">
                            Cancelar
                        </button>
                        <button type="submit" class="button-danger">
                            Sim, excluir grupo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>