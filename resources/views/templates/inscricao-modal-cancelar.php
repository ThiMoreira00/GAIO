<template id="template-inscricao-modal-cancelar">
    <div id="inscricao-modal-cancelar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <span class="material-icons-sharp text-red-600">warning</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Cancelar inscrição</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Tem certeza que deseja cancelar sua inscrição?</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="inscricao-form-cancelar" action="/inscricoes/cancelar" method="POST" class="space-y-4">
                    <input type="hidden" name="inscricao_id" id="inscricao-cancelar-id" value="">
                    <div class="modal-body">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <span class="material-icons-sharp text-red-600">info</span>
                                <div>
                                    <h4 class="text-sm font-semibold text-red-900 mb-1">Atenção!</h4>
                                    <p class="text-xs text-red-700">Você está cancelando sua inscrição nesta turma. Você poderá solicitar a inscrição novamente, se desejar.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Disciplina:</p>
                                <p class="text-sm text-gray-600" id="inscricao-cancelar-disciplina"></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Professor:</p>
                                <p class="text-sm text-gray-600" id="inscricao-cancelar-professor"></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Horário:</p>
                                <div class="text-sm text-gray-600" id="inscricao-cancelar-horario" style="line-height: 1.6;"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Turno:</p>
                                <p class="text-sm text-gray-600" id="inscricao-cancelar-turno"></p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Modalidade:</p>
                                <p class="text-sm text-gray-600" id="inscricao-cancelar-modalidade"></p>
                            </div>
                        </div>

                    </div>
                    <footer class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Não, manter inscrição</button>
                        <button type="submit" class="button-danger" id="btn-confirmar-cancelamento">Sim, cancelar inscrição</button>
                    </footer>
                </form>
            </div>
        </div>
    </div>
</template>
