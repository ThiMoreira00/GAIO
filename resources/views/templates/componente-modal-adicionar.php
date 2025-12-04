<template id="template-componente-modal-adicionar">
    <div id="componente-modal-adicionar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">book</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Adicionar componente curricular</h3>
                    <p class="mt-2 text-sm text-gray-600">Preencha as informações do novo componente curricular.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="componente-form-adicionar" action="/matrizes-curriculares/componentes/adicionar" method="POST" class="space-y-4">
                    <input type="hidden" name="matriz_curricular_id" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="componente-nome" class="form-label required">Nome do Componente</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">badge</span>
                                </div>
                                <input type="text" name="nome" id="componente-nome" required class="form-input pl-10" placeholder="Ex.: Estruturas de Dados">
                            </div>
                        </div>
                        <div>
                            <label for="componente-tipo" class="form-label required">Tipo</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">category</span>
                                </div>
                                <select name="tipo" id="componente-tipo" required class="form-select pl-10">
                                    <option value="" disabled selected>Selecione o tipo</option>
                                    <option value="Obrigatória">Obrigatória</option>
                                    <option value="Optativa">Optativa</option>
                                    <option value="Eletiva">Eletiva</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="componente-periodo" class="form-label required">Período</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">calendar_today</span>
                                </div>
                                <input type="number" name="periodo" id="componente-periodo" min="1" required class="form-input pl-10" placeholder="Ex.: 3">
                            </div>
                        </div>
                        <div>
                            <label for="componente-creditos" class="form-label required">Créditos</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">star</span>
                                </div>
                                <input type="number" name="creditos" id="componente-creditos" min="1" required class="form-input pl-10" placeholder="Ex.: 4">
                            </div>
                        </div>
                        <div>
                            <label for="componente-carga-horaria" class="form-label required">Carga Horária (horas)</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">schedule</span>
                                </div>
                                <input type="number" name="carga_horaria" id="componente-carga-horaria" min="1" required class="form-input pl-10" placeholder="Ex.: 60">
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary">Salvar Componente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
