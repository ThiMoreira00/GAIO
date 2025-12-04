<template id="template-lista-item-turma">
    <div class="turma-item flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:border-sky-300 transition-colors" data-id="{{id}}">
        
        <!-- Ícone -->
        <div class="w-10 h-10 rounded-full flex-shrink-0 flex bg-sky-600 items-center justify-center text-white">
            <span class="material-icons-sharp !text-lg">groups</span>
        </div>
        
        <!-- Nome da disciplina e código -->
        <div class="flex-[2.5] min-w-0">
            <a href="<?= obterURL('/turmas/{{id}}') ?>" class="turma-codigo text-base font-bold text-gray-900 hover:text-sky-600 hover:underline block truncate">{{codigo}} - {{disciplina_nome}}</a>
            <p class="turma-disciplina text-sm text-gray-600 truncate">Prof. {{professor_nome}}</p>
        </div>
        
        <!-- Período -->
        <div class="hidden sm:block flex-1 flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Período</p>
            <p class="turma-periodo text-sm font-medium text-gray-900 truncate">{{periodo_nome}}</p>
        </div>
        
        <!-- Ocupação -->
        <div class="hidden lg:block flex-1 flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Ocupação</p>
            <p class="text-sm font-medium text-gray-900">{{percentual_ocupacao}}% <span class="text-xs text-gray-500">({{vagas_ocupadas}}/{{capacidade_maxima}})</span></p>
        </div>
        
        <!-- Turno -->
        <div class="hidden xl:block flex-1 flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Turno</p>
            <span class="turma-turno text-xs font-semibold inline-block py-1 px-2.5 uppercase rounded-full bg-gray-100 text-gray-800">{{turno_valor}}</span>
        </div>
        
        <!-- Status -->
        <div class="hidden sm:block flex-1 flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Status</p>
            <span class="turma-status text-xs font-bold inline-flex items-center px-3 py-1 rounded-full uppercase whitespace-nowrap">{{status_valor}}</span>
        </div>
        
        <!-- Menu de ações -->
        <div class="relative flex-shrink-0">
            <button type="button" class="inline-flex items-center p-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 rounded-md turma-dropdown-trigger" id="menu-button-acoes-{{id}}" aria-label="Menu de acoes" onclick="toggleDropdownMenu('menu-acoes-{{id}}')">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>

            <!-- Menu de ações -->
            <div id="menu-acoes-{{id}}" class="hidden absolute right-0 top-full z-[9999] mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-gray-400/70 py-1 turma-dropdown-menu">
                <a href="<?= obterURL('/turmas/{{id}}') ?>" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors" data-action="visualizar">
                    <span class="material-icons-sharp !text-lg">visibility</span> Visualizar
                </a>
                <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-green-600 hover:bg-green-50 transition-colors border-t border-gray-100 my-1 turma-action-adicionar-alunos" data-action="adicionar_alunos">
                    <span class="material-icons-sharp !text-lg">person_add</span> Adicionar alunos
                </a>
                <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-green-600 hover:bg-green-50 transition-colors border-t border-gray-100 my-1 turma-action-remover-alunos" data-action="remover_alunos">
                    <span class="material-icons-sharp !text-lg">person_remove</span> Remover alunos
                </a>
                <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold transition-colors text-gray-700 hover:bg-gray-100" data-action="editar">
                    <span class="material-icons-sharp !text-lg">edit</span> Editar
                </a>
                <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold transition-colors text-gray-700 hover:bg-gray-100 turma-action-confirmar" data-action="confirmar">
                    <span class="material-icons-sharp !text-lg">check_circle</span> Confirmar
                </a>
                <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold transition-colors text-gray-700 hover:bg-gray-100 turma-action-liberar" data-action="liberar">
                    <span class="material-icons-sharp !text-lg">lock_open</span> Liberar
                </a>
                <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-green-600 hover:bg-green-50 transition-colors border-t border-gray-100 my-1 turma-action-finalizar" data-action="finalizar">
                    <span class="material-icons-sharp !text-lg">done_all</span> Finalizar
                </a>
                <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors border-t border-gray-100 my-1" data-action="arquivar">
                    <span class="material-icons-sharp !text-lg">archive</span> Arquivar
                </a>
            </div>
        </div>
    </div>
</template>