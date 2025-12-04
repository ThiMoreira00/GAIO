<?php

use App\Models\Usuario;
use App\Models\Enumerations\UsuarioSexo;

?>

<header>
    <h1 class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl mb-4">Configurações da conta</h1>
</header>

<main>
    <div class="bg-white px-2 py-2 mb-4">
        <div class="mx-auto">

            <div class="relative grid grid-cols-1 sm:hidden">
                <label for="tabs-mobile" class="sr-only">Selecione uma aba</label>
                <select id="tabs-mobile" name="tabs-mobile" class="w-full appearance-none rounded-md border-gray-300 bg-white py-2 pl-3 pr-8 text-base text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-sky-500">
                    <option value="conta-panel" selected>Conta</option>
<!--                    <option value="notificacoes-panel">Notificações</option>-->
                    <option value="dados-pessoais-panel">Dados pessoais</option>
                    <option value="contato-panel">Contato</option>
<!--                    <option value="documentos-panel">Documentos</option>-->
                </select>
                <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2">
                    <svg class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.53.22l3.5 3.5a.75.75 0 01-1.06 1.06L10 4.81 7.03 7.78a.75.75 0 01-1.06-1.06l3.5-3.5A.75.75 0 0110 3z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <div class="hidden sm:block">
                <nav id="tabs-desktop" class="flex space-x-4" aria-label="Tabs">
                    <a href="#" data-tab-target="conta-panel" class="tab-item rounded-md bg-sky-50 px-3 py-2 text-sm font-medium text-sky-700 min-w-32 text-center" aria-current="page">Conta</a>
                    <a href="#" data-tab-target="dados-pessoais-panel" class="tab-item rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 min-w-32 text-center">Dados pessoais</a>
                    <a href="#" data-tab-target="contato-panel" class="tab-item rounded-md px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 min-w-32 text-center">Contato</a>
                </nav>
            </div>

        </div>
    </div>

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

                        <img id="fotoPerfilImagem" src="<?= ($f = $configuracoes['usuario']?->obterCaminhoFoto()) ? $_ENV['SISTEMA_IMAGENS_PERFIL'] . $f : obterURL('/assets/img/usuario-padrao.png'); ?>" alt="Visualização da foto de perfil de <?= $configuracoes['usuario']?->obterNomeReduzido() ?? '[em branco]' ?>" class="w-24 h-24 rounded-full object-cover ring-2 ring-gray-200">

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
                                <input name="sexo" id="sexo" class="form-input pl-10 bg-gray-100" aria-readonly="true" readonly value="<?= $configuracoes['usuario']?->obterSexo()?->value ?? '[em branco]' ?>">
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
                                <input name="cor-raca" id="cor-raca" class="form-input pl-10 bg-gray-100" aria-readonly="true" readonly value="<?= $configuracoes['usuario']?->obterCorRaca()->value ?? '[em branco]' ?>">
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
                                <input name="estado-civil" id="estado-civil" class="form-input pl-10 bg-gray-100" readonly aria-readonly="true" value="<?= $configuracoes['usuario']?->obterEstadoCivil()->value ?? '[em branco]' ?>">
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
                                    $uf_usuario = $configuracoes['contato']?->obterUF()->name ?? '';

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
</main>

<script src="<?= obterURL('/assets/js/notificador-flash.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/js/formulario.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/js/contas/configuracoes-alterar-senha.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/js/contas/configuracoes-contato.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/js/configuracoes-foto.js'); ?>" type="application/javascript"></script>
<script src="<?= obterURL('/assets/js/sair-sem-salvar.js'); ?>" type="application/javascript"></script>
<script>
    // Instância para o formulário de Informações da Conta
    new Formulario({
        formId: 'formulario-informacoes-conta',
        notificador: true,
        onComplete: () => {
            const alteracoesNaoSalvas = document.querySelector('#formulario-informacoes-conta .alteracoes-nao-salvas');
            if (alteracoesNaoSalvas) {
                alteracoesNaoSalvas.remove();
            }
        }
    });

    // Instância para o formulário de Senha
    new Formulario({
        formId: 'formulario-senha',
        notificador: true,
        onComplete: () => {
            const alteracoesNaoSalvas = document.querySelector('#formulario-senha .alteracoes-nao-salvas');
            if (alteracoesNaoSalvas) {
                alteracoesNaoSalvas.remove();
            }
        }
    });

    // Instância para o formulário de Informações de Contato
    new Formulario ({
        formId: 'formulario-informacoes-contato',
        notificador: true,
        onComplete: () => {
            // Usando o seletor mais específico do seu código original
            const alteracoesNaoSalvas = document.querySelector('#formulario-informacoes-contato-rodape .alteracoes-nao-salvas');
            if (alteracoesNaoSalvas) {
                alteracoesNaoSalvas.remove();
            }
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const desktopTabs = document.querySelectorAll('#tabs-desktop .tab-item');
        const mobileTabsSelect = document.getElementById('tabs-mobile');
        const tabPanels = document.querySelectorAll('.tab-panel');

        function switchTab(targetId) {
            // Oculta todos os painéis de conteúdo
            tabPanels.forEach(panel => {
                panel.classList.add('hidden');
            });

            // Remove o estilo "ativo" de todas as abas do desktop
            desktopTabs.forEach(tab => {
                tab.classList.remove('bg-sky-50', 'text-sky-700');
                tab.classList.add('text-gray-500', 'hover:text-gray-700');
                tab.removeAttribute('aria-current');
            });

            // Mostra o painel de conteúdo alvo
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }

            // Aplica o estilo "ativo" na aba do desktop correspondente
            const activeTab = document.querySelector(`#tabs-desktop [data-tab-target="${targetId}"]`);
            if (activeTab) {
                activeTab.classList.add('bg-sky-50', 'text-sky-700');
                activeTab.classList.remove('text-gray-500', 'hover:text-gray-700');
                activeTab.setAttribute('aria-current', 'page');
            }

            if (mobileTabsSelect.value !== targetId) {
                mobileTabsSelect.value = targetId;
            }
        }

        // Adiciona o evento de clique para as abas do desktop
        desktopTabs.forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('data-tab-target');
                switchTab(targetId);
            });
        });

        // Adiciona o evento de mudança para o <select> do mobile
        mobileTabsSelect.addEventListener('change', function () {
            const targetId = this.value;
            switchTab(targetId);
        });
    });
</script>
<script>
    document.getElementById('cpf').addEventListener('input', function (e) {
        let value = e.target.value;

        // Remove tudo que não for número
        value = value.replace(/\D/g, '');

        // Aplica a máscara
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

        // Atualiza o valor no input
        e.target.value = value;
    });

    document.getElementById('telefone-fixo').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        let formattedValue = '';

        if (value.length > 0) {
            formattedValue = '(' + value.substring(0, 2);
        }
        if (value.length >= 3) {
            formattedValue += ') ' + value.substring(2, 6);
        }
        if (value.length >= 7) {
            formattedValue += '-' + value.substring(6, 10);
        }

        e.target.value = formattedValue;
    });

    document.getElementById('telefone-celular').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        let formattedValue = '';

        if (value.length > 0) {
            formattedValue = '(' + value.substring(0, 2);
        }
        if (value.length >= 3) {
            formattedValue += ') ' + value.substring(2, 7);
        }
        if (value.length >= 8) {
            formattedValue += '-' + value.substring(7, 11);
        }

        e.target.value = formattedValue;
    });
</script>