<template id="template-oferta-lista-item">
    <article class="oferta-item flex items-center gap-4 px-4 sm:px-6 py-4 rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-sm transition-all" data-id="{{id}}" data-turma-id="{{id}}">
        <div class="w-10 h-10 rounded-full bg-sky-600 text-white flex items-center justify-center flex-shrink-0">
            <span class="material-icons-sharp !text-lg">menu_book</span>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 truncate">{{codigo}} - {{disciplina_nome}}</h3>
                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full uppercase bg-sky-100 text-sky-700 border border-sky-200 hidden sm:inline-flex">{{tipo_disciplina}}</span>
            </div>
            <p class="text-xs sm:text-sm text-gray-500 truncate">
                <span class="material-icons-sharp !text-xs align-middle">person</span>
                {{professor_nome}}
            </p>
            <div class="flex items-center gap-3 mt-2 text-xs text-gray-600">
                <span class="flex items-center gap-1">
                    <span class="material-icons-sharp !text-xs">schedule</span>
                    {{turno_valor}}
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-icons-sharp !text-xs">{{modalidade_icone}}</span>
                    {{modalidade_valor}}
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-icons-sharp !text-xs">people</span>
                    <span id="vagas-{{id}}">{{vagas_disponiveis}}/{{vagas_max}}</span>
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-shrink-0">
            <button type="button" 
                    class="button-primary btn-solicitar-inscricao px-3 py-2 rounded text-xs flex items-center gap-1" 
                    data-action="solicitar"
                    data-turma-id="{{id}}"
                    data-turma-codigo="{{codigo}}"
                    data-disciplina="{{disciplina_nome}}"
                    data-professor="{{professor_nome}}"
                    data-vagas="{{vagas_disponiveis}}/{{vagas_max}}"
                    {{#if solicitado}}disabled{{/if}}>
                <span class="material-icons-sharp !text-base">{{#if solicitado}}check_circle{{else}}add_circle{{/if}}</span>
                <span class="hidden sm:inline">{{#if solicitado}}Solicitado{{else}}Solicitar{{/if}}</span>
            </button>
            {{#if solicitado}}
            <button type="button" 
                    class="button-danger btn-cancelar-inscricao px-3 py-2 rounded text-xs flex items-center gap-1" 
                    data-action="cancelar"
                    data-inscricao-id="{{inscricao_id}}"
                    data-turma-id="{{id}}"
                    data-turma-codigo="{{codigo}}"
                    data-disciplina="{{disciplina_nome}}">
                <span class="material-icons-sharp !text-base">cancel</span>
                <span class="hidden sm:inline">Cancelar</span>
            </button>
            {{/if}}
        </div>
    </article>
</template>
