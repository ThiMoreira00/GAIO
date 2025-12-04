<template id="template-aluno-modal-importar-sisu">
    <div id="aluno-modal-importar-sisu" class="modal2 hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl max-h-[calc(100vh-4rem)] overflow-y-auto overflow-x-hidden">
            <div class="p-4">
                <!-- Cabeçalho do Modal -->
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                    <span class="material-icons-sharp text-green-600">upload_file</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800">Importar alunos via SISU</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Faça o upload da planilha com os dados dos alunos aprovados pelo SISU.
                    </p>
                </div>

                <!-- Botão Fechar -->
                <button type="button"
                    class="button-modal-fechar-sisu absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>

                <!-- Formulário -->
                <form id="aluno-form-importar-sisu" action="/alunos/importar-sisu" method="POST" enctype="multipart/form-data"
                    class="space-y-6">

                    <!-- Tipo de Importação -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Tipo de Importação
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <!-- Opção Parcial -->
                            <label class="flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-300 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="radio" id="tipo_parcial" name="tipo_importacao" value="parcial" required
                                    class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="ml-3 flex-1">
                                    <span class="block text-sm font-medium text-gray-900">Parcial - Importar apenas registros válidos</span>
                                    <span class="block text-xs text-gray-600 mt-1">Os alunos com erros serão ignorados e apenas os válidos serão importados.</span>
                                </div>
                            </label>

                            <!-- Opção Completa -->
                            <label class="flex items-start p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-300 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="radio" id="tipo_completa" name="tipo_importacao" value="completa" required
                                    class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <div class="ml-3 flex-1">
                                    <span class="block text-sm font-medium text-gray-900">Completa - Importar somente se todos estiverem corretos</span>
                                    <span class="block text-xs text-gray-600 mt-1">Se houver qualquer erro, nenhum aluno será importado.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Upload de Arquivo -->
                    <div>
                        <label for="arquivo_sisu" class="block text-sm font-medium text-gray-700 mb-2">
                            Planilha SISU
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" id="arquivo_sisu" name="arquivo_sisu" accept=".xlsx,.xls,.csv" required
                                class="hidden">
                            <label for="arquivo_sisu"
                                class="flex items-center justify-center w-full px-4 py-8 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-colors">
                                <div class="text-center">
                                    <span class="material-icons-sharp text-4xl text-gray-400 mb-2">cloud_upload</span>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium text-blue-600 hover:text-blue-700">Clique para selecionar</span>
                                        ou arraste o arquivo aqui
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Formatos aceitos: XLSX, XLS, CSV (máx. 10MB)
                                    </p>
                                </div>
                            </label>
                        </div>
                        <!-- Informações do arquivo selecionado -->
                        <div id="arquivo-info" class="hidden mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons-sharp text-blue-600 text-sm">insert_drive_file</span>
                                    <span id="arquivo-nome" class="text-sm text-gray-700 font-medium"></span>
                                    <span id="arquivo-tamanho" class="text-xs text-gray-500"></span>
                                </div>
                                <button type="button" id="remover-arquivo" class="text-red-500 hover:text-red-700">
                                    <span class="material-icons-sharp text-sm">close</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Adicionais -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <span class="material-icons-sharp text-yellow-600 text-sm mr-2">info</span>
                            <div class="text-xs text-gray-700">
                                <p class="font-medium mb-1">Formato da planilha SISU:</p>
                                <ul class="list-disc list-inside space-y-0.5 ml-2">
                                    <li>A primeira linha deve conter os cabeçalhos fornecidos pelo MEC/SISU</li>
                                    <li>Colunas obrigatórias: NO_INSCRITO, NU_CPF_INSCRITO, DS_EMAIL, DT_NASCIMENTO, DS_MATRICULA</li>
                                    <li>A planilha deve seguir o formato padrão do Sistema SISU</li>
                                    <li>CPF e Email não podem estar duplicados no sistema</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex justify-between items-center pt-4">
                        <div class="flex space-x-3">
                            <!-- Botão Cancelar -->
                            <button type="button"
                                class="button-modal-cancelar-sisu px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors">
                                Cancelar
                            </button>

                            <!-- Botão Importar -->
                            <button type="submit"
                                class="button-modal-confirmar-sisu px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="flex items-center">
                                    Importar alunos
                                </span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const modalSisu = $('#aluno-modal-importar-sisu');
            const formSisu = $('#aluno-form-importar-sisu');
            const inputArquivo = $('#arquivo_sisu');
            const arquivoInfo = $('#arquivo-info');
            const arquivoNome = $('#arquivo-nome');
            const arquivoTamanho = $('#arquivo-tamanho');
            const btnRemoverArquivo = $('#remover-arquivo');

            // Abrir modal
            window.abrirModalImportarSisu = function() {
                modalSisu.removeClass('hidden').addClass('flex');
                formSisu[0].reset();
                arquivoInfo.addClass('hidden');
            };

            // Fechar modal
            function fecharModalSisu() {
                modalSisu.addClass('hidden').removeClass('flex');
                formSisu[0].reset();
                arquivoInfo.addClass('hidden');
            }

            // Eventos de fechar
            $('.button-modal-fechar-sisu, .button-modal-cancelar-sisu').on('click', fecharModalSisu);

            // Fechar ao clicar fora do modal
            modalSisu.on('click', function(e) {
                if (e.target === this) {
                    fecharModalSisu();
                }
            });

            // Manipular seleção de arquivo
            inputArquivo.on('change', function(e) {
                const arquivo = e.target.files[0];
                
                if (arquivo) {
                    // Validar tamanho (máx 10MB)
                    const tamanhoMaxMB = 10;
                    const tamanhoMaxBytes = tamanhoMaxMB * 1024 * 1024;
                    
                    if (arquivo.size > tamanhoMaxBytes) {
                        alert(`O arquivo é muito grande. Tamanho máximo permitido: ${tamanhoMaxMB}MB`);
                        inputArquivo.val('');
                        arquivoInfo.addClass('hidden');
                        return;
                    }

                    // Validar extensão
                    const extensoesValidas = ['xlsx', 'xls', 'csv'];
                    const extensao = arquivo.name.split('.').pop().toLowerCase();
                    
                    if (!extensoesValidas.includes(extensao)) {
                        alert('Formato de arquivo inválido. Use XLSX, XLS ou CSV.');
                        inputArquivo.val('');
                        arquivoInfo.addClass('hidden');
                        return;
                    }

                    // Exibir informações do arquivo
                    arquivoNome.text(arquivo.name);
                    const tamanhoKB = (arquivo.size / 1024).toFixed(2);
                    arquivoTamanho.text(`(${tamanhoKB} KB)`);
                    arquivoInfo.removeClass('hidden');
                }
            });

            // Remover arquivo selecionado
            btnRemoverArquivo.on('click', function() {
                inputArquivo.val('');
                arquivoInfo.addClass('hidden');
            });

            // Drag and drop
            const dropZone = $('label[for="arquivo_sisu"]');

            dropZone.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('border-blue-500 bg-blue-50');
            });

            dropZone.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border-blue-500 bg-blue-50');
            });

            dropZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('border-blue-500 bg-blue-50');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    inputArquivo[0].files = files;
                    inputArquivo.trigger('change');
                }
            });

            // Submeter formulário
            formSisu.on('submit', function(e) {
                e.preventDefault();

                // Validações
                const tipoImportacao = $('input[name="tipo_importacao"]:checked').val();
                const arquivo = inputArquivo[0].files[0];

                if (!tipoImportacao) {
                    alert('Por favor, selecione o tipo de importação.');
                    return;
                }

                if (!arquivo) {
                    alert('Por favor, selecione um arquivo para importar.');
                    return;
                }

                // Confirmar importação
                const tipoTexto = tipoImportacao === 'parcial' 
                    ? 'parcial (apenas registros válidos)' 
                    : 'completa (todos ou nenhum)';
                
                if (!confirm(`Deseja confirmar a importação ${tipoTexto}?`)) {
                    return;
                }

                // Desabilitar botão durante o envio
                const btnSubmit = $('.button-modal-confirmar-sisu');
                btnSubmit.prop('disabled', true).html(`
                    <span class="flex items-center">
                        <span class="material-icons-sharp text-sm mr-1 animate-spin">sync</span>
                        Importando...
                    </span>
                `);

                // Criar FormData
                const formData = new FormData(this);

                // Enviar requisição AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'sucesso') {
                            alert(response.mensagem);
                            fecharModalSisu();
                            
                            // Recarregar a lista de alunos se existir
                            if (typeof carregarAlunos === 'function') {
                                carregarAlunos();
                            } else {
                                location.reload();
                            }
                        } else {
                            alert(response.mensagem || 'Erro ao importar alunos.');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        alert(response?.mensagem || 'Erro ao processar a importação. Tente novamente.');
                    },
                    complete: function() {
                        // Reabilitar botão
                        btnSubmit.prop('disabled', false).html(`
                            <span class="flex items-center">
                                <span class="material-icons-sharp text-sm mr-1">upload</span>
                                Importar Alunos
                            </span>
                        `);
                    }
                });
            });
        });
    </script>
</template>
