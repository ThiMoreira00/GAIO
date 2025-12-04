<template id="template-lista-item-aluno">
    <div class="aluno-item flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:border-sky-300 transition-colors" data-id="{{id}}">
        
        <!-- Checkbox para seleção -->
        <div class="flex-shrink-0">
            <input type="checkbox" class="aluno-checkbox w-4 h-4 text-sky-600 bg-gray-100 border-gray-300 rounded focus:ring-sky-500 focus:ring-2 cursor-pointer" data-aluno-id="{{id}}">
        </div>
        
        <!-- Foto do aluno -->
        <img class="aluno-foto w-12 h-12 rounded-full flex-shrink-0 object-cover" src="{{foto_perfil}}" alt="Foto de {{nome}}">
        
        <!-- Nome e Email -->
        <div class="flex-[2.5] min-w-0">
            <a href="<?= obterURL('/alunos/visualizar/{{id}}') ?>" class="aluno-nome aluno-id text-base font-semibold text-gray-900 hover:text-sky-600 hover:underline block truncate">{{nome}}</a>
            <p class="aluno-email flex align-center gap-2 text-sm text-gray-600 truncate">{{email}}</p>
        </div>
        
        <!-- Matrícula -->
        <div class="hidden sm:block sm:flex-2 flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Matrícula</p>
            <p class="aluno-matricula text-sm font-medium text-gray-900">{{matricula.matricula}}</p>
        </div>
        
        <!-- Status -->
        <div class="hidden sm:block flex-1 flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Situação</p>
            <span class="aluno-status text-xs font-bold inline-flex items-center px-3 py-1 rounded-full uppercase whitespace-nowrap">{{matricula.status.nome}}</span>
        </div>
        
        <!-- Menu de ações -->
        <div class="relative flex-shrink-0">
            <button type="button" class="inline-flex items-center p-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 rounded-md aluno-dropdown-trigger" id="menu-button-acoes" aria-label="Menu de ações">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>

            <!-- Menu de Ações -->
            <div id="menu-acoes-{{id}}" class="hidden absolute right-0 top-full z-[9999] mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-gray-400/70 py-1 aluno-dropdown-menu">
                <a href="<?= obterURL('/alunos/visualizar/{{id}}') ?>" class="aluno-id flex items-center gap-3 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors" data-action="visualizar">
                    <span class="material-icons-sharp !text-lg">visibility</span> Visualizar
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors" data-action="editar">
                    <span class="material-icons-sharp !text-lg">edit</span> Editar
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors" data-action="gerenciar-matriculas">
                    <span class="material-icons-sharp !text-lg">school</span> Gerenciar matrículas
                </a>
                <!-- Inativar -->
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-yellow-600 hover:bg-yellow-50 transition-colors" data-action="inativar">
                    <span class="material-icons-sharp !text-lg">block</span> Inativar
                </a>
                <!-- Reativar -->
                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-green-600 hover:bg-green-50 transition-colors" data-action="reativar">
                    <span class="material-icons-sharp !text-lg">autorenew</span> Reativar
                </a>
            </div>
        </div>
    </div>
</template>