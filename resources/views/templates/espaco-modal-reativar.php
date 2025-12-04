<template id="template-espaco-modal-reativar">
    <div id="espaco-modal-reativar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                    <span class="material-icons-sharp text-green-600">autorenew</span>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <div class="mt-3 mb-8 text-center" id="info-reativar">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Reativar espaço</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Tem certeza que deseja reativar o espaço <span class="font-semibold" id="espaco-nome-reativar">{{nome}}</span>? O espaço voltará a ficar disponível no sistema.</p>
                </div>
                <form id="espaco-form-reativar" method="POST" action="/espacos/reativar">
                    <input type="hidden" name="id" class="espaco-id-input">
                    <div class="mt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-success">Sim, reativar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
