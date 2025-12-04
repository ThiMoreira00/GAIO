<?php
    $permissoesParaVisao = [
        'visualizar' => !empty($permissoes['visualizar']),
        'editar' => !empty($permissoes['editar']),
        'inativar' => !empty($permissoes['inativar']),
        'validar' => !empty($permissoes['validar']),
        'componenteCadastrar' => !empty($permissoes['componente_cadastrar']),
        'componenteEditar' => !empty($permissoes['componente_editar']),
        'componenteExcluir' => !empty($permissoes['componente_excluir']),
        'componentePrerequisito' => !empty($permissoes['componente_prerequisito']),
        'componenteEquivalencia' => !empty($permissoes['componente_equivalencia'])
    ];

    $configuracaoMatriz = [
        'matrizId' => $matriz->obterId(),
        'matrizStatus' => $matriz->obterStatus()->value ?? '',
        'permissoes' => $permissoesParaVisao,
        'rotas' => [
            'componentes' => obterURL("/matrizes-curriculares/{$matriz->obterId()}/componentes/obter")
        ]
    ];

    $configuracaoMatrizJson = htmlspecialchars(
        json_encode($configuracaoMatriz, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ENT_QUOTES,
        'UTF-8'
    );
?>

<script src="https://cdn.jsdelivr.net/npm/leader-line-new@1.1.9/leader-line.min.js"></script>

<div id="container-matriz" data-config='<?= $configuracaoMatrizJson ?>'>
    <header class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between py-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center text-white <?= ($matriz->obterStatus()->value ?? '') === 'Arquivado' ? 'bg-gray-500' : 'bg-sky-600' ?>">
                <span class="material-icons-sharp">auto_stories</span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Curso • <?= $curso->obterNome() ?? '[não informado]' ?></p>
                <h1 class="text-2xl/7 font-bold text-gray-900 sm:text-3xl">Matriz Curricular #<?= $matriz->obterId() ?></h1>
                <p class="text-gray-500 text-sm">Última atualização em <?= $matriz->updated_at?->format('d/m/Y H:i') ?? 'N/A' ?></p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <?php if (($matriz->obterStatus()->value ?? '') !== 'Arquivado'): ?>
                <?php if ($permissoes['editar']): ?>
                <button type="button" class="button-secondary" data-modal-trigger="modal-matriz-editar" data-matriz-id="<?= $matriz->obterId() ?>">
                    <span class="material-icons-sharp -ml-1 mr-2">edit</span>
                    Editar Matriz
                </button>
                <?php endif; ?>
                <?php if ($permissoes['inativar']): ?>
                <button type="button" class="button-danger" data-modal-trigger="modal-inativar-matriz" data-matriz-id="<?= $matriz->obterId() ?>">
                    <span class="material-icons-sharp -ml-1 mr-2">block</span>
                    Inativar
                </button>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (($curso->obterStatus() ?? '') !== 'Arquivado'): ?>
                <button type="button" class="button-secondary" data-modal-trigger="modal-curso-editar" data-curso-id="<?= $curso->obterId() ?>">
                    <span class="material-icons-sharp -ml-1 mr-2">edit</span>
                    Editar Curso
                </button>
                <button type="button" class="button-danger" data-modal-trigger="modal-arquivar-curso" data-curso-id="<?= $curso->obterId() ?>">
                    <span class="material-icons-sharp -ml-1 mr-2">archive</span>
                    Arquivar Curso
                </button>
            <?php endif; ?>
        </div>
    </header>

    <div class="mt-6" id="matriz-tabs">
        <div class="border-b border-gray-200">
            <ul class="-mb-px flex flex-wrap gap-4" aria-label="Abas">
                <li>
                    <button data-tab-target="#visao-geral" class="tab-button active whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm border-sky-500 text-sky-600">
                        Detalhes da Matriz
                    </button>
                </li>
                <?php if ($permissoes['visualizar']): ?>
                <li>
                    <button data-tab-target="#componentes" class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Grade Curricular
                    </button>
                </li>
                <li>
                    <button data-tab-target="#fluxograma" class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Fluxograma
                    </button>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="py-8">
            <!-- Visão Geral -->
            <div id="visao-geral" class="tab-panel">
                <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Informações da Matriz Curricular</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">school</span>
                            <div class="flex-1">
                                <dt class="text-gray-500 text-sm">Curso</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="matriz-curso-nome"><?= $curso->obterNome() ?? '[não informado]' ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">tag</span>
                            <div class="flex-1">
                                <dt class="text-gray-500 text-sm">Identificação</dt>
                                <dd class="font-medium text-gray-800 mt-1">Matriz #<?= $matriz->obterId() ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">calendar_today</span>
                            <div class="flex-1">
                                <dt class="text-gray-500 text-sm">Quantidade de períodos</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="matriz-quantidade-periodos">
                                    <?= $matriz->obterQuantidadePeriodos() ?> período<?= $matriz->obterQuantidadePeriodos() > 1 ? 's' : '' ?>
                                </dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">label</span>
                            <div class="flex-1">
                                <dt class="text-gray-500 text-sm">Status</dt>
                                <dd class="mt-1">
                                    <?php 
                                    $status = $matriz->obterStatus()->value ?? '';
                                    $statusClass = $status === 'Vigente' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="text-xs font-bold inline-flex items-center px-3 py-1 rounded-full <?= $statusClass ?>"><?= strtoupper($status) ?></span>
                                </dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">event</span>
                            <div class="flex-1">
                                <dt class="text-gray-500 text-sm">Data de criação</dt>
                                <dd class="font-medium text-gray-800 mt-1" id="matriz-data-criacao"><?= $matriz->created_at?->format('d/m/Y H:i') ?? 'N/A' ?></dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-sharp text-gray-400 mt-0.5">update</span>
                            <div class="flex-1">
                                <dt class="text-gray-500 text-sm">Última atualização em</dt>
                                <dd class="font-medium text-gray-800 mt-1"><?= $matriz->updated_at?->format('d/m/Y H:i') ?? 'N/A' ?></dd>
                            </div>
                        </div>
                    </dl>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-800 mb-4">Resumo da Matriz</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-sky-50 p-4 rounded-lg border border-sky-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-sky-600 rounded-lg flex items-center justify-center">
                                        <span class="material-icons-sharp text-white text-lg">library_books</span>
                                    </div>
                                    <div>
                                        <dt class="text-sky-600 text-xs font-medium uppercase">Total de Componentes</dt>
                                        <dd class="text-2xl font-bold text-sky-900 mt-1" id="stat-total-componentes">0</dd>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                        <span class="material-icons-sharp text-white text-lg">check_circle</span>
                                    </div>
                                    <div>
                                        <dt class="text-green-600 text-xs font-medium uppercase">Obrigatórios</dt>
                                        <dd class="text-2xl font-bold text-green-900 mt-1" id="stat-obrigatorias">0</dd>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center">
                                        <span class="material-icons-sharp text-white text-lg">star</span>
                                    </div>
                                    <div>
                                        <dt class="text-amber-600 text-xs font-medium uppercase">Optativos</dt>
                                        <dd class="text-2xl font-bold text-amber-900 mt-1" id="stat-optativas">0</dd>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                        <span class="material-icons-sharp text-white text-lg">schedule</span>
                                    </div>
                                    <div>
                                        <dt class="text-purple-600 text-xs font-medium uppercase">Carga Horária Total</dt>
                                        <dd class="text-2xl font-bold text-purple-900 mt-1" id="stat-carga-horaria">0h</dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($permissoes['visualizar']): ?>
            <!-- Grade Curricular -->
            <div id="componentes" class="tab-panel hidden">
                <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Grade Curricular</h3>
                            <p class="text-sm text-gray-500">Visualize os componentes organizados por período.</p>
                        </div>
                        <?php if ($permissoes['componente_cadastrar'] && ($matriz->obterStatus()->value ?? '') !== 'Arquivado'): ?>
                        <button type="button" class="button-primary" id="btn-adicionar-componente">
                            <span class="material-icons-sharp -ml-1 mr-2">add</span>
                            Adicionar Componente
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="mb-6 flex flex-wrap gap-4">
                        <select id="filtro-periodo" class="form-select">
                            <option value="">Todos os períodos</option>
                        </select>
                        <select id="filtro-tipo" class="form-select">
                            <option value="">Todos os tipos</option>
                            <option value="Obrigatória">Obrigatória</option>
                            <option value="Optativa">Optativa</option>
                            <option value="Eletiva">Eletiva</option>
                        </select>
                    </div>

                    <div id="tabela-componentes" class="overflow-x-auto"></div>
                </div>
            </div>

            <!-- Fluxograma -->
            <div id="fluxograma-archived" class="tab-panel hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Fluxograma da Matriz Curricular</h3>
                                <p class="text-sm text-gray-500">As linhas percorrem o espaço entre os componentes, conectando lateral direita &rarr; lateral esquerda.</p>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap justify-end">
                                <?php if (!empty($permissoes["componente_prerequisito"])): ?>
                                <button type="button"
                                        id="btn-modo-prerequisito"
                                        aria-pressed="false"
                                        title="Selecione duas disciplinas e ligue-as por seta para definir pré-requisito"
                                        class="flex items-center gap-2 px-3 py-2 text-xs font-semibold rounded-md border border-sky-600 bg-sky-600 text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition">
                                    <span class="material-icons-sharp text-base">call_merge</span>
                                    <span class="hidden md:inline">Pré-requisito</span>
                                </button>
                                <?php endif; ?>
                                <button type="button" class="button-secondary-sm" id="btn-fit-screen" title="Ir para o início">
                                    <span class="material-icons-sharp">first_page</span>
                                </button>
                                <button type="button" class="button-secondary-sm" id="btn-layout-vertical" title="Distribuir em colunas">
                                    <span class="material-icons-sharp">view_agenda</span>
                                </button>
                                <button type="button" class="button-secondary-sm" id="btn-layout-horizontal" title="Distribuir em linhas">
                                    <span class="material-icons-sharp">view_day</span>
                                </button>
                                <button type="button" class="button-secondary-sm" id="btn-toggle-diagonais" title="Alternar setas diagonais" aria-pressed="false">
                                    <span class="material-icons-sharp">timeline</span>
                                </button>
                                <label for="fluxo-espacamento" class="sr-only">Espaçamento entre colunas</label>
                                <select id="fluxo-espacamento" class="form-select text-sm py-1 pl-3 pr-8 border-gray-300 focus:border-sky-500 focus:ring-sky-500" aria-label="Espaçamento entre períodos">
                                    <option value="compacto">Compacto</option>
                                    <option value="padrao" selected>Intermediário</option>
                                    <option value="amplo">Amplo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 fluxograma-canvas-legend">
                            <span class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded bg-blue-500"></span>Obrigatória</span>
                            <span class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded bg-green-500"></span>Optativa</span>
                            <span class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded bg-purple-500"></span>Eletiva</span>
                            <span class="flex items-center gap-2"><span class="inline-block w-8 h-0.5 bg-red-500"></span>Pré-requisito</span>
                            <span class="flex items-center gap-2"><span class="inline-block w-8 h-0.5 bg-yellow-500 fluxo-legenda-equivalencia"></span>Equivalência</span>
                        </div>
                    </div>

                    <div id="fluxograma-canvas" class="fluxograma-canvas">
                        <div class="flex justify-center items-center text-gray-400 gap-3 h-full w-full">
                            <span class="material-icons-sharp animate-spin">autorenew</span>
                            <span>Montando fluxograma...</span>
                        </div>
                    </div>

                    <div class="px-6 pb-6 border-t border-gray-100 bg-gray-50">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2">Leitura das conexões</h4>
                        <p class="text-sm text-gray-600">
                            As setas vermelhas conectam componentes que possuem relação de pré-requisito, sempre partindo da lateral direita do componente de origem. As linhas amarelas tracejadas representam equivalências entre componentes ofertados em períodos diferentes.
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($permissoes['editar']): ?>
            <!-- Organização Visual -->
            <div id="fluxograma" class="tab-panel hidden">
                <div class="bg-white p-6 sm:p-8 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Organização Visual da Matriz</h3>
                            <p class="text-sm text-gray-500">Arraste e solte as disciplinas para reorganizar os períodos.</p>
                        </div>
                        <div class="flex gap-3">
                            <button id="btn-matriz-add" class="hidden items-center px-3 py-2 bg-sky-50 text-sky-700 border border-sky-200 rounded hover:bg-sky-100 transition-colors text-sm font-medium">
                                <span class="material-icons-sharp text-base mr-1">add</span>
                                Nova disciplina
                            </button>
                            <button id="btn-matriz-edit" class="flex items-center px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition-colors text-sm font-medium">
                                <span class="material-icons-sharp text-base mr-2">edit</span>
                                <span id="btn-matriz-edit-text">Editar Matriz</span>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-4 text-sm text-gray-600">
                        <span class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded bg-blue-500"></span>Obrigatória</span>
                        <span class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded bg-green-500"></span>Optativa</span>
                        <span class="flex items-center gap-2"><span class="inline-block w-4 h-4 rounded bg-purple-500"></span>Eletiva</span>
                        <span class="flex items-center gap-2"><span class="inline-block w-8 h-0.5 bg-red-500"></span>Pré-requisito</span>
                        <span class="flex items-center gap-2"><span class="inline-block w-8 h-0.5 bg-yellow-500 fluxo-legenda-equivalencia"></span>Equivalência</span>
                    </div>

                    <div class="grid grid-cols-1 w-full">
                        <div id="matrix-board-container" class="w-full overflow-x-auto border border-gray-200 rounded-xl bg-gray-50 p-3 sm:p-4">
                            <div id="matrix-board" class="inline-flex gap-4 pb-4 min-w-full min-h-[20rem]">
                                <div class="m-auto flex flex-col items-center text-gray-400">
                                    <span class="material-icons-sharp text-3xl animate-spin mb-2">autorenew</span>
                                    <span>Carregando estrutura curricular...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="unlinked-area" class="mt-8 border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50/50">
                        <h4 class="flex items-center text-sm font-bold text-gray-700 uppercase mb-4">
                            <span class="material-icons-sharp mr-2 text-gray-400">inbox</span>
                            Disciplinas não vinculadas a períodos
                        </h4>
                        <div id="unlinked-list" class="drop-zone flex flex-wrap gap-3 min-h-[100px] p-2 rounded-lg transition-all" data-period-id="null"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Templates para Geração Dinâmica via JavaScript -->
<template id="template-fluxo-coluna">
    <div class="fluxo-periodo flex-shrink-0 w-64" data-periodo="">
        <div class="fluxo-periodo-header">
            <div>
                <h4 class="text-sm font-semibold text-gray-800" data-template="titulo"></h4>
                <p class="text-xs text-gray-500" data-template="subtitulo"></p>
            </div>
            <span class="inline-flex items-center justify-center text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-full px-2 py-0.5" data-template="contador"></span>
        </div>
        <div class="fluxo-periodo-list" data-template="lista"></div>
    </div>
</template>

<template id="template-fluxo-card">
    <div class="fluxo-card subject-card" data-componente-id="">
        <div class="flex items-start justify-between gap-3">
            <span class="fluxo-card-badge !uppercase" data-template="tipo-badge"></span>
            <span class="text-xs font-semibold text-gray-400 font-mono" data-template="codigo"></span>
        </div>  
        <div class="fluxo-card-titulo" data-template="nome"></div>
        <div class="fluxo-card-info">
            <span data-template="creditos"></span>
            <span data-template="carga-horaria"></span>
        </div>
    </div>
</template>

<template id="template-detalhes-componente-modal">
    <div id="modal-detalhes-componente" aria-hidden="true"
        class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/50 cursor-pointer" tabindex="-1" data-modal-esconder></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 sm:mx-auto flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detalhes do Componente</h3>
                <button class="text-gray-400 hover:text-gray-700 transition" data-modal-esconder aria-label="Fechar modal">
                    <span class="material-icons-sharp text-2xl">close</span>
                </button>
            </div>
            <div class="px-6 py-4 space-y-4 overflow-y-auto max-h-[70vh]" data-template="corpo">
                <!-- O conteúdo será inserido aqui -->
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                <button class="button-secondary" data-modal-esconder>Fechar</button>
            </div>
        </div>
    </div>
</template>

<template id="template-organizacao-coluna">
    <section class="w-64 flex-shrink-0 bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-4 shadow-sm">
        <header class="flex items-start justify-between gap-2 text-sm">
            <div>
                <h4 class="font-semibold text-gray-800" data-template="titulo"></h4>
                <p class="text-xs text-gray-500" data-template="subtitulo"></p>
            </div>
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full" data-template="contador"></span>
        </header>
        <div class="drop-zone flex flex-col gap-3 bg-gray-50/70 rounded-lg p-2 min-h-[120px] transition-all" data-period-id="" data-template="lista">
        </div>
    </section>
</template>

<template id="template-organizacao-card">
    <article class="matrix-card border border-gray-200 rounded-lg bg-white px-3 py-2 text-sm shadow-sm cursor-pointer select-none" data-componente-id="">
        <div class="flex items-start justify-between gap-2">
            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full uppercase" data-template="tipo-badge"></span>
            <span class="text-[11px] text-gray-400 font-mono" data-template="codigo"></span>
        </div>
        <div class="font-semibold text-gray-800 leading-5" data-template="nome"></div>
        <div class="flex justify-between text-xs text-gray-500">
            <span data-template="creditos"></span>
            <span data-template="carga-horaria"></span>
        </div>
        <div class="text-[11px] text-gray-400 truncate" title="" data-template="prerequisitos"></div>
    </article>
</template>

<template id="template-tabela-componentes-cabecalho-periodo">
    <tr class="bg-gray-50">
        <td colspan="6" class="px-6 py-3 text-xs font-semibold text-gray-500 tracking-wide uppercase" data-template="titulo"></td>
    </tr>
</template>

<template id="template-tabela-componentes-linha">
    <tr>
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900" data-template="nome"></div>
            <div class="text-xs text-gray-500" data-template="codigo"></div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" data-template="tipo"></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" data-template="creditos"></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" data-template="carga-horaria"></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-template="prerequisitos"></td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-template="acoes"></td>
    </tr>
</template>

<?php
    if ($permissoes['editar']) {
        include __DIR__ . '/../templates/matriz-modal-editar.php';
    }
    if ($permissoes['inativar']) {
        include __DIR__ . '/../templates/matriz-modal-inativar.php';
    }
    if ($permissoes['componente_cadastrar']) {
        include __DIR__ . '/../templates/componente-modal-adicionar.php';
    }
    if ($permissoes['componente_editar']) {
        include __DIR__ . '/../templates/componente-modal-editar.php';
    }
    if ($permissoes['componente_excluir']) {
        include __DIR__ . '/../templates/componente-modal-excluir.php';
    }
    if ($permissoes['componente_prerequisito']) {
        include __DIR__ . '/../templates/componente-modal-prerequisitos.php';
    }
    if ($permissoes['componente_equivalencia']) {
        include __DIR__ . '/../templates/componente-modal-equivalencias.php';
    }

    include __DIR__ . '/../templates/curso-modal-editar.php';
    include __DIR__ . '/../templates/curso-modal-arquivar.php';
?>

<script src="<?= obterURL('/assets/javascript/utils/notificador.js'); ?>" defer></script>
<script src="<?= obterURL('/assets/javascript/utils/formulario.js') ?>" defer></script>
<script src="<?= obterURL('/assets/javascript/utils/modal.js') ?>" defer></script>
<script src="<?= obterURL('/assets/javascript/matrizes-fluxograma.js') ?>" defer></script>
<script src="<?= obterURL('/assets/javascript/cursos.js') ?>" defer></script>
<script src="<?= obterURL('/assets/javascript/matrizes-visualizar.js') ?>" defer></script>