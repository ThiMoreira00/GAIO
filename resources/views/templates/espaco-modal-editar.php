<template id="template-espaco-modal-editar">
    <div id="espaco-modal-editar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">edit</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Editar espaço</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Altere as informações do espaço abaixo.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="espaco-form-editar" action="/espacos/editar" method="POST" class="space-y-4">
                    <input type="hidden" name="id" id="editar-espaco-id">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="espaco-nome" class="form-label required">Nome do espaço</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">badge</span>
                                </div>
                                <input type="text" name="nome" id="espaco-nome" required class="form-input pl-10" placeholder="Ex.: Sala 101">
                            </div>
                        </div>
                        <div>
                            <label for="espaco-capacidade-maxima" class="form-label required">Capacidade máxima <small>(em pessoas)</small></label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">people</span>
                                </div>
                                <input type="number" name="capacidade-maxima" id="espaco-capacidade-maxima" class="form-input pl-10" placeholder="Ex.: 50" required min="1">
                            </div>
                        </div>
                        <div>
                            <label for="espaco-tipo" class="form-label required">Tipo de espaço</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">room_preferences</span>
                                </div>
                                <select name="tipo" id="espaco-tipo" required class="form-select pl-10">
                                    <option value="" disabled selected>Selecione o tipo</option>
                                    <?php foreach ($tipos as $tipo): ?>
                                        <option value="<?= $tipo->name; ?>"><?= $tipo->value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end border-t border-gray-200">
                        <button type="button" class="button-secondary button-modal-fechar">Cancelar</button>
                        <button type="submit" class="button-primary" id="btn-salvar-alteracoes">Salvar alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>