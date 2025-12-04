<template id="template-periodo-letivo-modal-adicionar">
    <div id="periodo-letivo-modal-adicionar-form" class="modal fixed hidden inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">school</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Adicionar novo período letivo</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Preencha as informações para adicionar um novo período letivo.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="form-adicionar-periodo-letivo" action="/periodos-letivos/adicionar" method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="periodo-sigla" class="form-label required">Sigla do período letivo</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">abc</span>
                                </div>
                                <input type="text" name="sigla" id="periodo-sigla" required class="form-input pl-10" placeholder="Ex.: 2026.1" pattern="^\d{4}\.\d$" title="A sigla deve estar no formato 'AAAA.X', onde 'AAAA' é o ano com 4 dígitos e 'X' é o semestre (1 ou 2).">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="periodo-data-inicio" class="form-label required">Data de início</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">schedule</span>
                                    </div>
                                    <input type="date" name="data_inicio" id="periodo-data-inicio" class="form-input pl-10" required>
                                </div>
                            </div>
                            <div>
                                <label for="periodo-data-termino" class="form-label required">Data de término</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">schedule</span>
                                    </div>
                                    <input type="date" name="data_termino" id="periodo-data-termino" class="form-input pl-10" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary" id="btn-adicionar-periodo-letivo">Adicionar período letivo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>