<section id="contato-panel" class="tab-panel hidden" aria-labelledby="configuracoes-contato-section-heading">
        <h2 id="configuracoes-contato-section-heading" class="sr-only">Contato</h2>
        <article class="p-8 bg-white relative mb-4 overflow-x-auto sm:rounded-lg">
            <div class="bg-white p-4 sm:p-6">

                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800" id="informacoes-pessoais-titulo">Contato</h2>
                    <p class="mt-2 text-gray-600">Atualize suas informações de contato.</p>
                </div>

                <form id="formulario-informacoes-contato" action="/configuracoes/contato" method="POST" enctype="multipart/form-data" novalidate>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="md:col-span-1">
                            <label for="cep" class="form-label">CEP <span class="text-red-500" aria-hidden="true">*</span></label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400 w-24">place</span>
                                </div>
                                <input type="text" name="cep" id="cep" required class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterCEPFormatado() ?? '' ?>">
                                <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2" id="pesquisar-cep">
                                    <span class="material-icons-sharp text-gray-500">search</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mt-6">
                        <div>
                            <label for="endereco" class="form-label">Endereço <span class="text-red-500" aria-hidden="true">*</span></label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">home</span>
                                </div>
                                <input type="text" name="endereco" id="endereco" required class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterEndereco() ?? '' ?>">
                            </div>
                        </div>

                        <div>
                            <label for="numero" class="form-label">Número <span class="text-red-500" aria-hidden="true">*</span></label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">numbers</span>
                                </div>
                                <input type="text" name="numero" id="numero" required class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterNumero() ?? '' ?>">
                            </div>
                        </div>

                        <div>
                            <label for="complemento" class="form-label">Complemento</label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">add</span>
                                </div>
                                <input type="text" name="complemento" id="complemento" class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterComplemento() ?? '' ?>">
                            </div>
                        </div>

                        <div>
                            <label for="bairro" class="form-label">Bairro <span class="text-red-500" aria-hidden="true">*</span></label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">location_city</span>
                                </div>
                                <input type="text" name="bairro" id="bairro" required class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterBairro() ?? '' ?>">
                            </div>
                        </div>

                        <div>
                            <label for="cidade" class="form-label">Cidade <span class="text-red-500" aria-hidden="true">*</span></label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">apartment</span>
                                </div>
                                <input type="text" name="cidade" id="cidade" required class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterCidade() ?? '' ?>">
                            </div>
                        </div>

                        <div>
                            <label for="uf" class="form-label">UF <span class="text-red-500" aria-hidden="true">*</span></label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">map</span>
                                </div>
                                <select name="uf" id="uf" required class="form-select pl-10 w-full">
                                    <?php
                                    // Obtém o UF do usuário ou define como nulo se não existir
                                    $uf_usuario = $configuracoes['contato']?->obterUF() ?? '';

                                    // Lista dos estados brasileiros
                                    $ufs = [
                                            'AC' => 'Acre',
                                            'AL' => 'Alagoas',
                                            'AP' => 'Amapá',
                                            'AM' => 'Amazonas',
                                            'BA' => 'Bahia',
                                            'CE' => 'Ceará',
                                            'DF' => 'Distrito Federal',
                                            'ES' => 'Espírito Santo',
                                            'GO' => 'Goiás',
                                            'MA' => 'Maranhão',
                                            'MT' => 'Mato Grosso',
                                            'MS' => 'Mato Grosso do Sul',
                                            'MG' => 'Minas Gerais',
                                            'PA' => 'Pará',
                                            'PB' => 'Paraíba',
                                            'PR' => 'Paraná',
                                            'PE' => 'Pernambuco',
                                            'PI' => 'Piauí',
                                            'RJ' => 'Rio de Janeiro',
                                            'RN' => 'Rio Grande do Norte',
                                            'RS' => 'Rio Grande do Sul',
                                            'RO' => 'Rondônia',
                                            'RR' => 'Roraima',
                                            'SC' => 'Santa Catarina',
                                            'SP' => 'São Paulo',
                                            'SE' => 'Sergipe',
                                            'TO' => 'Tocantins'
                                    ];

                                    echo '<option value="" disabled aria-disabled="true">Selecione um estado</option>';

                                    // Gera as opções dinamicamente
                                    foreach ($ufs as $sigla => $nome) {
                                        // Adiciona o atributo 'selected' se a sigla da UF for igual à do usuário
                                        $selected = ($sigla === $uf_usuario) ? 'selected' : '';
                                        echo "<option value=\"{$sigla}\" {$selected}>{$nome}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label for="telefone-fixo" class="form-label">Telefone fixo <span class="text-red-500" aria-hidden="true">*</span></label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">call</span>
                                    </div>
                                    <input type="text" name="telefone-fixo" id="telefone-fixo" required class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterTelefoneFixo() ?? '' ?>">
                                </div>
                            </div>

                            <div>
                                <label for="telefone-celular" class="form-label">Telefone celular <span class="text-red-500" aria-hidden="true">*</span></label>
                                <div class="relative mt-1">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="material-icons-sharp text-gray-400">smartphone</span>
                                    </div>
                                    <input type="text" name="telefone-celular" id="telefone-celular" required class="form-input pl-10 w-full" value="<?= $configuracoes['contato']?->obterTelefoneCelular() ?? '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="token_csrf" value="<?= htmlspecialchars($token_csrf) ?>">
                    <div class="mt-8 pt-4 border-t border-gray-200">
                        <div class="flex justify-end items-center" id="formulario-informacoes-contato-rodape">
                            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 border border-transparent rounded-md text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors">
                                Salvar alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </article>
    </section>