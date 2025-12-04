<template id="template-matriz-modal-editar">
    <div id="matriz-modal-editar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">edit</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Editar matriz curricular</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Atualize as informações da matriz curricular.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="matriz-form-editar" action="/matrizes-curriculares/1/editar" method="POST" class="space-y-4">
                    <input type="hidden" name="id" value="">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="form-label">Curso</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">school</span>
                                </div>
                                <input type="text" id="matriz-curso-nome-editar" disabled class="form-input pl-10 bg-gray-50" value="">
                            </div>
                        </div>
                        <div>
                            <label for="matriz-quantidade-periodos-editar" class="form-label required">Quantidade de Períodos</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">calendar_today</span>
                                </div>
                                <input type="number" name="quantidade_periodos" id="matriz-quantidade-periodos-editar" min="1" max="20" required class="form-input pl-10">
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
