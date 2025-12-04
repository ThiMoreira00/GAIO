<section id="conta-panel" class="tab-panel border-b border-gray-200 min-h-max" aria-labelledby="configuracoes-conta-section-heading">
    <h2 id="configuracoes-conta-section-heading" class="sr-only">Configurações da conta</h2>
    <article class="p-8 bg-white relative mb-4 overflow-x-auto sm:rounded-lg">
        <div class="bg-white p-4 sm:p-6">

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-800" id="informacoes-pessoais-titulo">Informações pessoais</h2>
                <p class="mt-2 text-gray-600">Atualize os dados principais da sua conta de usuário.</p>
            </div>

            <form id="formulario-informacoes-conta" action="/configuracoes/informacoes-pessoais" method="POST" enctype="multipart/form-data" novalidate>
                <label for="imagem-perfil" class="form-label mb-3">
                    Foto de perfil
                </label>
                <div class="flex items-center gap-5 mb-10">

                    <img id="fotoPerfilImagem" src="<?= ($f = $configuracoes['usuario']?->obterCaminhoFoto()) ? $_ENV['SISTEMA_IMAGENS_PERFIL'] . $f : obterURL('/assets/img/usuario-padrao.png'); ?>" alt="Visualização da foto de perfil." class="w-24 h-24 rounded-full object-cover ring-2 ring-gray-200">

                    <div>
                        <input type="file" id="fotoPerfilInput" name="imagem-perfil" class="hidden" accept="image/png, image/jpeg, image/gif" aria-labelledby="informacoes-pessoais-titulo">

                        <button id="alterarFotoButton" type="button" class="px-4 py-2 text-sm font-medium text-gray-800 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            Alterar
                        </button>

                        <button id="removerFotoButton" type="button" class="ml-2 px-4 py-2 text-sm font-medium text-gray-600 hover:text-red-600 focus:outline-none hidden">
                            Remover
                        </button>

                        <p class="text-xs text-gray-500 mt-2">
                            PNG, JPG, GIF até 10MB
                        </p>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <label for="nome-civil" class="form-label">
                            Nome civil <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="material-icons-sharp text-gray-400">person</span>
                            </div>
                            <input type="text" name="nome-civil" id="nome-civil" required readonly aria-readonly="true" class="form-input pl-10 " placeholder="Seu nome civil" value="<?= $configuracoes['usuario']?->obterNomeCivil() ?? '[em branco]';?>">
                        </div>
                        <p class="text-xs mt-1 text-gray-600">Para alterar o seu nome civil, entre em contato com a administração.</p>
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <label for="nome-social" class="form-label">
                                Nome social
                            </label>
                            <div class="group relative flex items-center hover:cursor-help">
                                <span class="material-icons-sharp text-gray-500">help</span>
                                <div role="tooltip" class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-none">
                                    Como você prefere ser chamado(a). Este nome será usado em comunicações e no sistema.
                                    <div class="absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-x-8 border-x-transparent border-t-8 border-t-gray-800"></div>
                                </div>
                            </div>
                        </div>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="material-icons-sharp text-gray-400">person</span>
                            </div>
                            <input type="text" name="nome-social" id="nome-social" class="form-input pl-10 " readonly aria-readonly="true" placeholder="Como gosta de ser chamado" value="<?= $configuracoes['usuario']?->obterNomeSocial() ?? '[em branco]'; ?>">
                        </div>
                        <p class="text-xs mt-1 text-gray-600">Para alterar o seu nome social, entre em contato com a administração.</p>
                    </div>

                    <div>
                        <label for="email-pessoal" class="form-label">
                            E-mail pessoal <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="material-icons-sharp text-gray-400">email</span>
                            </div>
                            <input type="email" name="email-pessoal" id="email-pessoal" required class="form-input pl-10" placeholder="seu.email@exemplo.com" value="<?= $configuracoes['usuario']?->obterEmailPessoal() ?? '[em branco]'; ?>">
                        </div>
                    </div>

                    <div>
                        <label for="email-institucional" class="form-label">
                            E-mail institucional
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="material-icons-sharp text-gray-400">domain</span>
                            </div>
                            <input type="email" name="email-institucional" id="email-institucional" readonly class="form-input pl-10 " placeholder="aluno@instituicao.edu.br" value="<?= $configuracoes['usuario']?->obterEmailInstitucional() ?? '[em branco]'; ?>">
                        </div>
                        <p class="text-xs mt-1 text-gray-600">Para alterar o seu e-mail institucional, entre em contato com a administração.</p>
                    </div>

                </div>
                <input type="hidden" name="token_csrf" value="<?= htmlspecialchars($token_csrf) ?>">
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <div class="flex justify-end items-center">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 border border-transparent rounded-md text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors">
                            Salvar alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </article>
    <article class="p-8 bg-white relative overflow-x-auto sm:rounded-lg">
        <div class="p-4 sm:p-6">

            <h2 class="text-xl font-bold text-gray-900 mb-8" id="alterar-senha-titulo">
                Senha
            </h2>

            <form action="/configuracoes/senha" method="POST" id="formulario-senha" enctype="multipart/form-data" novalidate>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

                    <div class="flex flex-col space-y-6">

                        <div>
                            <label for="senha-atual" class="form-label">
                                Senha atual:
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">lock</span>
                                </div>
                                <input type="password" name="senha-atual" id="senha-atual" required class="form-input pl-10 pr-12" placeholder="Digite sua senha atual">
                                <div class="absolute inset-y-0 right-0 flex items-center">
                                    <button type="button" class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Mostrar ou esconder a senha">
                                        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="senha-nova" class="form-label">
                                Nova senha:
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">lock</span>
                                </div>
                                <input type="password" name="senha-nova" id="senha-nova" required class="form-input pl-10 pr-12" placeholder="Digite sua nova senha">
                                <div class="absolute inset-y-0 right-0 flex items-center">
                                    <button type="button" class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Mostrar ou esconder a senha">
                                        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="senha-confirmacao" class="form-label">
                                Confirmar nova senha:
                            </label>
                            <div class="relative mt-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="material-icons-sharp text-gray-400">lock</span>
                                </div>
                                <input type="password" name="senha-confirmacao" id="senha-confirmacao" required class="form-input pl-10 pr-12" placeholder="Confirme a sua nova senha">
                                <div class="absolute inset-y-0 right-0 flex items-center">
                                    <button type="button" class="js-password-toggle p-2 rounded-md hover:focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Mostrar ou esconder a senha">
                                        <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4C7 4 2.73 7.11 1 11.5 2.73 15.89 7 19 12 19s9.27-3.11 11-7.5C21.27 7.11 17 4 12 4zm0 12.5c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                    </button>
                                </div>
                            </div>
                            <p id="password-match-msg" class="text-xs mt-2"></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg h-fit">
                        <h3 class="text-md font-semibold text-gray-800">Requisitos de senha</h3>
                        <p class="text-sm text-gray-600 mt-2 mb-4">Certifique-se de que estes requisitos sejam atendidos:</p>

                        <ul class="space-y-3">
                            <li id="req-length" class="flex items-center text-sm text-gray-500">
                                <span class="material-icons-sharp mr-2 flex-shrink-0 transition-colors duration-300">check_circle</span>
                                <span class="transition-colors duration-300">Pelo menos 8 caracteres (e até 32)</span>
                            </li>
                            <li id="req-case" class="flex items-center text-sm text-gray-500">
                                <span class="material-icons-sharp mr-2 flex-shrink-0 transition-colors duration-300">check_circle</span>
                                <span class="transition-colors duration-300">Pelo menos uma letra minúscula e uma maiúscula</span>
                            </li>
                            <li id="req-special" class="flex items-center text-sm text-gray-500">
                                <span class="material-icons-sharp mr-2 flex-shrink-0 transition-colors duration-300">check_circle</span>
                                <span class="transition-colors duration-300">Inclusão de pelo menos um caractere especial (!@#?)</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="token_csrf" value="<?= htmlspecialchars($token_csrf) ?>">
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <div class="flex justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 border border-transparent rounded-md text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors">
                            Alterar senha
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </article>
</section>