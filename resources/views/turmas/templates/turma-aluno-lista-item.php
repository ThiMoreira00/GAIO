<template id="template-lista-item-turma-aluno">
    <div class="turma-item flex items-center gap-4 p-4 rounded-lg border border-gray-200 hover:border-sky-300 transition-colors" data-id="{{id}}">
        
        <!-- Ícone -->
        <div class="w-10 h-10 rounded-full flex-shrink-0 flex bg-sky-600 items-center justify-center text-white">
            <span class="material-icons-sharp !text-lg">groups</span>
        </div>
        
        <!-- Nome da disciplina e código -->
        <div class="flex-[2.5] min-w-0">
            <a href="<?= obterURL('/turmas/{{id}}') ?>" class="turma-codigo text-base font-bold text-gray-900 hover:text-sky-600 hover:underline block truncate">{{codigo}} - {{disciplina_nome}}</a>
            <p class="turma-disciplina text-sm text-gray-600 truncate">{{professor_nome}}</p>
        </div>
        
        <!-- Período -->
        <div class="hidden sm:block flex-1 flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Período</p>
            <p class="turma-periodo text-sm font-medium text-gray-900 truncate">{{periodo_nome}}</p>
        </div>
        
        <!-- Turno -->
        <div class="hidden xl:block flex-shrink-0">
            <p class="text-sm text-gray-500 mb-0.5">Turno</p>
            <span class="turma-turno text-xs font-semibold inline-block py-1 px-2.5 uppercase rounded-full bg-gray-100 text-gray-800">{{turno_valor}}</span>
        </div>
    </div>
</template>