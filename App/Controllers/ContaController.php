<?php

/**
 * @file ContaController.php
 * @description Controlador responsável pelo gerenciamento das configurações da conta do usuário.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Enumerations\UF;
use App\Models\Usuario;
use App\Models\UsuarioContato;
use App\Services\AutenticacaoService;
use App\Services\ViaCepService;
use Exception;
use Random\RandomException;

/**
 * Classe ContaController
 *
 * Gerencia as configurações da conta do usuário
 *
 * @package App\Controllers
 * @extends Controller
 */
class ContaController extends Controller
{

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Renderiza a página de configurações da conta do usuário
     *
     * @return void
     * @throws RandomException
     */
    public function exibirConfiguracoes(): void
    {

        // Verificar se o usuário está autenticado
        $usuario = AutenticacaoService::usuarioAutenticado();

        if (!$usuario) {
            throw new Exception('Usuário não encontrado.');
        }

        // Busca os contatos (model UsuarioContatos)
        $contato = UsuarioContato::where('usuario_id', $usuario->obterId())->first();

        // Breadcrumbs = links de navegação
        $breadcrumbs = [
            ['label' => 'Configurações', 'url' => '/configuracoes']
        ];

        // Renderiza a página de configurações com os dados
        $this->renderizar('conta/configuracoes', [
            'titulo' => 'Configurações da conta',
            'breadcrumbs' => $breadcrumbs,
            'configuracoes' => [
                'usuario' => $usuario,
                'contato' => $contato
            ],
            'token_csrf' => $this->gerarTokenCSRF()
        ]);
    }

    /**
     * Salva as alterações na conta do usuário
     *
     * @param Request $request
     * @return void
     */
    public function salvarInformacoesPessoais(Request $request): void {

        try {

            // Validação do token CSRF
            $this->validarTokenCSRF($request);

            // Verifica se o usuário está autenticado
            $usuarioAutenticado = AutenticacaoService::usuarioAutenticado();

            if (!$usuarioAutenticado) {
                throw new Exception('Não foi possível identificar a sua conta. Tente novamente mais tarde.');
            }

            // Obtenção dos dados do formulário
            $imagemPerfil = $request->file('imagem-perfil');
            $emailPessoal = $request->post('email-pessoal');

            // Validação dos campos obrigatórios
            if (empty($emailPessoal)) {
                throw new Exception('Preencha os campos obrigatórios.');
            }

            // Validação do e-mail (verifica se é válido e se o domínio existe)
            if (!filter_var($emailPessoal, FILTER_VALIDATE_EMAIL) || !checkdnsrr(substr(strrchr($emailPessoal, "@"), 1), "MX")) {
                throw new Exception('O e-mail pessoal preenchido não é válido. Corrija-o e tente novamente.');
            }

            // Busca um usuário com o mesmo e-mail pessoal
            $usuarioPorEmailPessoal = Usuario::email($emailPessoal)->first();

            // Verifica se o e-mail pessoal já está registrado por outro usuário
            if ($usuarioPorEmailPessoal && $usuarioPorEmailPessoal->obterId() != $usuarioAutenticado->obterId()) {
                throw new Exception('O e-mail pessoal preenchido já está registrado por outro usuário. Tente novamente.');
            }

            // Atribui os novos dados e salvar
            $usuarioAutenticado->atribuirEmailPessoal($emailPessoal);

            // Verifica se há uma nova imagem de perfil
            if ($imagemPerfil && $imagemPerfil['error'] !== UPLOAD_ERR_NO_FILE) {

                // Verifica o tipo da imagem
                $tiposPermitidos = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];

                if (!in_array($request->obterTipoArquivo('imagem-perfil'), $tiposPermitidos)) {
                    throw new Exception('O arquivo de imagem deve ser um arquivo PNG, JPG, JPEG ou GIF.');
                }

                // Verifica o tamanho da imagem (máximo de 10MB)
                if ($request->obterTamanhoArquivo('imagem-perfil') > 10 * 1024 * 1024) {
                    throw new Exception('O arquivo de imagem deve ser menor que 10MB.');
                }

                // Obtém o caminho temporário da imagem
                $caminhoTemporario = $request->obterCaminhoTemporarioArquivo('imagem-perfil');
                $extensao = pathinfo($request->obterNomeArquivo('imagem-perfil'), PATHINFO_EXTENSION);

                // Gera um nome único para a imagem
                $novoNomeArquivo = uniqid() . '-' . $usuarioAutenticado->obterId() . '.' . $extensao;

                // Obtém o caminho final para salvar a imagem
                $caminhoDestino = $_ENV['SISTEMA_IMAGENS_PERFIL'] . $novoNomeArquivo;

                // Move o arquivo para o diretório de destino
                if (move_uploaded_file($caminhoTemporario, $caminhoDestino)) {

                    // Se o usuário já possuia uma imagem de perfil, exclua-a
                    if ($usuarioAutenticado->obterCaminhoFoto() && file_exists($_ENV['SISTEMA_IMAGENS_PERFIL'] . $usuarioAutenticado->obterCaminhoFoto())) {
                        unlink($_ENV['SISTEMA_IMAGENS_PERFIL'] . $usuarioAutenticado->obterCaminhoFoto());
                    }

                    // Atualiza a foto do usuário com o novo nome de arquivo
                    $usuarioAutenticado->atribuirCaminhoFoto($novoNomeArquivo);

                } else {

                    // Se o upload falhar, lança uma exceção
                    throw new Exception("Não foi possível salvar a imagem. Tente novamente mais tarde.");
                }
            }

            // Verifica se o usuário removeu a imagem de perfil
            if (($usuarioAutenticado->obterCaminhoFoto() != null) && $imagemPerfil && $imagemPerfil['error'] == UPLOAD_ERR_NO_FILE) {

                // Se o usuário possuia uma imagem de perfil, exclua-a
                if (file_exists($_ENV['SISTEMA_IMAGENS_PERFIL'] . $usuarioAutenticado->obterCaminhoFoto())) {
                    unlink($_ENV['SISTEMA_IMAGENS_PERFIL'] . $usuarioAutenticado->obterCaminhoFoto());
                }

                // Atualiza a foto do usuário com null (sem imagem)
                $usuarioAutenticado->atribuirCaminhoFoto(null);
            }

            // Salva as alterações
            $usuarioAutenticado->salvar();

            // Remove o token CSRF (para evitar ataques CSRF)
            $this->removerTokenCSRF();

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Informações atualizadas com sucesso.',
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao atualizar as informações.'
            ]);
        }
    }

    /**
     * Salva a alteração da senha do usuário
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function salvarSenha(Request $request): void {

        try {

            // Validação do token CSRF
            $this->validarTokenCSRF($request);

            // Verifica se o usuário está autenticado
            $usuarioAutenticado = AutenticacaoService::usuarioAutenticado();

            if (!$usuarioAutenticado) {
                throw new Exception('Não foi possível identificar a sua conta. Tente novamente mais tarde.');
            }

            // Obtenção dos dados do formulário
            $senhaAtual = $request->post('senha-atual');
            $senhaNova = $request->post('senha-nova');
            $senhaConfirmacao = $request->post('senha-confirmacao');

            // Validação dos campos obrigatórios
            if (!$senhaAtual || !$senhaNova || !$senhaConfirmacao) {
                throw new Exception('Preencha todos os campos obrigatórios.');
            }

            // Verifica se a senha nova é a mesma que a senha de confirmação
            if ($senhaNova != $senhaConfirmacao) {
                throw new Exception('As senhas não coincidem. Verifique-as e tente novamente.');
            }

            $loginUsuarioAutenticado = $usuarioAutenticado->login()->first();

            // Verifica se a senha atual é a que está registrada
            if (!$loginUsuarioAutenticado->verificarSenha($senhaAtual)) {
                echo json_encode(['status' => 'erro', 'mensagem' => 'Sua senha atual está incorreta. Verifique-a e tente novamente.']);
                exit;
            }

            // Verifica se a senha nova atende aos requisitos
            // 1. A senha deve possuir, pelo menos, 8 caracteres (e até 32)
            if (strlen($senhaNova) < 8 || strlen($senhaNova) > 32) {
                throw new Exception('A senha nova deve possuir, pelo menos, 8 caracteres (e até 32). Altere-a e tente novamente.');
            }

            // 2. A senha deve possuir uma letra minúscula e uma letra maiúscula
            if (is_numeric($senhaNova) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z]).+$/', $senhaNova)) {
                throw new Exception('A senha nova deve possuir uma letra minúscula e uma letra maiúscula. Altere-a e tente novamente.');
            }

            // 3. A senha deve possuir um caractere especial
            if (!preg_match('/[^a-zA-Z0-9]/', $senhaNova)) {
                throw new Exception('A senha nova deve possuir um caractere especial. Altere-a e tente novamente.');
            }

            // Verificar se a senha nova é a mesma que a senha atual
            if ($loginUsuarioAutenticado->verificarSenha($senhaNova)) {
                throw new Exception('A senha nova não pode ser igual à senha atual. Por favor, escolha uma senha diferente.');
            }

            // Altera a senha
            $loginUsuarioAutenticado->atribuirSenha($senhaNova);
            $loginUsuarioAutenticado->salvar();

            // Remove o token CSRF (para evitar ataques CSRF)
            $this->removerTokenCSRF();

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Senha atualizada com sucesso.'
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao atualizar a senha.'
            ]);

        }

    }

    /**
     * Salva as informações de contato do usuário
     *
     * @param Request $request
     * @return void
     */
    public function salvarInformacoesContato(Request $request): void
    {

        try {

            // Validação do token CSRF
            $this->validarTokenCSRF($request);

            // Verifica se o usuário está autenticado
            $usuarioAutenticado = AutenticacaoService::usuarioAutenticado();

            if (!$usuarioAutenticado) {
                throw new Exception('Não foi possível identificar a sua conta. Tente novamente mais tarde.');
            }

            // Obtenção dos dados do formulário
            $cep = str_replace(['.', '-'], '', $request->post('cep'));
            $endereco = $request->post('endereco');
            $numero = $request->post('numero');
            $complemento = $request->post('complemento');
            $bairro = $request->post('bairro');
            $cidade = $request->post('cidade');
            $uf = $request->post('uf');
            $telefone_fixo = $request->post('telefone-fixo');
            $telefone_celular = $request->post('telefone-celular');

            // Validação dos campos obrigatórios
            if (!$cep || !$endereco || !$numero || !$bairro || !$cidade || !$uf || !$telefone_fixo || !$telefone_celular) {
                throw new Exception('Preencha todos os campos obrigatórios.');
            }

            // Cria a instância do serviço de ViaCEP
            $viaCepService = new ViaCepService();
            $dadosEndereco = $viaCepService->buscarEndereco($cep);

            // Verifica se o CEP foi encontrado
            if (!$dadosEndereco) {
                throw new Exception('Não foi possível encontrar o CEP informado.');
            }

            // Remove formatação nos telefones (deixando apenas os números)
            $telefone_fixo = preg_replace('/[^0-9]/', '', $telefone_fixo);
            $telefone_celular = preg_replace('/[^0-9]/', '', $telefone_celular);

            // Verifica se os dados coincidem
            if (strtolower($dadosEndereco["logradouro"]) != strtolower($endereco)) {
                throw new Exception('O endereço informado não coincide com o CEP registrado. Você quis dizer: "' . $dadosEndereco["logradouro"] . '"? Verifique-o e tente novamente.');
            }

            if (strtolower($dadosEndereco["bairro"]) != strtolower($bairro)) {
                throw new Exception('O bairro informado não coincide com o CEP registrado. Você quis dizer: "' . $dadosEndereco["bairro"] . '"? Verifique-o e tente novamente.');
            }

            if (strtolower($dadosEndereco["cidade"]) != strtolower($cidade)) {
                throw new Exception('A cidade informada não coincide com o CEP registrado. Você quis dizer: "' . $dadosEndereco["cidade"] . '"? Verifique-a e tente novamente.');
            }

            if (strtolower($dadosEndereco["uf"]) != strtolower($uf)) {
                throw new Exception('A UF informada não coincide com o CEP registrado. Você quis dizer: "' . $dadosEndereco["uf"] . '"? Verifique-a e tente novamente.');
            }

            // Verifica se o telefone celular é válido (formato: (99) 99999-9999 ou variações)
            if (!preg_match('/^\(?\d{2}\)?[\s-]?\d{4,5}[\s-]?\d{4}$/', $telefone_celular)) {
                throw new Exception('O telefone celular informado não é válido.');
            }

            // Verifica se o telefone fixo é válido (formato: (99) 9999-9999 ou variações)
            if (!preg_match('/^\(?\d{2}\)?[\s-]?\d{4}[\s-]?\d{4}$/', $telefone_fixo)) {
                throw new Exception('O telefone fixo informado não é válido.');
            }

            // Verifica se a UF informada é válida
            if (!in_array($uf, array_map(fn($case) => $case->value, UF::cases()))) {
                throw new Exception('A UF informada não é válida.');
            }

            // Atualiza os dados do usuário
            $usuarioDadosContatos = $usuarioAutenticado->contato()->first();

            // Se não tiver, cria
            if (!$usuarioDadosContatos) {
                $usuarioDadosContatos = new UsuarioContato();
                $usuarioDadosContatos->atribuirUsuarioId($usuarioAutenticado->obterId());
            }
            $usuarioDadosContatos->atribuirCEP($cep);
            $usuarioDadosContatos->atribuirEndereco($endereco);
            $usuarioDadosContatos->atribuirNumero($numero);
            $usuarioDadosContatos->atribuirComplemento($complemento);
            $usuarioDadosContatos->atribuirBairro($bairro);
            $usuarioDadosContatos->atribuirCidade($cidade);
            $usuarioDadosContatos->atribuirUF(UF::from($uf));
            $usuarioDadosContatos->atribuirTelefoneFixo($telefone_fixo);
            $usuarioDadosContatos->atribuirTelefoneCelular($telefone_celular);
            $usuarioDadosContatos->salvar();

            // Remove o token CSRF (para evitar ataques CSRF)
            $this->removerTokenCSRF();

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Dados atualizados com sucesso.'
            ]);


        } catch (Exception $e) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $e->getMessage() ?? 'Erro ao atualizar os dados.'
            ]);
        }
    }
}