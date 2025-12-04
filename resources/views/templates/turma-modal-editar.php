<template id="template-turma-modal-editar">
    <div id="turma-modal-editar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">edit</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Editar turma</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Atualize as informações da turma.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="turma-form-editar" action="/turmas/{id}/editar" method="POST" class="space-y-4">
                    <input type="hidden" name="id" value="">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="turma-codigo-editar" class="form-label required">Código da turma</label>
                            <input type="text" name="codigo" id="turma-codigo-editar" required class="form-input" placeholder="Ex.: TUR-2025-001">
                        </div>
                        <div>
                            <label for="turma-professor-editar" class="form-label required">Professor</label>
                            <select name="professor_id" id="turma-professor-editar" required class="form-select">
                                <option value="" disabled selected>Selecione o professor</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="turma-turno-editar" class="form-label required">Turno</label>
                                <select name="turno" id="turma-turno-editar" required class="form-select">
                                    <option value="MANHA">Manhã</option>
                                    <option value="TARDE">Tarde</option>
                                    <option value="NOITE">Noite</option>
                                    <option value="INTEGRAL">Integral</option>
                                </select>
                            </div>
                            <div>
                                <label for="turma-capacidade-editar" class="form-label required">Capacidade Máxima</label>
                                <input type="number" name="capacidade_maxima" id="turma-capacidade-editar" min="1" max="200" class="form-input" required>
                            </div>
                        </div>
                        <div>
                            <label for="turma-modalidade-editar" class="form-label required">Modalidade</label>
                            <select name="modalidade" id="turma-modalidade-editar" required class="form-select">
                                <option value="PRESENCIAL">Presencial</option>
                                <option value="REMOTA">Remota</option>
                                <option value="HIBRIDA">Híbrida</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary">Salvar alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
