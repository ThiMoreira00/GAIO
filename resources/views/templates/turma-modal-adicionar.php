<template id="template-turma-modal-adicionar">
    <div id="turma-modal-adicionar" class="modal2 fixed inset-0 z-50 hidden items-center justify-center bg-black/60" data-modal2>
        <div class="relative w-full max-w-4xl rounded-lg bg-white shadow-xl max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                        <span class="material-icons-sharp text-blue-600">groups</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Adicionar nova turma</h3>
                        <p class="text-sm text-gray-500" data-modal2-subtitle>Etapa 1 de 2: Informações básicas</p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-500" data-modal2-close>
                    <span class="material-icons-sharp">close</span>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="px-6 pt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-600" data-modal2-step-label>Etapa 1 de 2</span>
                    <span class="text-xs text-gray-500" data-modal2-progress-text>50%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 50%" data-modal2-progress-bar></div>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form id="turma-form-adicionar" action="/turmas/adicionar" method="POST" class="space-y-6" data-modal2-form>
                    <!-- Etapa 1: Informações Básicas -->
                    <div data-modal2-step="1" class="space-y-4">
                        <div>
                            <label for="turma-curso" class="form-label required">Curso</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">school</span>
                                </div>
                                <select name="curso_id" id="turma-curso" required class="form-select pl-10">
                                    <option value="" disabled selected>Selecione o curso</option>
                                    <?php if (isset($cursos)): ?>
                                        <?php foreach ($cursos as $curso): ?>
                                            <option value="<?= $curso->obterId(); ?>"><?= $curso->obterNome(); ?> (<?= $curso->obterSigla(); ?>)</option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="turma-disciplina" class="form-label required">Disciplina</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">book</span>
                                </div>
                                <select name="disciplina_id" id="turma-disciplina" required class="form-select pl-10" disabled>
                                    <option value="" disabled selected>Selecione primeiro o curso</option>
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <span class="material-icons-sharp !text-xs align-middle">info</span>
                                Se não aparecer nenhuma disciplina, é necessário <a href="/matrizes" class="text-blue-600 hover:underline font-medium">registrar uma matriz curricular</a> para este curso.
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="turma-codigo" class="form-label required">Código da turma</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">label</span>
                                    </div>
                                    <input type="text" name="codigo" id="turma-codigo" required class="form-input pl-10" placeholder="Ex.: TUR-2025-001">
                                </div>
                            </div>
                            <div>
                                <label for="turma-periodo" class="form-label required">Período Letivo</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">calendar_today</span>
                                    </div>
                                    <select name="periodo_id" id="turma-periodo" required class="form-select pl-10">
                                        <option value="" disabled selected>Selecione o período</option>
                                        <?php if (isset($periodos)): ?>
                                            <?php foreach ($periodos as $periodo): ?>
                                                <option value="<?= $periodo->obterId(); ?>"><?= $periodo->obterSigla(); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="turma-professor" class="form-label required">Professor</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">person</span>
                                </div>
                                <select name="professor_id" id="turma-professor" required class="form-select pl-10">
                                    <option value="" disabled selected>Selecione o professor</option>
                                    <?php if (isset($professores)): ?>
                                        <?php foreach ($professores as $professor): ?>
                                            <?php $usuario = $professor->usuario()->first(); ?>
                                            <?php if ($usuario): ?>
                                                <option value="<?= $professor->obterId(); ?>">
                                                    <?= $usuario->obterNomeSocial() ?: $usuario->obterNomeCivil(); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="turma-turno" class="form-label required">Turno</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">schedule</span>
                                    </div>
                                    <select name="turno" id="turma-turno" required class="form-select pl-10">
                                        <option value="" disabled selected>Selecione o turno</option>
                                        <option value="MANHA">Manhã</option>
                                        <option value="TARDE">Tarde</option>
                                        <option value="NOITE">Noite</option>
                                        <option value="INTEGRAL">Integral</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="turma-capacidade" class="form-label required">Capacidade Máxima</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">people</span>
                                    </div>
                                    <input type="number" name="capacidade_maxima" id="turma-capacidade" min="1" max="200" class="form-input pl-10" placeholder="Ex.: 40" required>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="turma-modalidade" class="form-label required">Modalidade</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">laptop</span>
                                </div>
                                <select name="modalidade" id="turma-modalidade" required class="form-select pl-10">
                                    <option value="" disabled selected>Selecione a modalidade</option>
                                    <option value="PRESENCIAL">Presencial</option>
                                    <option value="REMOTA">Remota</option>
                                    <option value="HIBRIDA">Híbrida</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="turma-grade" class="form-label">Grade Horária</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">grid_on</span>
                                </div>
                                <select name="grade_id" id="turma-grade" class="form-select pl-10">
                                    <option value="">Selecione a grade (opcional)</option>
                                    <?php
                                    // TODO: Buscar grades horarias do banco
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 2: Grade de Horários -->
                    <div data-modal2-step="2" class="hidden space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <span class="material-icons-sharp text-blue-600">schedule</span>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900 mb-1">Selecione os horários das aulas</h4>
                                    <p class="text-xs text-blue-700">Clique nos horários disponíveis para adicionar aulas. O turno selecionado foi: <strong data-turno-selecionado></strong></p>
                                </div>
                            </div>
                        </div>

                        <!-- Grade de Horários -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm" id="grade-horarios">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 w-24">Horário</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Segunda</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Terça</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Quarta</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Quinta</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Sexta</th>
                                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700">Sábado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="grade-horarios-body" class="divide-y divide-gray-200">
                                        <!-- Horários serão preenchidos dinamicamente com base no turno -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <input type="hidden" name="horarios" id="horarios-selecionados" value="[]">
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex flex-col-reverse sm:flex-row sm:justify-between gap-3">
                <button type="button" class="button-secondary" data-modal2-back>
                    <span class="material-icons-sharp text-sm">arrow_back</span>
                    <span>Voltar</span>
                </button>
                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <button type="button" class="button-secondary" data-modal2-close>Cancelar</button>
                    <button type="button" class="button-primary" data-modal2-next>
                        <span>Próximo</span>
                        <span class="material-icons-sharp text-sm">arrow_forward</span>
                    </button>
                    <button type="submit" form="turma-form-adicionar" class="button-primary hidden" data-modal2-submit id="btn-salvar-turma">
                        <span class="material-icons-sharp text-sm">check</span>
                        <span>Salvar turma</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
