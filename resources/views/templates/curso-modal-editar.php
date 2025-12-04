<template id="template-curso-modal-editar">
    <div id="curso-modal-editar" class="modal hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
            <div class="p-4">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">edit</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Editar curso</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Altere as informações do curso abaixo.</p>
                </div>
                <button type="button" class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>
                <form id="curso-form-editar" action="/cursos/editar" method="POST" class="space-y-4">
                    <input type="hidden" name="id" id="editar-curso-id">

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="editar-curso-nome" class="form-label required">Nome do curso</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">badge</span>
                                </div>
                                <input type="text" name="nome" id="editar-curso-nome" required class="form-input pl-10" placeholder="Ex.: Análise e Desenvolvimento de Sistemas">
                            </div>
                        </div>
                        <div>
                            <label for="editar-curso-sigla" class="form-label">Sigla do curso</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">abc</span>
                                </div>
                                <input type="text" name="sigla" id="editar-curso-sigla" class="form-input pl-10" placeholder="Ex.: ADS">
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <label for="editar-curso-emec-codigo" class="form-label">
                                    Código (e-MEC)
                                </label>
                                <div class="group relative flex items-center hover:cursor-help">
                                    <span class="material-icons-sharp text-gray-500">help</span>
                                    <div role="tooltip" class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-none">
                                        Código do curso atrelado a unidade, conforme registrado no sistema e-MEC do Ministério da Educação.
                                        <div class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                </div>
                                <input type="text" name="emec-codigo" id="editar-curso-emec-codigo" class="form-input pl-10" placeholder="Ex.: 01234567">
                            </div>
                        </div>
                        <div>
                            <label for="editar-curso-grau" class="form-label required">Grau</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">workspace_premium</span>
                                </div>
                                <select name="grau" id="editar-curso-grau" required class="form-select pl-10">
                                    <option value="" disabled>Selecione o grau</option>
                                    <?php foreach ($graus as $grau): ?>
                                        <option value="<?= $grau->obterId(); ?>"><?= $grau->obterNome(); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="editar-curso-duracao-minima" class="form-label required">Duração do curso <small>(em semestres)</small></label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">schedule</span>
                                    </div>
                                    <input type="number" name="duracao-minima" id="editar-curso-duracao-minima" min="1" max="20" class="form-input pl-10" placeholder="Ex.: 5" required>
                                </div>
                            </div>
                            <div>
                                <label for="editar-curso-duracao-maxima" class="form-label required">Duração máxima <small>(em semestres)</small></label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">schedule</span>
                                    </div>
                                    <input type="number" name="duracao-maxima" id="editar-curso-duracao-maxima" min="1" max="20" class="form-input pl-10" placeholder="Ex.: 10" required>
                                </div>
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