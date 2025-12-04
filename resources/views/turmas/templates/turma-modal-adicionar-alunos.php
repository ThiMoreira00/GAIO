<template id="template-turma-modal-adicionar-alunos">
    <div id="turma-modal-adicionar-alunos" class="modal" role="dialog" aria-labelledby="turma-modal-adicionar-alunos-titulo" aria-modal="true">
        <div class="modal-overlay"></div>
        <div class="modal-container max-w-2xl">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-sky-100 rounded-lg flex items-center justify-center">
                            <span class="material-icons-sharp text-sky-600">person_add</span>
                        </div>
                        <div>
                            <h3 id="turma-modal-adicionar-alunos-titulo" class="modal-title">Adicionar Alunos à Turma</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Turma: <span id="turma-codigo-adicionar-alunos" class="font-medium"></span></p>
                        </div>
                    </div>
                    <button type="button" class="modal-close" data-fechar-modal aria-label="Fechar modal">
                        <span class="material-icons-sharp">close</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <form id="turma-form-adicionar-alunos" action="/turmas/{id}/alunos/adicionar" method="POST" data-formulario>
                        <input type="hidden" name="turma_id" id="turma-id-adicionar-alunos">
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex gap-3">
                                <span class="material-icons-sharp text-blue-600 flex-shrink-0">info</span>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">Informação Importante</p>
                                    <p>Os alunos selecionados serão adicionados automaticamente à turma, <strong>sem a necessidade de solicitação de inscrição</strong>. Esta ação criará registros de inscrição com status aprovado.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="alunos-busca" class="form-label">Buscar Alunos</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">search</span>
                                    </div>
                                    <input type="search" id="alunos-busca" class="form-input pl-10" placeholder="Digite o nome ou matrícula do aluno...">
                                </div>
                            </div>

                            <div>
                                <label class="form-label required">Selecione os Alunos</label>
                                <div id="container-alunos-disponiveis" class="border border-gray-300 rounded-lg max-h-64 overflow-y-auto">
                                    <div class="text-center py-8 text-gray-500">
                                        <span class="material-icons-sharp text-4xl text-gray-300">person_search</span>
                                        <p class="mt-2 text-sm">Use o campo de busca para encontrar alunos</p>
                                    </div>
                                </div>
                            </div>

                            <div id="alunos-selecionados-preview" class="hidden">
                                <label class="form-label">Alunos Selecionados (<span id="contador-alunos-selecionados">0</span>)</label>
                                <div id="lista-alunos-selecionados" class="flex flex-wrap gap-2"></div>
                            </div>

                            <input type="hidden" name="alunos_ids" id="alunos-ids-input" value="[]">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="button-secondary" data-fechar-modal>Cancelar</button>
                            <button type="submit" class="button-primary">
                                <span class="material-icons-sharp">person_add</span>
                                Adicionar Alunos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// Script será carregado pelo JavaScript principal
</script>
