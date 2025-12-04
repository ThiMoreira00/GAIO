<template id="template-lista-item-inscricao-turma">
    <div class="turma-item flex items-center gap-4 p-4 rounded-lg border transition-colors" data-id="{{id}}" data-ja-aprovado="{{ja_aprovado}}" data-tem-inscricao="{{tem_inscricao}}" data-inscricao-id="{{inscricao_id}}">
        
        <!-- Ícone -->
        <div class="turma-icone w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center text-white">
            <span class="material-icons-sharp !text-lg turma-icone-nome">school</span>
        </div>
        
        <!-- Nome da disciplina e código -->
        <div class="flex-[2.5] min-w-0">
            <p class="turma-disciplina text-base font-bold block truncate">{{sigla}} - {{disciplina}}</p>
            <p class="turma-professor text-sm truncate">{{professor}}</p>
        </div>
        
        <div class="hidden lg:block flex-1 flex-shrink-0">
            <p class="text-sm mb-0.5 turma-label-horario">Horário</p>
            <div class="turma-horario text-xs font-semibold" style="line-height: 1.6;">{{horario}}</div>
        </div>

        <!-- Turno -->
        <div class="hidden lg:block flex-1 flex-shrink-0">
            <p class="text-sm mb-0.5 turma-label-turno">Turno</p>
            <span class="turma-turno text-xs font-semibold inline-block py-1 px-2.5 uppercase rounded-full">{{turno}}</span>
        </div>

        <!-- Botão de ação -->
        <div class="flex-shrink-0 turma-acao-container">
            <button type="button" class="button-solicitar hidden items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition-colors" data-action="solicitar" data-turma-id="{{id}}">
                <span class="material-icons-sharp !text-base">add_circle</span>
                <span class="hidden sm:inline">Solicitar</span>
            </button>
            <button type="button" class="button-cancelar hidden items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors" data-action="cancelar" data-inscricao-id="{{inscricao_id}}">
                <span class="material-icons-sharp !text-base">cancel</span>
                <span class="hidden sm:inline">Cancelar</span>
            </button>
            <span class="turma-concluida hidden items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-500">
                <span class="material-icons-sharp !text-base">check_circle</span>
                <span class="hidden sm:inline">Concluída</span>
            </span>
        </div>
    </div>
</template>