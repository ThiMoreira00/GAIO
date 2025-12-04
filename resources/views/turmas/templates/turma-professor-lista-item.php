<template id="template-lista-item-turma-professor">
    <div class="turma-item bg-white border-2 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer" data-id="{{ id }}">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4 flex-1">
                <div class="turma-icone flex-shrink-0 w-12 h-12 bg-emerald-600 rounded-lg flex items-center justify-center">
                    <span class="material-icons-sharp text-white text-2xl">school</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <h3 class="turma-disciplina text-lg font-semibold truncate">{{ disciplina_nome }}</h3>
                        <span class="turma-status inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" data-status="{{ status }}">
                            {{ status_valor }}
                        </span>
                    </div>
                    <p class="turma-codigo text-sm text-gray-600 mb-2">Código: {{ codigo }}</p>
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <div class="flex items-center gap-1.5">
                            <span class="material-icons-sharp !text-base text-gray-400">calendar_today</span>
                            <span class="text-gray-600">{{ periodo_nome }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-icons-sharp !text-base text-gray-400">people</span>
                            <span class="text-gray-600">{{ vagas_ocupadas }} / {{ capacidade_maxima }} alunos</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-icons-sharp !text-base text-gray-400">schedule</span>
                            <span class="turma-turno text-xs font-medium px-2 py-1 rounded-md bg-gray-100 text-gray-800">{{ turno_valor }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 ml-4">
                <a href="/turmas/professor/{{ id }}" data-action="visualizar" class="button-primary inline-flex items-center gap-2 whitespace-nowrap">
                    <span class="material-icons-sharp">visibility</span>
                    Gerenciar
                </a>
            </div>
        </div>
    </div>
</template>

<style>
.turma-status[data-status="ATIVA"] {
    @apply bg-green-100 text-green-800;
}
.turma-status[data-status="CONFIRMADA"] {
    @apply bg-blue-100 text-blue-800;
}
.turma-status[data-status="OFERTADA"] {
    @apply bg-purple-100 text-purple-800;
}
.turma-status[data-status="PLANEJADA"] {
    @apply bg-yellow-100 text-yellow-800;
}
.turma-status[data-status="CONCLUIDA"] {
    @apply bg-gray-100 text-gray-800;
}
.turma-status[data-status="ARQUIVADA"] {
    @apply bg-gray-200 text-gray-600;
}
</style>
