<section id="dados-pessoais-panel" class="tab-panel hidden" aria-labelledby="configuracoes-dados-pessoais-section-heading">
        <h2 id="configuracoes-dados-pessoais-section-heading" class="sr-only">Dados pessoais</h2>
        <article class="p-8 bg-white relative mb-4 overflow-x-auto sm:rounded-lg">
            <div class="bg-white p-4 sm:p-6">

                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800" id="informacoes-pessoais-titulo">Dados pessoais</h2>
                    <p class="mt-2 text-gray-600">Visualize e edite seus dados pessoais.</p>
                </div>

                <div class="flex bg-yellow-100 p-4 rounded-lg mb-6" role="alert">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.485 2.495c.646-1.113 2.384-1.113 3.03 0l6.28 10.875c.646 1.113-.273 2.505-1.515 2.505H3.72c-1.242 0-2.161-1.392-1.515-2.505l6.28-10.875ZM10 6a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Atenção!</h3>
                        <div class="mt-2 text-sm text-yellow-700">Caso algum dos seus dados pessoais esteja preenchido incorretamente, entre em contato com a administração para solicitar as devidas correções.</div>
                    </div>
                </div>

                <form id="formulario-dados-pessoais" action="#" method="POST" enctype="multipart/form-data" novalidate>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label for="nome-civil" class="form-label">
                                Nome civil
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">person</span>
                                </div>
                                <input type="text" name="nome-civil" id="nome-civil" required readonly aria-readonly="true" class="form-input pl-10 " placeholder="Seu nome civil" value="<?= $configuracoes['usuario']?->obterNomeCivil() ?? '[em branco]' ?>">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <label for="nome-social" class="form-label">
                                    Nome social
                                </label>
                                <div class="group relative flex items-center hover:cursor-help">
                                    <span class="material-icons-sharp text-gray-500">help</span>
                                    <div role="tooltip" class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-none">
                                        Como você prefere ser chamado(a). Este nome será usado em comunicações e na plataforma.
                                        <div class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">person</span>
                                </div>
                                <input type="text" name="nome-social" id="nome-social" class="form-input pl-10 " readonly aria-readonly="true" placeholder="Como gosta de ser chamado" value="<?= $configuracoes['usuario']?->obterNomeSocial() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <label for="cpf" class="form-label">
                                CPF
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">badge</span>
                                </div>
                                <input type="text" name="cpf" id="cpf" readonly aria-readonly="true" class="form-input pl-10" placeholder="000.000.000-00" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" maxlength="14" value="<?= $configuracoes['usuario']?->obterCPFFormatado() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <label for="rg" class="form-label">
                                    Documento de Identidade (RG)
                                </label>
                                <div class="group relative flex items-center hover:cursor-help">
                                    <span class="material-icons-sharp text-gray-500">help</span>
                                    <div role="tooltip" class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-none">
                                        Registro Geral é um documento de identificação civil brasileiro, também conhecido como carteira de identidade. Caso possua o novo modelo (CIN), informe o número conforme o documento.
                                        <div class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">badge</span>
                                </div>
                                <input type="text" name="rg" id="rg" class="form-input pl-10 " readonly aria-readonly="true" placeholder="00.000.000-0" value="<?= $configuracoes['usuario']?->obterRGFormatado() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <label for="sexo" class="form-label">
                                Sexo
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">wc</span>
                                </div>
                                <input name="sexo" id="sexo" class="form-input pl-10 bg-gray-100" aria-readonly="true" readonly value="<?= $configuracoes['usuario']?->obterSexo() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <label for="cor-raca" class="form-label">
                                Cor / Raça
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">diversity_3</span>
                                </div>
                                <input name="cor-raca" id="cor-raca" class="form-input pl-10 bg-gray-100" aria-readonly="true" readonly value="<?= $configuracoes['usuario']?->obterCorRaca() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <label for="data_nascimento" class="form-label">
                                Data de nascimento
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">calendar_month</span>
                                </div>
                                <input type="text" name="data_nascimento" id="data_nascimento" class="form-input pl-10 pr-[1rem] bg-gray-100" aria-readonly="true" readonly value="<?= $configuracoes['usuario']?->obterDataNascimentoFormatada() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <label for="estado-civil" class="form-label">
                                Estado civil
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">favorite</span>
                                </div>
                                <input name="estado-civil" id="estado-civil" class="form-input pl-10 bg-gray-100" readonly aria-readonly="true" value="<?= $configuracoes['usuario']?->obterEstadoCivil() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <label for="nacionalidade" class="form-label">
                                Nacionalidade
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">public</span>
                                </div>
                                <input type="text" name="nacionalidade" id="nacionalidade" class="form-input pl-10 bg-gray-100" readonly aria-readonly="true" value="<?= $configuracoes['usuario']?->obterNacionalidade() ?? '[em branco]' ?>">
                            </div>
                        </div>
                        <div>
                            <label for="naturalidade" class="form-label">
                                Naturalidade
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">place</span>
                                </div>
                                <input type="text" name="naturalidade" id="naturalidade" class="form-input pl-10 bg-gray-100" readonly aria-readonly="true" value="<?= $configuracoes['usuario']?->obterNaturalidade() ?? '[em branco]' ?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </article>
    </section>