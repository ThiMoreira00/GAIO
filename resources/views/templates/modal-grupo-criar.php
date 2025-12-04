<template id="template-modal-grupo-criar">
    <div id="modal-criar-grupo" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">group_add</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800" id="modal-title">Criar novo grupo</h3>
                    <p class="mt-2 text-sm text-gray-600">Preencha as informações abaixo para criar um novo grupo.</p>
                </div>
                <button type="button" class="modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="form-criar-grupo" action="/grupos/criar" method="POST" class="space-y-4">
                    <div class="mb-4">
                        <label for="grupo-nome" class="form-label required">Nome do grupo</label>
                        <div class="relative mt-1">
                            <span class="material-icons-sharp text-gray-400 absolute inset-y-0 left-0 pl-3 !flex items-center">groups</span>
                            <input type="text" name="grupo-nome" id="grupo-nome" required class="form-input pl-10" placeholder="Ex.: Alunos">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="grupo-descricao" class="form-label">Descrição</label>
                        <div class="relative mt-1">
                            <span class="material-icons-sharp text-gray-400 absolute inset-y-0 left-0 pl-3 pt-[12px]">subject</span>
                            <textarea name="grupo-descricao" id="grupo-descricao" rows="4" class="form-input pl-10" placeholder="Descreva o propósito deste grupo..."></textarea>
                        </div>
                    </div>
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary">Criar grupo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>