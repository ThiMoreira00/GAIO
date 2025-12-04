<template id="template-aluno-modal-gerenciar-matriculas">
    <div id="aluno-modal-gerenciar-matriculas" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">school</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Gerenciar matrículas</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Matrículas do aluno <span class="font-semibold" id="aluno-nome-gerenciar">{{nome}}</span></p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                
                <!-- Lista de matrículas -->
                <div class="mt-6 space-y-4" id="lista-matriculas">
                    <!-- As matrículas serão carregadas dinamicamente aqui -->
                    <div class="text-center py-8 text-gray-500">
                        <span class="material-icons-sharp text-4xl mb-2">school</span>
                        <p>Carregando matrículas...</p>
                    </div>
                </div>

                <!-- Botão para adicionar nova matrícula -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <button type="button" class="button-primary w-full sm:w-auto" id="btn-adicionar-matricula">
                        <span class="material-icons-sharp !text-lg">add</span>
                        Adicionar matrícula
                    </button>
                </div>

                <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                    <button type="button" class="button-secondary button-modal-fechar">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</template>
