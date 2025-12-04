<template id="template-componente-modal-prerequisitos">
    <div id="componente-modal-prerequisitos" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-purple-100">
                    <span class="material-icons-sharp text-purple-600">link</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Definir pré-requisitos</h3>
                    <p class="mt-2 text-sm text-gray-600">Selecione os componentes que são pré-requisitos para <strong id="componente-nome-prerequisitos"></strong>.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="componente-form-prerequisitos" action="/matrizes-curriculares/componentes/1/prerequisitos" method="POST" class="space-y-4">
                    <input type="hidden" name="id" value="">
                    <div>
                        <label class="form-label">Pré-requisitos</label>
                        <div id="lista-prerequisitos" class="mt-2 space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            <!-- Será preenchido dinamicamente -->
                        </div>
                    </div>
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary">Salvar Pré-requisitos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
