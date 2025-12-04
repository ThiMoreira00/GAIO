<?php

use App\Models\Usuario;
use App\Services\AutenticacaoService;

?>

<h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl my-3">
    <?php

    // Verificar que intervalo de horas está
    $horas = date('H');

    if ($horas >= 6 && $horas < 12) {
        echo 'Bom dia';
    } elseif ($horas >= 12 && $horas < 18) {
        echo 'Boa tarde';
    } else {
        echo 'Boa noite';
    }
    ?>, <?= AutenticacaoService::usuarioAutenticado()->obterNomeReduzido(); ?>!
</h1>

<div class="flex flex-col gap-8 my-8">
    <section>
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Estatísticas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Disciplinas em andamento</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">6</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp w-6 h-6 text-blue-500">book</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Andamento do curso</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">18%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp w-6 h-6 text-green-500">school</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Coeficiente de Rendimento</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">7.4</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp w-6 h-6 text-yellow-500">trending_up</span>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Período atual</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?= $estatisticas->periodo_atual; ?></p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl flex align-center justify-center">
                    <span class="material-icons-sharp w-6 h-6 text-purple-500">calendar_today</span>
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
                
                $minimoTurmas = min($turmas->count(), 3);
                ?>
                    <ul class="grid grid-cols-1 gap-4 mt-2">
                        <?php for($x = 1; $x <= $minimoTurmas; $x++ ): 
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

        <section>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Calendário</h2>

            <div class="bg-white p-6 rounded-lg">
                <div id="calendario-container"></div>
            </div>

        </section>
    </div>
</div>
<script src="<?= obterURL('/assets/javascript/utils/calendario.js') ?>"></script>
<script type="text/javascript">
    
    document.addEventListener('DOMContentLoaded', () => {
        new Calendario('calendario-container');
    });
    
</script>