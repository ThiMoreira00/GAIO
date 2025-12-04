<template id="template-inscricao-modal-solicitar">
    <div id="inscricao-modal-solicitar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">edit</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Solicitar inscrição</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Confirme sua solicitação de inscrição na turma abaixo.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="inscricao-form-solicitar" action="/inscricoes/solicitar" method="POST" class="space-y-4">
                    <input type="hidden" name="turma_id" id="inscricao-turma-id" value="">
                    <div class="modal-body">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <span class="material-icons-sharp text-blue-600">info</span>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900 mb-1">Confirme sua solicitação</h4>
                                    <p class="text-xs text-blue-700">Após o período de solicitação de inscrição, a coordenação analisará sua solicitação para esta turma.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Disciplina:</p>
                                <p class="text-sm text-gray-600" id="inscricao-turma-disciplina"></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Professor:</p>
                                <p class="text-sm text-gray-600" id="inscricao-turma-professor"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Turno:</p>
                                    <p class="text-sm text-gray-600" id="inscricao-turma-turno"></p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Modalidade:</p>
                                    <p class="text-sm text-gray-600" id="inscricao-turma-modalidade"></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Horário:</p>
                                <div class="text-sm text-gray-600" id="inscricao-turma-horario" style="line-height: 1.6;"></div>
                            </div>
                        </div>
                    </div>
                    <footer class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary" id="btn-confirmar-inscricao">Confirmar inscrição</button>
                    </footer>
                </form>
            </div>
        </div>
    </div>
</template>
