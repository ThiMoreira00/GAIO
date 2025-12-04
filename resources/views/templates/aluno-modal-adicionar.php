<template id="template-aluno-modal-adicionar">
    <div id="aluno-modal-adicionar" class="modal2 hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
        <div
            class="relative w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl max-h-[calc(100vh-4rem)] overflow-y-auto overflow-x-hidden">
            <div class="p-4">
                <!-- Cabeçalho do Modal -->
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                    <span class="material-icons-sharp text-blue-600">person_add</span>
                </div>
                <div class="mt-3 mb-8 text-center">
                    <h3 class="text-xl font-semibold leading-6 text-gray-800 modal-title">Adicionar novo aluno</h3>
                    <p class="mt-2 text-sm text-gray-600 modal-description">Preencha as informações do aluno para
                        registrá-lo no sistema.</p>
                </div>

                <!-- Botão Fechar -->
                <button type="button"
                    class="button-modal-fechar absolute right-4 top-4 text-gray-400 hover:text-gray-500">
                    <span class="material-icons-sharp">close</span>
                </button>

                <!-- Indicador de Progresso -->
                <div class="mb-8 px-4">
                    <div class="relative flex items-center justify-between">
                        <!-- Linha conectora de fundo (cinza) -->
                        <div class="absolute left-0 right-0 bg-gray-300" style="top: 5px; height: 2px; z-index: 0;">
                        </div>

                        <!-- Etapa 1 (Primeira - alinhada à esquerda) -->
                        <div class="indicador-etapa flex flex-col items-start flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-blue-600 border-blue-600 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-left leading-tight hidden sm:block">Básicas</span>
                        </div>
                        <!-- Etapa 2 -->
                        <div class="indicador-etapa flex flex-col items-center flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-white border-gray-300 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-center leading-tight hidden sm:block px-1">Pessoais</span>
                        </div>
                        <!-- Etapa 3 -->
                        <div class="indicador-etapa flex flex-col items-center flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-white border-gray-300 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-center leading-tight hidden sm:block px-1">Contato</span>
                        </div>
                        <!-- Etapa 4 -->
                        <div class="indicador-etapa flex flex-col items-center flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-white border-gray-300 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-center leading-tight hidden sm:block px-1">Escolares</span>
                        </div>
                        <!-- Etapa 5 -->
                        <div class="indicador-etapa flex flex-col items-center flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-white border-gray-300 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-center leading-tight hidden sm:block px-1">Filiação</span>
                        </div>
                        <!-- Etapa 6 -->
                        <div class="indicador-etapa flex flex-col items-center flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-white border-gray-300 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-center leading-tight hidden sm:block px-1">Documentos</span>
                        </div>
                        <!-- Etapa 7 -->
                        <div class="indicador-etapa flex flex-col items-center flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-white border-gray-300 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-center leading-tight hidden sm:block px-1">Matrículas</span>
                        </div>
                        <!-- Etapa 8 (Última - alinhada à direita) -->
                        <div class="indicador-etapa flex flex-col items-end flex-1 relative" style="z-index: 2;">
                            <div
                                class="ponto-etapa w-3 h-3 rounded-full border-2 bg-white border-gray-300 transition-colors duration-300">
                            </div>
                            <span
                                class="text-[10px] text-gray-500 mt-1.5 text-right leading-tight hidden sm:block">Confirmação</span>
                        </div>
                    </div>
                </div>

                <!-- Formulário -->
                <!-- enctype adicionado para suportar uploads de documentos -->
                <form id="aluno-form-adicionar" action="/alunos/adicionar" method="POST" enctype="multipart/form-data"
                    class="space-y-4">

                    <!-- Etapa 1: Informações Básicas -->
                    <div class="modal-etapa" data-etapa="1">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Informações básicas</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="nome_civil" class="form-label required">Nome civil</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">person</span>
                                    </div>
                                    <input type="text" name="nome_civil" id="nome_civil" required
                                        class="form-input pl-10" placeholder="Digite o nome civil">
                                </div>
                            </div>
                            <!-- Nome social -->
                            <div>
                                <div class="flex items-center gap-2">
                                    <label for="nome-social" class="form-label">
                                        Nome social
                                    </label>
                                    <div class="group relative flex items-center hover:cursor-help">
                                        <span class="material-icons-sharp text-gray-500">help</span>
                                        <div role="tooltip"
                                            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-none">
                                            Como o aluno prefere ser chamado(a). Este nome será usado em comunicações e
                                            no sistema.
                                            <div
                                                class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-gray-800">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">person</span>
                                    </div>
                                    <input type="text" name="nome_social" id="nome_social" class="form-input pl-10"
                                        placeholder="Digite o nome social">
                                </div>
                            </div>
                            <!-- Duas colunas: CPF e RG -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <label for="rg" class="form-label required">
                                            Documento de Identidade (RG)
                                        </label>
                                        <div class="group relative flex items-center hover:cursor-help">
                                            <span class="material-icons-sharp text-gray-500">help</span>
                                            <div role="tooltip"
                                                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-none">
                                                Registro Geral é um documento de identificação civil brasileiro, também
                                                conhecido como carteira de identidade. Caso possua o novo modelo (CIN),
                                                informe o número conforme o documento.
                                                <div
                                                    class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-gray-800">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">badge</span>
                                        </div>
                                        <input type="text" name="rg" id="rg" required class="form-input pl-10"
                                            maxlength="14" placeholder="__.___.___-_" oninput="formatarRG(this); document.querySelector('#documento_rg_numero').value = this.value;">
                                    </div>
                                </div>
                                <div>
                                    <label for="cpf" class="form-label required">CPF</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">badge</span>
                                        </div>
                                        <input type="text" name="cpf" id="cpf" required class="form-input pl-10"
                                            maxlength="14" placeholder="___.___.___-__" oninput="formatarCPF(this); document.querySelector('#documento_cpf_numero').value = this.value;">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="email_pessoal" class="form-label required">E-mail pessoal</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">email</span>
                                    </div>
                                    <input type="email" name="email_pessoal" id="email_pessoal" required
                                        class="form-input pl-10" placeholder="exemplo@email.com">
                                </div>
                            </div>
                            <div>
                                <label for="email_institucional" class="form-label">E-mail institucional</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">email</span>
                                    </div>
                                    <input type="email" name="email_institucional" id="email_institucional" class="form-input pl-10" placeholder="exemplo@edu.br">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 2: Dados pessoais: Sexo, cor/raça, data de nascimento, estado civil, nacionalidade, naturalidade, necessidades específicas -->
                    <div class="modal-etapa hidden" data-etapa="2">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Dados pessoais</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Linha 1: Sexo e Cor/Raça -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="sexo" class="form-label required">Sexo</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">transgender</span>
                                        </div>
                                        <?php
                                        if (isset($sexos) && !empty($sexos)): ?>
                                            <select name="sexo" id="sexo" required class="form-select pl-10 truncate">
                                                <option value="" disabled selected>Selecione o sexo</option>
                                                <?php foreach ($sexos as $sexo): ?>
                                                    <option value="<?= $sexo->name; ?>"><?= htmlspecialchars($sexo->value); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <label for="cor_raca" class="form-label required">Cor/Raça</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">diversity_3</span>
                                        </div>
                                        <?php
                                        if (isset($coresRaca) && !empty($coresRaca)): ?>
                                            <select name="cor_raca" id="cor_raca" required class="form-select pl-10 truncate">
                                                <option value="" disabled selected>Selecione a cor/raça</option>
                                                <?php foreach ($coresRaca as $corRaca): ?>
                                                    <option value="<?= $corRaca->name; ?>">
                                                        <?= htmlspecialchars($corRaca->value); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 2: Data de Nascimento e Estado Civil -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="data_nascimento" class="form-label required">Data de nascimento</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">calendar_month</span>
                                        </div>
                                        <input type="date" name="data_nascimento" id="data_nascimento" required
                                            class="form-input pl-10">
                                    </div>
                                </div>
                                <div>
                                    <label for="estado_civil" class="form-label required">Estado civil</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">favorite</span>
                                        </div>
                                        <?php if (isset($estadosCivis) && !empty($estadosCivis)): ?>
                                            <select name="estado_civil" id="estado_civil" required
                                                class="form-select pl-10 truncate">
                                                <option value="" disabled selected>Selecione o estado civil</option>
                                                <?php foreach ($estadosCivis as $estadoCivil): ?>
                                                    <option value="<?= $estadoCivil->name; ?>">
                                                        <?= htmlspecialchars($estadoCivil->value); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 3: Nacionalidade e Naturalidade -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nacionalidade" class="form-label required">Nacionalidade</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">public</span>
                                        </div>
                                        <?php if (isset($nacionalidades) && !empty($nacionalidades)): ?>
                                            <select name="nacionalidade" id="nacionalidade" required
                                                class="form-select pl-10 truncate">
                                                <option value="" disabled selected>Selecione a nacionalidade</option>
                                                <?php foreach ($nacionalidades as $nacionalidade): ?>
                                                    <option value="<?= $nacionalidade->name; ?>">
                                                        <?= htmlspecialchars($nacionalidade->value); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <label for="naturalidade" class="form-label required">Naturalidade</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">place</span>
                                        </div>
                                        <input type="text" name="naturalidade" id="naturalidade" required
                                            class="form-input pl-10" placeholder="Ex.: São Paulo/SP">
                                    </div>
                                </div>
                            </div>

                            <!-- Necessidades Específicas (Checkboxes) -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="form-label flex items-center gap-2">
                                        Necessidades específicas
                                    </label>
                                    <button type="button"
                                        onclick="document.getElementById('necessidades-container').classList.toggle('hidden')"
                                        class="text-xs text-sky-600 hover:text-sky-700 font-medium flex items-center gap-1">
                                        <span class="material-icons-sharp text-sm">expand_more</span>
                                        Expandir
                                    </button>
                                </div>
                                <div id="necessidades-container"
                                    class="hidden mt-2 border border-gray-200 rounded-lg p-3 bg-gray-50">
                                    <?php if (isset($necessidadesEspecificas) && !empty($necessidadesEspecificas)): ?>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto pr-2">
                                            <?php foreach ($necessidadesEspecificas as $necessidade): ?>
                                                <div
                                                    class="flex items-center gap-2 p-2 bg-white rounded border border-gray-100 hover:border-blue-200 transition-colors">
                                                    <input type="checkbox" name="necessidades_especificas[]"
                                                        id="necessidade_<?= $necessidade->name; ?>"
                                                        value="<?= $necessidade->name; ?>"
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                    <span
                                                        class="material-icons-sharp text-gray-400 text-sm">accessibility_new</span>
                                                    <label for="necessidade_<?= $necessidade->name; ?>"
                                                        class="text-xs text-gray-700 cursor-pointer flex-1">
                                                        <?= htmlspecialchars($necessidade->value); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <p class="text-xs mt-2 text-gray-600 italic">Marque todas as necessidades
                                            específicas que se aplicam ao aluno.</p>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-500 italic">Nenhuma necessidade específica cadastrada no
                                            sistema.</p>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs mt-1 text-gray-500">Clique em "Expandir" para selecionar as
                                    necessidades específicas do aluno.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 3: Dados de Contato -->
                    <div class="modal-etapa hidden" data-etapa="3">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Dados de contato</h4>
                        <div class="grid grid-cols-1 gap-4">

                            <div class="pb-3">
                                <!-- CEP com busca -->
                                <div class="mb-4">
                                    <label for="cep" class="form-label required">CEP</label>
                                    <div class="relative mt-1 flex gap-2">
                                        <div class="relative flex-1">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="material-icons-sharp text-gray-400">place</span>
                                            </div>
                                            <input type="text" name="cep" id="cep" required class="form-input pl-10"
                                                maxlength="9" placeholder="00000-000" oninput="formatarCEP(this)">
                                        </div>
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2"
                                            id="pesquisar-cep"
                                            onclick="buscarCEP(this.previousElementSibling.querySelector('input'))">
                                            <span class="material-icons-sharp text-gray-500">search</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Endereço e Número -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="md:col-span-2">
                                        <label for="endereco" class="form-label required">Endereço</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="material-icons-sharp text-gray-400">home</span>
                                            </div>
                                            <input type="text" name="endereco" id="endereco" required
                                                class="form-input pl-10" placeholder="Rua, Avenida, etc.">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="numero" class="form-label required">Número</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="material-icons-sharp text-gray-400">tag</span>
                                            </div>
                                            <input type="text" name="numero" id="numero" required
                                                class="form-input pl-10" placeholder="Nº">
                                        </div>
                                    </div>
                                </div>

                                <!-- Complemento e Bairro -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="material-icons-sharp text-gray-400">apartment</span>
                                            </div>
                                            <input type="text" name="complemento" id="complemento"
                                                class="form-input pl-10" placeholder="Apto, Bloco, etc.">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="bairro" class="form-label required">Bairro</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="material-icons-sharp text-gray-400">location_city</span>
                                            </div>
                                            <input type="text" name="bairro" id="bairro" required
                                                class="form-input pl-10" placeholder="Nome do bairro">
                                        </div>
                                    </div>
                                </div>

                                <!-- Cidade e UF -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label for="cidade" class="form-label required">Cidade</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="material-icons-sharp text-gray-400">location_city</span>
                                            </div>
                                            <input type="text" name="cidade" id="cidade" required
                                                class="form-input pl-10" placeholder="Nome da cidade">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="uf" class="form-label required">UF</label>
                                        <div class="relative mt-1">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="material-icons-sharp text-gray-400">map</span>
                                            </div>
                                            <?php if (isset($ufs) && !empty($ufs)): ?>
                                                <select name="uf" id="uf" required class="form-select pl-10 truncate">
                                                    <option value="" disabled selected>Selecione a UF</option>
                                                    <?php foreach ($ufs as $uf): ?>
                                                        <option value="<?= $uf->name; ?>"><?= htmlspecialchars($uf->value); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="telefone_fixo" class="form-label">Telefone fixo</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">phone</span>
                                    </div>
                                    <input type="tel" name="telefone_fixo" id="telefone_fixo" class="form-input pl-10"
                                        placeholder="(00) 0000-0000" pattern="\([0-9]{2}\) [0-9]{4}-[0-9]{4}"
                                        title="Formato: (00) 0000-0000" oninput="formatarTelefoneFixo(this)">
                                </div>
                            </div>
                            <div>
                                <label for="telefone_celular" class="form-label required">Telefone celular</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">smartphone</span>
                                    </div>
                                    <input type="tel" name="telefone_celular" id="telefone_celular" required
                                        class="form-input pl-10" placeholder="(00) 00000-0000"
                                        pattern="\([0-9]{2}\) [0-9]{5}-[0-9]{4}" title="Formato: (00) 00000-0000"
                                        oninput="formatarTelefoneCelular(this)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 4: Dados escolares -->
                    <div class="modal-etapa hidden" data-etapa="4">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Dados escolares</h4>
                        <div class="grid grid-cols-1 gap-4">

                            <!-- Nome da Instituição -->
                            <div>
                                <label for="nome_instituicao" class="form-label required">Nome da instituição</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">school</span>
                                    </div>
                                    <input type="text" name="nome_instituicao" id="nome_instituicao" required
                                        class="form-input pl-10" placeholder="Digite o nome da instituição">
                                </div>
                            </div>

                            <!-- Cidade e UF -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label for="cidade_instituicao" class="form-label required">Cidade</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">location_city</span>
                                        </div>
                                        <input type="text" name="cidade_instituicao" id="cidade_instituicao" required
                                            class="form-input pl-10" placeholder="Cidade da instituição">
                                    </div>
                                </div>
                                <div>
                                    <label for="uf_instituicao" class="form-label required">UF</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">map</span>
                                        </div>
                                        <?php if (isset($ufs) && !empty($ufs)): ?>
                                            <select name="uf_instituicao" id="uf_instituicao" required
                                                class="form-select pl-10 truncate">
                                                <option value="" disabled selected>Selecione a UF</option>
                                                <?php foreach ($ufs as $uf): ?>
                                                    <option value="<?= $uf->name; ?>"><?= htmlspecialchars($uf->value); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Ano de Conclusão e Modalidade -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="ano_conclusao" class="form-label required">Ano de conclusão</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">event</span>
                                        </div>
                                        <input type="number" name="ano_conclusao" id="ano_conclusao" required
                                            class="form-input pl-10" min="1900" max="2099" placeholder="Ex.: 2023">
                                    </div>
                                </div>
                                <div>
                                    <label for="nivel_ensino" class="form-label required">Nível de ensino</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">school</span>
                                        </div>
                                        <select name="nivel_ensino" id="nivel_ensino" required
                                            class="form-select pl-10 truncate">
                                            <option value="" disabled selected>Selecione o nível</option>
                                            <?php if (isset($escolaNiveis) && !empty($escolaNiveis)): ?>
                                                <?php foreach ($escolaNiveis as $escolaNivel): ?>
                                                    <option value="<?= $escolaNivel->name; ?>"><?= htmlspecialchars($escolaNivel->value); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 5: Responsáveis -->
                    <div class="modal-etapa hidden" data-etapa="5">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-lg font-semibold text-gray-700">Filiação</h4>
                        </div>
                        <div id="responsaveis-list" class="space-y-3 mt-4">
                            <?php
                            for ($i = 1; $i <= 3; $i++): // Inicia com 1 responsável por padrão ?>
                                <div
                                    class="responsavel-item grid grid-cols-1 gap-2 p-3 bg-white rounded border border-gray-100">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center gap-3">
                                            <span class="material-icons-sharp text-gray-500">badge</span>
                                            <strong class="text-sm text-gray-700">Responsável #<?= $i ?></strong>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-1">
                                        <div class="md:col-span-2">
                                            <label for="responsavel_<?= $i ?>_nome"
                                                class="form-label <?= $i === 1 ? 'required' : '' ?>">Nome completo</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">person</span>
                                                </div>
                                                <input type="text" id="responsavel_<?= $i ?>_nome" class="form-input pl-10"
                                                    name="responsaveis[<?= $i - 1 ?>][nome]"
                                                    placeholder="Nome do responsável" <?= $i === 1 ? 'required' : '' ?>>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="responsavel_<?= $i ?>_tipo"
                                                class="form-label <?= $i === 1 ? 'required' : '' ?>">Tipo (filiação)</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">group</span>
                                                </div>
                                                <?php
                                                if (isset($tiposResponsavel) && !empty($tiposResponsavel)): ?>
                                                    <select id="responsavel_<?= $i ?>_tipo" class="form-select pl-10 truncate"
                                                        name="responsaveis[<?= $i - 1 ?>][tipo]" <?= $i === 1 ? 'required' : '' ?>>
                                                        <option value="" disabled selected>Selecione o tipo</option>
                                                        <?php foreach ($tiposResponsavel as $tipo): ?>
                                                            <option value="<?= $tipo->name ?>">
                                                                <?= htmlspecialchars($tipo->value); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Etapa 6: Documentos (accordion) -->
                    <div class="modal-etapa hidden" data-etapa="6">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Documentos</h4>
                        <div class="space-y-2">
                            <!-- RG -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">badge</span>
                                        <span class="text-sm font-semibold text-gray-700">Registro Geral (RG)</span>
                                    </div>
                                    <span class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_less</span>
                                </button>
                                <div class="p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_rg_numero" class="form-label required">Número</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_rg_numero" type="text"
                                                    name="documentos[rg][numero]" class="form-input pl-10"
                                                    placeholder="Número do RG" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_rg_orgao_emissor" class="form-label required">Órgão
                                                emissor</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">business</span>
                                                </div>
                                                <input id="documento_rg_orgao_emissor" type="text"
                                                    name="documentos[rg][orgao_emissor]" class="form-input pl-10"
                                                    placeholder="Ex.: SSP, DETRAN" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_rg_data_emissao" class="form-label required">Data de emissão</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span
                                                        class="material-icons-sharp text-gray-400">calendar_today</span>
                                                </div>
                                                <input id="documento_rg_data_emissao" type="date"
                                                    name="documentos[rg][data_emissao]" class="form-input pl-10" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_rg_uf_emissor" class="form-label required">UF (emissor)</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">map</span>
                                                </div>
                                                <?php if (isset($ufs) && !empty($ufs)): ?>
                                                    <select id="documento_rg_uf_emissor" name="documentos[rg][uf_emissor]"
                                                        class="form-select pl-10 truncate">
                                                        <option value="" selected disabled>Selecione a UF</option>
                                                        <?php foreach ($ufs as $uf): ?>
                                                            <option value="<?= htmlspecialchars($uf->name); ?>">
                                                                <?= htmlspecialchars($uf->value); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input id="documento_rg_uf_emissor" type="text"
                                                        name="documentos[rg][uf_emissor]" class="form-input pl-10"
                                                        placeholder="UF" required>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CPF -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">fingerprint</span>
                                        <span class="text-sm font-semibold text-gray-700">CPF</span>
                                    </div>
                                    <span
                                        class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_less</span>
                                </button>
                                <div class="p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_cpf_numero" class="form-label required">CPF</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_cpf_numero" type="text" name="documentos[cpf][cpf]"
                                                    class="form-input pl-10" placeholder="___.___.___-__" required
                                                    oninput="formatarCPF(this)">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_cpf_proprio" class="form-label required">CPF próprio?</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">check_circle</span>
                                                </div>
                                                <select id="documento_cpf_proprio" name="documentos[cpf][cpf_proprio]"
                                                    class="form-select pl-10 truncate" required>
                                                    <option value="" selected disabled>Selecione</option>
                                                    <option value="1">Sim</option>
                                                    <option value="0">Não</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certidão de Nascimento -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">child_care</span>
                                        <span class="text-sm font-semibold text-gray-700">Certidão de Nascimento</span>
                                    </div>
                                    <span
                                        class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_more</span>
                                </button>
                                <div class="hidden p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_certidao_nascimento_numero"
                                                class="form-label">Número</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_certidao_nascimento_numero" type="text"
                                                    name="documentos[nascimento][numero]" class="form-input pl-10"
                                                    placeholder="Número">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_certidao_nascimento_uf" class="form-label">UF</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">map</span>
                                                </div>
                                                <?php if (isset($ufs) && !empty($ufs)): ?>
                                                    <select id="documento_certidao_nascimento_uf"
                                                        name="documentos[nascimento][uf]" class="form-select pl-10 truncate">
                                                        <option value="" selected disabled>Selecione a UF</option>
                                                        <?php foreach ($ufs as $uf): ?>
                                                            <option value="<?= htmlspecialchars($uf->name); ?>">
                                                                <?= htmlspecialchars($uf->value); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input id="documento_certidao_nascimento_uf" type="text"
                                                        name="documentos[nascimento][uf]" class="form-input pl-10"
                                                        placeholder="UF">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_certidao_nascimento_data_emissao"
                                                class="form-label">Data de emissão</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span
                                                        class="material-icons-sharp text-gray-400">calendar_today</span>
                                                </div>
                                                <input id="documento_certidao_nascimento_data_emissao" type="date"
                                                    name="documentos[nascimento][data_emissao]"
                                                    class="form-input pl-10">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certidão de Casamento -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">favorite</span>
                                        <span class="text-sm font-semibold text-gray-700">Certidão de Casamento</span>
                                    </div>
                                    <span
                                        class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_more</span>
                                </button>
                                <div class="hidden p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_certidao_casamento_numero"
                                                class="form-label">Número</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_certidao_casamento_numero" type="text"
                                                    name="documentos[casamento][numero]" class="form-input pl-10"
                                                    placeholder="Número">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_certidao_casamento_uf" class="form-label">UF</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">map</span>
                                                </div>
                                                <?php if (isset($ufs) && !empty($ufs)): ?>
                                                    <select id="documento_certidao_casamento_uf"
                                                        name="documentos[casamento][uf]" class="form-select pl-10 truncate">
                                                        <option value="" selected disabled>Selecione a UF</option>
                                                        <?php foreach ($ufs as $uf): ?>
                                                            <option value="<?= htmlspecialchars($uf->name); ?>">
                                                                <?= htmlspecialchars($uf->value); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input id="documento_certidao_casamento_uf" type="text"
                                                        name="documentos[casamento][uf]" class="form-input pl-10"
                                                        placeholder="UF">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_certidao_casamento_data_emissao"
                                                class="form-label">Data de emissão</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span
                                                        class="material-icons-sharp text-gray-400">calendar_today</span>
                                                </div>
                                                <input id="documento_certidao_casamento_data_emissao" type="date"
                                                    name="documentos[casamento][data_emissao]" class="form-input pl-10">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Carteira de Trabalho -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">work</span>
                                        <span class="text-sm font-semibold text-gray-700">Carteira de Trabalho</span>
                                    </div>
                                    <span
                                        class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_more</span>
                                </button>
                                <div class="hidden p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_carteira_trabalho_numero"
                                                class="form-label">Número</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_carteira_trabalho_numero" type="text"
                                                    name="documentos[carteira_trabalho][numero]"
                                                    class="form-input pl-10" placeholder="Número">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_carteira_trabalho_serie"
                                                class="form-label">Série</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span
                                                        class="material-icons-sharp text-gray-400">format_list_numbered</span>
                                                </div>
                                                <input id="documento_carteira_trabalho_serie" type="text"
                                                    name="documentos[carteira_trabalho][serie]" class="form-input pl-10"
                                                    placeholder="Série">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Título de Eleitor -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">how_to_vote</span>
                                        <span class="text-sm font-semibold text-gray-700">Título de Eleitor</span>
                                    </div>
                                    <span
                                        class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_more</span>
                                </button>
                                <div class="hidden p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_titulo_eleitor_numero"
                                                class="form-label">Número</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_titulo_eleitor_numero" type="text"
                                                    name="documentos[titulo_eleitor][numero]" class="form-input pl-10"
                                                    placeholder="Número do título">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_titulo_eleitor_zona" class="form-label">Zona</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">location_on</span>
                                                </div>
                                                <input id="documento_titulo_eleitor_zona" type="text"
                                                    name="documentos[titulo_eleitor][zona]" class="form-input pl-10"
                                                    placeholder="Zona eleitoral">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_titulo_eleitor_secao" class="form-label">Seção</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">numbers</span>
                                                </div>
                                                <input id="documento_titulo_eleitor_secao" type="text"
                                                    name="documentos[titulo_eleitor][secao]" class="form-input pl-10"
                                                    placeholder="Seção">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificado de Alistamento Militar -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">military_tech</span>
                                        <span class="text-sm font-semibold text-gray-700">Certificado de Alistamento
                                            Militar</span>
                                    </div>
                                    <span
                                        class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_more</span>
                                </button>
                                <div class="hidden p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_alistamento_militar_data"
                                                class="form-label">Data</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span
                                                        class="material-icons-sharp text-gray-400">calendar_today</span>
                                                </div>
                                                <input id="documento_alistamento_militar_data" type="date"
                                                    name="documentos[alistamento][data]" class="form-input pl-10">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_alistamento_militar_serie"
                                                class="form-label">Série</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span
                                                        class="material-icons-sharp text-gray-400">format_list_numbered</span>
                                                </div>
                                                <input id="documento_alistamento_militar_serie" type="text"
                                                    name="documentos[alistamento][serie]" class="form-input pl-10"
                                                    placeholder="Série">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_alistamento_militar_numero"
                                                class="form-label">Número</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_alistamento_militar_numero" type="text"
                                                    name="documentos[alistamento][numero]" class="form-input pl-10"
                                                    placeholder="Número">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Certificado de Reservista -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button"
                                    class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.icone-seta').classList.toggle('rotate-180')">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons-sharp text-blue-600">shield</span>
                                        <span class="text-sm font-semibold text-gray-700">Certificado de
                                            Reservista</span>
                                    </div>
                                    <span
                                        class="material-icons-sharp text-gray-400 icone-seta transition-transform duration-200">expand_more</span>
                                </button>
                                <div class="hidden p-4 bg-white border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label for="documento_reservista_regiao_militar" class="form-label">Região
                                                Militar</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">tag</span>
                                                </div>
                                                <input id="documento_reservista_regiao_militar" type="text"
                                                    name="documentos[reservista][rm]" class="form-input pl-10"
                                                    placeholder="Região Militar">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_reservista_categoria"
                                                class="form-label">Categoria</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">category</span>
                                                </div>
                                                <input id="documento_reservista_categoria" type="text"
                                                    name="documentos[reservista][cat]" class="form-input pl-10"
                                                    placeholder="Categoria">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_reservista_circunscricao"
                                                class="form-label">Circunscrição de Serviço Militar</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="material-icons-sharp text-gray-400">numbers</span>
                                                </div>
                                                <input id="documento_reservista_circunscricao" type="text"
                                                    name="documentos[reservista][csm]" class="form-input pl-10"
                                                    placeholder="CSM">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="documento_reservista_data_emissao" class="form-label">Data de
                                                emissão</label>
                                            <div class="relative mt-1">
                                                <div
                                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span
                                                        class="material-icons-sharp text-gray-400">calendar_today</span>
                                                </div>
                                                <input id="documento_reservista_data_emissao" type="date"
                                                    name="documentos[reservista][data]" class="form-input pl-10">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 7: Matrículas -->
                    <div class="modal-etapa hidden" data-etapa="7">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Nova matrícula</h4>
                        <div class="grid grid-cols-1 gap-4">

                            <div>
                                <label for="matricula_curso" class="form-label required">Curso</label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">school</span>
                                    </div>
                                    <?php if (isset($cursos) && !empty($cursos)): ?>
                                        <select name="matricula[curso_id]" id="matricula_curso" required
                                            class="form-select pl-10 truncate">
                                            <option value="" disabled selected>Selecione o curso</option>
                                            <?php foreach ($cursos as $curso): ?>
                                                <option value="<?= $curso->obterId(); ?>">
                                                    <?= $curso->obterSigla() ? htmlspecialchars($curso->obterSigla()) . ' - ' . htmlspecialchars($curso->obterNome()) : htmlspecialchars($curso->obterNome()); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" name="matricula[curso_id]" id="matricula_curso" required
                                            class="form-input pl-10" placeholder="Curso">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="matricula_numero" class="form-label required">Matrícula</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">confirmation_number</span>
                                        </div>
                                        <input type="text" name="matricula[numero]" id="matricula_numero" required
                                            class="form-input pl-10" placeholder="Ex.: 0123456789" pattern="[0-9]*"
                                            title="Apenas números são permitidos">
                                    </div>
                                </div>
                                <div>
                                    <label for="matricula_data" class="form-label required">Data da matrícula</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">calendar_today</span>
                                        </div>
                                        <input type="date" name="matricula[data]" id="matricula_data" required
                                            class="form-input pl-10">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="matricula_turno" class="form-label required">Turno de ingresso</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">schedule</span>
                                        </div>
                                        <?php
                                        if (isset($turnosIngresso) && !empty($turnosIngresso)):
                                            ?>
                                            <select name="matricula[turno]" id="matricula_turno" required
                                                class="form-select pl-10 truncate">
                                                <option value="" disabled selected>Selecione o turno</option>
                                                <?php foreach ($turnosIngresso as $turno): ?>
                                                    <option value="<?= htmlspecialchars($turno->name); ?>">
                                                        <?= htmlspecialchars($turno->value); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <label for="matricula_forma_ingresso" class="form-label required">Forma de
                                        ingresso</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">login</span>
                                        </div>
                                        <?php if (isset($tiposIngresso) && !empty($tiposIngresso)): ?>
                                            <select name="matricula[forma_ingresso]" id="matricula_forma_ingresso" required
                                                class="form-select pl-10 truncate">
                                                <option value="" disabled selected>Selecione a forma</option>
                                                <?php foreach ($tiposIngresso as $tipo): ?>
                                                    <option value="<?= $tipo->obterId(); ?>"><?= $tipo->obterNome(); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 4: Pontuação e Classificação -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="matricula_pontuacao" class="form-label">Pontuação</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">star</span>
                                        </div>
                                        <input type="text" name="matricula[pontuacao]" id="matricula_pontuacao"
                                            class="form-input pl-10" placeholder="Ex.: 85,5"
                                            pattern="[0-9]+([,][0-9]+)?" title="Use vírgula para decimais">
                                    </div>
                                </div>
                                <div>
                                    <label for="matricula_classificacao" class="form-label">Classificação</label>
                                    <div class="relative mt-1">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="material-icons-sharp text-gray-400">emoji_events</span>
                                        </div>
                                        <input type="number" name="matricula[classificacao]"
                                            id="matricula_classificacao" class="form-input pl-10" placeholder="Ex.: 1"
                                            min="1">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Etapa 8: Confirmação -->
                    <div class="modal-etapa hidden" data-etapa="8">
                        <h4 class="text-lg font-semibold text-gray-700 mb-4">Revisão e confirmação</h4>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <div class="flex items-start gap-2">
                                <span class="material-icons-sharp text-blue-600 text-xl">info</span>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-blue-800 !text-left">Revise todas as informações preenchidas</p>
                                    <p class="mt-2 text-sm text-blue-700 !text-left">Clique em "Voltar" para editar alguma informação ou em "Adicionar" para confirmar o cadastro.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            <!-- Seção 1: Informações Básicas -->
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                        <span class="material-icons-sharp text-blue-600 text-lg">person</span>
                                    </div>
                                    <h5 class="font-semibold text-gray-800">Informações básicas</h5>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-500 text-xs">Nome civil</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_nome_civil">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Nome social</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_nome_social">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">CPF</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_cpf">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">RG</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_rg">-</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="text-gray-500 text-xs">E-mail pessoal</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_email">-</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 2: Dados Pessoais -->
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                        <span class="material-icons-sharp text-blue-600 text-lg">badge</span>
                                    </div>
                                    <h5 class="font-semibold text-gray-800">Dados pessoais</h5>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-500 text-xs">Sexo</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_sexo">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Cor/Raça</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_cor_raca">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Data de nascimento</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_data_nascimento">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Estado civil</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_estado_civil">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Nacionalidade</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_nacionalidade">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Naturalidade</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_naturalidade">-</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 3: Dados de Contato -->
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                        <span class="material-icons-sharp text-blue-600 text-lg">contact_phone</span>
                                    </div>
                                    <h5 class="font-semibold text-gray-800">Dados de contato</h5>
                                </div>
                                <div class="space-y-3 text-sm">
                                    <div>
                                        <span class="text-gray-500 text-xs">Endereço completo</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_endereco_completo">-</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <span class="text-gray-500 text-xs">Telefone fixo</span>
                                            <p class="font-medium text-gray-800 mt-0.5" id="resumo_telefone_fixo">-</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 text-xs">Telefone celular</span>
                                            <p class="font-medium text-gray-800 mt-0.5" id="resumo_telefone_celular">-
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 4: Dados Escolares -->
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                        <span class="material-icons-sharp text-blue-600 text-lg">school</span>
                                    </div>
                                    <h5 class="font-semibold text-gray-800">Dados escolares</h5>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div class="md:col-span-2">
                                        <span class="text-gray-500 text-xs">Instituição</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_instituicao">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Ano de conclusão</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_ano_conclusao">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Nível de ensino</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_nivel_ensino">-</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 5: Filiação -->
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                        <span class="material-icons-sharp text-blue-600 text-lg">family_restroom</span>
                                    </div>
                                    <h5 class="font-semibold text-gray-800">Filiação</h5>
                                </div>
                                <div class="space-y-2 text-sm" id="resumo_responsaveis">
                                    <p class="text-gray-500 italic text-xs">Nenhum responsável informado</p>
                                </div>
                            </div>

                            <!-- Seção 6: Matrícula -->
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                        <span class="material-icons-sharp text-blue-600 text-lg">card_membership</span>
                                    </div>
                                    <h5 class="font-semibold text-gray-800">Matrícula</h5>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div class="md:col-span-2">
                                        <span class="text-gray-500 text-xs">Curso</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_curso">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Número da matrícula</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_matricula_numero">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Data da matrícula</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_matricula_data">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Turno de ingresso</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_turno">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Forma de ingresso</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_forma_ingresso">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Pontuação</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_pontuacao">-</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 text-xs">Classificação</span>
                                        <p class="font-medium text-gray-800 mt-0.5" id="resumo_classificacao">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Navegação -->
                    <div
                        class="mt-8 pt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between border-t border-gray-200">
                        <div class="flex gap-3">
                            <button type="button" class="btn-modal-voltar button-secondary hidden">
                                <span class="material-icons-sharp text-sm mr-1">arrow_back</span>
                                Voltar
                            </button>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" class="btn-modal-avancar button-primary">
                                Avançar
                                <span class="material-icons-sharp">arrow_forward</span>
                            </button>
                            <button type="button" class="btn-modal-finalizar button-primary hidden">
                                <span class="material-icons-sharp text-sm mr-1">check</span>
                                Adicionar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        /**
         * Carrega as matrizes curriculares disponíveis para o curso selecionado
         * @param {number} cursoId - ID do curso selecionado
         */


        // Colocar dentro do ready
        $(document).ready(function () {

            /**
             * Preenche o resumo com os dados do formulário
             * Esta função deve ser chamada quando o usuário chegar na etapa 8
             */
            function preencherResumo() {
                console.log('Preenchendo resumo da etapa 8...');

                // Helper para obter valor de campo ou retornar "-"
                const obterValor = (id) => {
                    const elemento = document.getElementById(id);
                    if (!elemento) {
                        console.warn(`Elemento não encontrado: ${id}`);
                        return '-';
                    }

                    if (elemento.tagName === 'SELECT') {
                        const selectedIndex = elemento.selectedIndex;
                        if (selectedIndex >= 0 && elemento.options[selectedIndex]) {
                            return elemento.options[selectedIndex].text || '-';
                        }
                        return '-';
                    }

                    const valor = elemento.value?.trim() || '-';
                    return valor;
                };

                // Helper para formatar data
                const formatarData = (data) => {
                    if (!data || data === '-') return '[não informado]';
                    const [ano, mes, dia] = data.split('-');
                    return `${dia}/${mes}/${ano}`;
                };

                // Helper para verificar se valor é válido
                const valorValido = (valor) => {
                    return valor && valor !== '-' && valor !== 'Selecione' && !valor.startsWith('Selecione');
                };

                // 1. Informações Básicas
                const nomeCivil = obterValor('nome_civil');
                const nomeSocial = obterValor('nome_social');
                const cpf = obterValor('cpf');
                const rg = obterValor('rg');
                const email = obterValor('email_pessoal');

                document.getElementById('resumo_nome_civil').textContent = valorValido(nomeCivil) ? nomeCivil : '[não informado]';
                document.getElementById('resumo_nome_social').textContent = valorValido(nomeSocial) ? nomeSocial : '[não informado]';
                document.getElementById('resumo_cpf').textContent = valorValido(cpf) ? cpf : '[não informado]';
                document.getElementById('resumo_rg').textContent = valorValido(rg) ? rg : '[não informado]';
                document.getElementById('resumo_email').textContent = valorValido(email) ? email : '[não informado]';

                // 2. Dados Pessoais
                const sexo = obterValor('sexo');
                const corRaca = obterValor('cor_raca');
                const estadoCivil = obterValor('estado_civil');
                const nacionalidade = obterValor('nacionalidade');
                const naturalidade = obterValor('naturalidade');

                document.getElementById('resumo_sexo').textContent = valorValido(sexo) ? sexo : '[não informado]';
                document.getElementById('resumo_cor_raca').textContent = valorValido(corRaca) ? corRaca : '[não informado]';
                document.getElementById('resumo_data_nascimento').textContent = formatarData(obterValor('data_nascimento'));
                document.getElementById('resumo_estado_civil').textContent = valorValido(estadoCivil) ? estadoCivil : '[não informado]';
                document.getElementById('resumo_nacionalidade').textContent = valorValido(nacionalidade) ? nacionalidade : '[não informado]';
                document.getElementById('resumo_naturalidade').textContent = valorValido(naturalidade) ? naturalidade : '[não informado]';

                // 3. Dados de Contato
                const endereco = obterValor('endereco');
                const numero = obterValor('numero');
                const complemento = obterValor('complemento');
                const bairro = obterValor('bairro');
                const cidade = obterValor('cidade');
                const uf = obterValor('uf');
                const cep = obterValor('cep');

                // Monta endereço apenas se os campos principais estiverem preenchidos
                if (valorValido(endereco) && valorValido(numero) && valorValido(bairro) && valorValido(cidade) && valorValido(uf) && valorValido(cep)) {
                    let enderecoCompleto = `${endereco}, ${numero}`;
                    if (valorValido(complemento)) {
                        enderecoCompleto += ` - ${complemento}`;
                    }
                    enderecoCompleto += ` - ${bairro}, ${cidade}/${uf} - CEP: ${cep}`;
                    document.getElementById('resumo_endereco_completo').textContent = enderecoCompleto;
                } else {
                    document.getElementById('resumo_endereco_completo').textContent = '[não informado]';
                }

                const telefoneFixo = obterValor('telefone_fixo');
                const telefoneCelular = obterValor('telefone_celular');

                document.getElementById('resumo_telefone_fixo').textContent = valorValido(telefoneFixo) ? telefoneFixo : '[não informado]';
                document.getElementById('resumo_telefone_celular').textContent = valorValido(telefoneCelular) ? telefoneCelular : '[não informado]';

                // 4. Dados Escolares
                const nomeInstituicao = obterValor('nome_instituicao');
                const cidadeInstituicao = obterValor('cidade_instituicao');
                const ufInstituicao = obterValor('uf_instituicao');

                if (valorValido(nomeInstituicao) && valorValido(cidadeInstituicao) && valorValido(ufInstituicao)) {
                    document.getElementById('resumo_instituicao').textContent = `${nomeInstituicao} - ${cidadeInstituicao}/${ufInstituicao}`;
                } else {
                    document.getElementById('resumo_instituicao').textContent = '[não informado]';
                }

                const anoConclusao = obterValor('ano_conclusao');
                const nivelEnsino = obterValor('nivel_ensino');

                document.getElementById('resumo_ano_conclusao').textContent = valorValido(anoConclusao) ? anoConclusao : '[não informado]';
                document.getElementById('resumo_nivel_ensino').textContent = valorValido(nivelEnsino) ? nivelEnsino : '[não informado]';

                // 5. Filiação (Responsáveis)
                const containerResponsaveis = document.getElementById('resumo_responsaveis');
                const responsaveis = [];

                for (let i = 1; i <= 3; i++) {
                    const nome = obterValor(`responsavel_${i}_nome`);
                    const tipo = obterValor(`responsavel_${i}_tipo`);

                    if (valorValido(nome) && valorValido(tipo)) {
                        responsaveis.push(`<div class="flex items-center gap-2">
                        <span class="material-icons-sharp text-gray-400 text-sm">person</span>
                        <span><strong>${tipo}:</strong> ${nome}</span>
                    </div>`);
                    }
                }

                if (responsaveis.length > 0) {
                    containerResponsaveis.innerHTML = responsaveis.join('');
                } else {
                    containerResponsaveis.innerHTML = '<p class="text-gray-500 italic text-xs">Nenhum responsável informado</p>';
                }

                // 6. Matrícula
                const curso = obterValor('matricula_curso');
                const matriculaNumero = obterValor('matricula_numero');
                const matriculaData = obterValor('matricula_data');
                const turno = obterValor('matricula_turno');
                const formaIngresso = obterValor('matricula_forma_ingresso');
                const pontuacao = obterValor('matricula_pontuacao');
                const classificacao = obterValor('matricula_classificacao');

                document.getElementById('resumo_curso').textContent = valorValido(curso) ? curso : '[não informado]';
                document.getElementById('resumo_matricula_numero').textContent = valorValido(matriculaNumero) ? matriculaNumero : '[não informado]';
                document.getElementById('resumo_matricula_data').textContent = formatarData(matriculaData);
                document.getElementById('resumo_turno').textContent = valorValido(turno) ? turno : '[não informado]';
                document.getElementById('resumo_forma_ingresso').textContent = valorValido(formaIngresso) ? formaIngresso : '[não informado]';
                document.getElementById('resumo_pontuacao').textContent = valorValido(pontuacao) ? pontuacao : '[não informado]';
                document.getElementById('resumo_classificacao').textContent = valorValido(classificacao) ? classificacao : '[não informado]';

                // console.log('Resumo preenchido com sucesso!');
            }

            // Função para configurar os listeners quando o modal for aberto
            function configurarResumoModal() {
                // Observer para detectar mudanças nas etapas
                const observarMudancaEtapa = () => {
                    const modal = document.getElementById('aluno-modal-adicionar');
                    if (!modal) return;

                    const etapaAtual = modal.querySelector('.modal-etapa:not(.hidden)');
                    if (etapaAtual && etapaAtual.getAttribute('data-etapa') === '8') {
                        // Pequeno delay para garantir que o DOM está pronto
                        setTimeout(() => {
                            preencherResumo();
                        }, 50);
                    }
                };

                // Monitora cliques nos botões de navegação usando event delegation
                document.addEventListener('click', function (e) {
                    if (e.target.closest('.btn-modal-avancar') || e.target.closest('.btn-modal-voltar')) {
                        setTimeout(observarMudancaEtapa, 150);
                    }
                });

                // Observa mudanças no DOM para detectar quando etapas mudam de visibilidade
                const modal = document.getElementById('aluno-modal-adicionar');
                if (modal) {
                    const observer = new MutationObserver(function (mutations) {
                        mutations.forEach(function (mutation) {
                            if (mutation.attributeName === 'class') {
                                observarMudancaEtapa();
                            }
                        });
                    });

                    // Observa todas as etapas
                    const etapas = modal.querySelectorAll('.modal-etapa');
                    etapas.forEach(etapa => {
                        observer.observe(etapa, { attributes: true });
                    });
                }
            }

            // Configura quando o DOM estiver pronto
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', configurarResumoModal);
            } else {
                configurarResumoModal();
            }

            // Também configura quando o template for clonado/inserido
            // (para garantir compatibilidade com modal2.js)
            setTimeout(configurarResumoModal, 500);


            debugPreencherDadosTodosFormulario();

            function debugPreencherDadosTodosFormulario() {
                // Função para preencher todos os campos do formulário com dados de teste
                console.log('Preenchendo formulário com dados de teste...');

                // Informações Básicas
                $('#nome_civil').val('João da Silva');
                $('#nome_social').val('Joãozinho');
                $('#cpf').val('123.456.789-00');
                $('#rg').val('12.345.678-9');
                $('#email_pessoal').val('joao.silva@example.com');
                $('#data_nascimento').val('2000-05-15');
                $('#sexo').val('masculino');
                $('#cor_raca').val('parda');

                // Dados de Contato
                $('#endereco').val('Rua das Flores');
                $('#numero').val('123');
                $('#complemento').val('Apto 45');
                $('#bairro').val('Jardim Primavera');
                $('#cidade').val('São Paulo');
                $('#uf').val('SP');
                $('#cep').val('01234-567');
                $('#telefone_fixo').val('(11) 2345-6789');
                $('#telefone_celular').val('(11) 91234-5678');
                // Dados Escolares
                $('#nome_instituicao').val('Escola Estadual ABC');
                $('#cidade_instituicao').val('São Paulo');
                $('#uf_instituicao').val('SP');
                $('#ano_conclusao').val('2018');
                $('#nivel_ensino').val('Ensino Médio');
                // Filiação
                $('#responsavel_1_nome').val('Maria da Silva');
                $('#responsavel_1_tipo').val('Mãe');
                $('#responsavel_2_nome').val('Carlos da Silva');
                $('#responsavel_2_tipo').val('Pai');
                // Matrícula
                $('#matricula_curso').val('1'); // Supondo que o ID do curso seja 1
                $('#matricula_numero').val('20230001');
                $('#matricula_data').val('2023-03-01');
                $('#matricula_turno').val('integral');
                $('#matricula_forma_ingresso').val('vestibular');
                $('#matricula_pontuacao').val('85,5');
                $('#matricula_classificacao').val('10');
            }
        });
    </script>
</template>