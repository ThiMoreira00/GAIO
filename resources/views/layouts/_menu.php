<?php

use App\Models\Usuario;
use App\Services\AutenticacaoService;

$rotaAtiva = $_SERVER['REQUEST_URI'];

// GERAL
$rotaPainel = '/inicio';
$rotaCarteirinha = '/carteirinha';

// ACADÊMICO
$rotaCurso = '/curso';
$rotaDisciplinas = '/disciplinas';
$rotaInscricoes = '/inscricoes';
$rotaFrequencias = '/frequencias';
$rotaAtividades = '/atividades';
$rotaAvaliacoes = '/avaliacoes';
$rotaTurmas = '/turmas';

// DOCUMENTOS
$rotaFichaMatricula = '/documentos/ficha-matricula';
$rotaDeclaracaoMatricula = '/documentos/declaracao-matricula';
$rotaHistoricoEscolarParcial = '/documentos/historico-parcial';
$rotaGradeHorarios = '/documentos/grade-horarios';

// ADMINISTRAÇÃO
$rotaCursos = '/cursos';
$rotaAlunos = '/alunos';
$rotaPeriodos = '/periodos';
$rotaProfessores = '/professores';
$rotaRelatorios = '/relatorios';
$rotaMatrizes = '/matrizes-curriculares';
$rotaEspacos = '/espacos';

// GRUPOS
$rotaGruposPermissoes = '/grupos/permissoes';
$rotaGruposMembros = '/grupos/membros';

// Variável para verificar se algum item do menu de grupos está ativo
$isGrupoMenuActive = str_starts_with($rotaAtiva ?? '', $rotaGruposPermissoes) || str_starts_with($rotaAtiva ?? '', $rotaGruposMembros);

?>

<aside id="sidebar" class="sidebar-menu">
    <div class="sidebar-header">
        <a href="<?= obterURL('/'); ?>">
            <img class="h-10 w-auto" src="<?= obterURL('/assets/img/gaio-icone-azul.png'); ?>" alt="Logotipo do Sistema GAIO">
        </a>
        <button id="sidebar-button-close" aria-label="Fechar menu" class="sidebar-button-close">
            <span class="material-icons-sharp h-6 w-6">close</span>
        </button>
    </div>

    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <div class="sidebar-category">GERAL</div>
                <ul role="list" class="space-y-1 mt-1">
                    <li>
                        <a href="<?= obterURL($rotaPainel); ?>" class="<?= (($rotaAtiva ?? '') === $rotaPainel) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaPainel)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">home</span>
                            <span>Início</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaCarteirinha); ?>" class="<?= (($rotaAtiva ?? '') === $rotaCarteirinha) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaCarteirinha)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">badge</span>
                            <span>Carteirinha</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <div class="sidebar-category">ACADÊMICO</div>
                <ul role="list" class="space-y-1 mt-1">
                    <li>
                        <a href="<?= obterURL($rotaCurso); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaCurso)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaCurso)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">school</span>
                            <span>Curso</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaDisciplinas); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaDisciplinas)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaDisciplinas)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">book</span>
                            <span>Disciplinas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaInscricoes); ?>" class="<?= (($rotaAtiva ?? '') === $rotaInscricoes) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaInscricoes)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">sticky_note_2</span>
                            <span>Inscrições</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaTurmas); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaTurmas)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaTurmas)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">groups</span>
                            <span>Turmas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaFrequencias); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaFrequencias)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaFrequencias)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">rule</span>
                            <span>Frequências</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaAvaliacoes); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaAvaliacoes)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaAvaliacoes)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">assignment</span>
                            <span>Avaliações / Notas</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <div class="sidebar-category">DOCUMENTOS</div>
                <ul role="list" class="space-y-1 mt-1">
                    <li>
                        <a href="<?= obterURL($rotaFichaMatricula); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaFichaMatricula)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaFichaMatricula)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">description</span>
                            <span>Ficha de Matrícula</span> <span class="material-icons-sharp !text-base">open_in_new</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaDeclaracaoMatricula); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaDeclaracaoMatricula)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaDeclaracaoMatricula)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">description</span>
                            <span class="truncate">Declaração de Matrícula</span> <span class="material-icons-sharp !text-base">open_in_new</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaHistoricoEscolarParcial); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaHistoricoEscolarParcial)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaHistoricoEscolarParcial)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">description</span>
                            <span class="truncate">Histórico Escolar (Parcial)</span> <span class="material-icons-sharp !text-base">open_in_new</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaGradeHorarios); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaGradeHorarios)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaGradeHorarios)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">description</span>
                            <span class="truncate">Grade de Horários</span> <span class="material-icons-sharp !text-base">open_in_new</span>
                        </a>
                    </li>
                    
                    
                    
                </ul>
            </li>
            
            <?php if (AutenticacaoService::usuarioAutenticado()->verificarAdministrador()): ?>
            <li>
                <div class="sidebar-category">ADMINISTRAÇÃO</div>
                <ul role="list" class="space-y-1 mt-1">
                    <li>
                        <a href="<?= obterURL($rotaCursos); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaCursos)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaCursos)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">school</span>
                            <span>Cursos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaAlunos); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaAlunos)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaAlunos)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">face</span>
                            <span>Alunos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaProfessores); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaProfessores)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaProfessores)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">co_present</span>
                            <span>Professores</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaPeriodos); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaPeriodos)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaPeriodos)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">date_range</span>
                            <span>Períodos Letivos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaMatrizes); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaMatrizes)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaMatrizes)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">view_compact</span>
                            <span>Matrizes Curriculares</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaRelatorios); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaRelatorios)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaRelatorios)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">analytics</span>
                            <span>Relatórios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= obterURL($rotaEspacos); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaEspacos)) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', $rotaEspacos)) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">meeting_room</span>
                            <span>Espaços</span>
                        </a>
                    </li>

                    <!-- Dropdown de Grupos -->
                    <li>
                        <button type="button" data-toggle="dropdown" aria-haspopup="true"class="group flex items-center w-full gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 <?= $isGrupoMenuActive ? 'bg-sky-100 text-sky-700' : 'text-gray-700 hover:text-sky-700 hover:bg-sky-50'; ?>">
                            <span class="material-icons-sharp <?= $isGrupoMenuActive ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">admin_panel_settings</span>
                            <span>Grupos</span>
                            <span class="material-icons-sharp ml-auto h-6 w-6 transform transition-transform duration-200 ease-in-out <?= $isGrupoMenuActive ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>" data-chevron="true"><?= $isGrupoMenuActive ? 'expand_less' : 'expand_more'; ?></span>
                        </button>
                        <!-- Submenu -->
                        <ul class="mt-1 space-y-1 pl-5 <?= $isGrupoMenuActive ? 'block' : 'hidden'; ?>">
                            <li>
                                <a href="<?= obterURL($rotaGruposPermissoes); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaGruposPermissoes)) ? 'sidebar-subitem-active' : 'sidebar-subitem'; ?>">
                                    <span>Permissões</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= obterURL($rotaGruposMembros); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', $rotaGruposMembros)) ? 'sidebar-subitem-active' : 'sidebar-subitem'; ?>">
                                    <span>Membros</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Logs -->
                    <li>
                        <a href="<?= obterURL('/logs'); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', '/logs')) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp <?= (str_starts_with($rotaAtiva ?? '', '/logs')) ? 'text-sky-700' : 'text-gray-500 group-hover:text-sky-700'; ?>">history</span>
                            <span>Logs</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 px-2">TURMAS</div>
                <ul role="list" class="space-y-1 mt-1">
                    <li>
                        <a href="<?= obterURL('/turmas/arquivadas'); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', '/turmas/arquivadas')) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp text-gray-500 group-hover:text-sky-700">archive</span>
                            <span>Arquivadas</span>
                        </a>
                    </li>
                    <?php foreach ($turmas as $turma): 
                    /*
                    <li>
                        <a href="<?= obterURL('/'); ?>" class="<?= (str_starts_with($rotaAtiva ?? '', '/turmas/ativas')) ? 'sidebar-item-active group' : 'sidebar-item group'; ?>">
                            <span class="material-icons-sharp text-sky-500 group-hover:text-sky-700">book</span>
                            <span><?= sprintf("%s - %s", $turma->obterCodigo(), $turma->obterPeriodo()->obterSigla()) ?></span>
                        </a>
                    </li>
                    */
                    
                 endforeach; ?>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
