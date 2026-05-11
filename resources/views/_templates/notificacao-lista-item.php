<?php

/**
 * @file notificacao-lista-item.php
 * @description Template de item de notificação para exibição na lista de notificações do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 * 
 * Placeholders utilizados:
 * - {{id}}: ID da notificação
 * - {{mensagem}}: Mensagem da notificação (formatada em HTML)
 * - {{data_formatada}}: Data de registro formatada (ex: "10 Mar 2026")
 * - {{hora_formatada}}: Hora de registro formatada (ex: "14:30")
 * - {{cor}}: Cor do ícone (ex: "red-500", "blue-500")
 * - {{icone}}: Nome do ícone Material Icons (ex: "notifications", "event")
 */

?>
<template id="template-lista-item-notificacao">
    <div>
        <div class="flex flex-col sm:flex-row sm:items-center mb-4 relative z-10 w-full">
            <div class="text-left sm:text-right text-gray-500 text-sm sm:mr-6 mb-2 sm:mb-0 flex-shrink-0 sm:w-24 sm:h-10 sm:flex sm:items-center sm:justify-end">
                <p class="leading-tight">
                    {{data_formatada}}
                    <br class="hidden sm:block">
                    {{hora_formatada}}
                </p>
            </div>
            <div class="flex-1 flex items-center gap-4 w-full">
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-{{cor}} text-white">
                        <span class="material-icons-sharp !text-xl">{{icone}}</span>
                    </div>
                </div>
                <div id="detalhes-notificacao-{{id}}" class="flex-1 p-3 rounded-lg border border-gray-200 w-full self-center">
                    <p class="text-base break-words text-gray-700" id="mensagem-notificacao-{{id}}">{{mensagem}}</p>
                    <div class="notificacao-ler">
                        <button type="button" class="text-sky-500 hover:text-sky-700 text-xs font-medium underline">Marcar como lida</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>