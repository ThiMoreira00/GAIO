<template id="template-turma-modal-remover-aluno">
    <div id="turma-modal-remover-aluno" class="modal" role="dialog" aria-labelledby="turma-modal-remover-aluno-titulo" aria-modal="true">
        <div class="modal-overlay"></div>
        <div class="modal-container max-w-lg">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <span class="material-icons-sharp text-red-600">person_remove</span>
                        </div>
                        <div>
                            <h3 id="turma-modal-remover-aluno-titulo" class="modal-title">Remover Aluno da Turma</h3>
                        </div>
                    </div>
                    <button type="button" class="modal-close" data-fechar-modal aria-label="Fechar modal">
                        <span class="material-icons-sharp">close</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form id="turma-form-remover-aluno" action="/turmas/{turma_id}/alunos/{aluno_id}/remover" method="POST" data-formulario>
                        <input type="hidden" name="turma_id" id="turma-id-remover-aluno">
                        <input type="hidden" name="aluno_id" id="aluno-id-remover">
                        
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                            <div class="flex gap-3">
                                <span class="material-icons-sharp text-amber-600 flex-shrink-0">warning</span>
                                <div class="text-sm text-amber-800">
                                    <p class="font-medium mb-1">Atenção!</p>
                                    <p>O aluno será removido automaticamente da turma. <strong>Esta ação não excluirá o registro de inscrição</strong>, apenas alterará o status para "EXCLUÍDO" mantendo o histórico.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                        <span class="material-icons-sharp text-gray-600">person</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" id="aluno-nome-remover">-</p>
                                        <p class="text-sm text-gray-500">Matrícula: <span id="aluno-matricula-remover">-</span></p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Turma: <span class="font-medium" id="turma-codigo-remover-aluno">-</span></p>
                            </div>

                            <div>
                                <label for="motivo-remocao" class="form-label">Motivo da Remoção (Opcional)</label>
                                <textarea id="motivo-remocao" name="motivo" rows="3" class="form-input" placeholder="Descreva o motivo da remoção do aluno..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="button-secondary" data-fechar-modal>Cancelar</button>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center gap-2">
                                <span class="material-icons-sharp">person_remove</span>
                                Remover Aluno
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
