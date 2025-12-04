<?php

use App\Models\Usuario;
use App\Services\AutenticacaoService;


?>

<h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl my-3">
    <?php

    // Verificar que intervalo de horas está
    $horas = date('H');

    if ($horas >= 0 && $horas < 12) {
        echo 'Bom dia';
    } elseif ($horas >= 12 && $horas < 18) {
        echo 'Boa tarde';
    } else {
        echo 'Boa noite';
    }
    ?>, <?= AutenticacaoService::usuarioAutenticado()->obterNomeReduzido(); ?>!
</h1>

<?= flash()->exibir(); ?>

<div class="flex flex-col gap-8 my-8">
    <section>
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Estatísticas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Turmas ativas em <?= $estatisticas->periodo_ultimo; ?></p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?= $estatisticas->turmas_ativas; ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp text-blue-500">book</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Alunos matriculados em <?= $estatisticas->periodo_ultimo; ?></p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?= $estatisticas->alunos_matriculados; ?></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp text-yellow-500">face</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Professores alocados em <?= $estatisticas->periodo_ultimo; ?></p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?= $estatisticas->professores_alocados; ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp text-green-500">co_present</span>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Alunos integralizando em <?= $estatisticas->periodo_ultimo; ?></p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?= $estatisticas->alunos_integralizando; ?></p>
                </div>
                <div class="bg-red-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp text-red-500">person_off</span>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <section class="lg:col-span-2">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Turmas</h2>
                <a href="<?= obterURL('/turmas') ?>" class="text-sky-600 hover:text-sky-800 font-medium underline">Ver todas as turmas</a>
            </div>

            <div>
                <?php 
                if (isset($turmas) && $turmas->count() > 0): 
                
                $minimoTurmas = min($turmas->count(), 2);
                ?>
                    <ul class="grid grid-cols-1 gap-4 mt-2">
                        <?php for($x = 0; $x <= $minimoTurmas; $x++ ): 
                            $turma = $turmas[$x];
                            ?>
                            <li class="bg-white p-4 px-6 rounded-lg">
                                <span class="block text-xs text-gray-500">
                                    <?= $turma->obterCodigo() ?> &bull; 
                                    <?= strtoupper($turma->obterTurno()->value) ?>
                                </span>
                                <a href="<?= obterURL('/turmas/' . $turma->obterId()) ?>" class="block text-sky-600 font-bold hover:underline">
                                    <?= $turma->disciplina?->obterSigla() ?> - <?= $turma->disciplina?->componenteCurricular?->obterNome() ?>
                                </a>
                                <span class="block text-sm text-gray-700">
                                    Prof. <?= $turma->professor?->usuario?->obterNomeReduzido() ?? 'Não atribuído' ?>
                                </span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php else: ?>
                    <div class="bg-white p-6 rounded-lg text-center text-gray-500 mt-2 min-h-[320px] flex flex-col items-center justify-center">
                        <span class="material-icons-sharp text-4xl mb-2">schedule</span>
                        <p>Nenhuma turma ativa no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="min-w-0">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Calendário</h2>

            <div class="bg-white p-6 rounded-lg overflow-auto">
                <div id="calendario-container" class="min-w-[280px]"></div>
            </div>

        </section>
    </div>

    <section>
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Ações rápidas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <a href="<?= obterURL('/alunos') ?>" class="bg-white p-4 rounded-2xl flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-100 p-2 rounded-lg flex align-center justify-center">
                        <span class="material-icons-sharp w-5 h-5 text-orange-500">groups</span>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">Alunos</p>
                        <p class="text-sm text-gray-500">Gerencie os alunos cadastrados.</p>
                    </div>
                </div>
                <span class="material-icons-sharp w-6 h-6 text-gray-400">launch</span>
            </a>

            <a href="<?= obterURL('/turmas') ?>" class="bg-white p-4 rounded-2xl flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="bg-cyan-100 p-2 rounded-lg flex align-center justify-center">
                        <span class="material-icons-sharp w-5 h-5 text-cyan-500">school</span>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">Turmas</p>
                        <p class="text-sm text-gray-500">Crie e administre as turmas.</p>
                    </div>
                </div>
                <span class="material-icons-sharp w-6 h-6 text-gray-400">launch</span>
            </a>

            <a href="<?= obterURL('/inscricoes') ?>" class="bg-white p-4 rounded-2xl flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="bg-pink-100 p-2 rounded-lg flex align-center justify-center">
                        <span class="material-icons-sharp w-5 h-5 text-pink-500">how_to_reg</span>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">Inscrições</p>
                        <p class="text-sm text-gray-500">Inscreva alunos nas turmas.</p>
                    </div>
                </div>
                <span class="material-icons-sharp w-6 h-6 text-gray-400">launch</span>
            </a>

        </div>
    </section>
    </div>

<script src="<?= obterURL('/assets/javascript/utils/calendario.js') ?>"></script>
<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', () => {
        new Calendario('calendario-container');
    });
    
</script>