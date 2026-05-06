<?php

/**
 * @file AuthController.php
 * @description Controlador responsável pela autenticação e gestão de sessões de usuários
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Controllers;

// Importação de classes
use App\Core\Controller;
use App\Core\Request;
use App\Models\Enumerations\EmailTipo;
use App\Models\Usuario;
use App\Models\UsuarioLogin;
use App\Models\UsuarioToken;
use App\Services\AuthService;
use App\Services\EmailService;
use App\Services\NotificacaoService;
use Exception;
use Random\RandomException;

/**
 * Classe AuthController
 *
 * Gerencia autenticação, registro e recuperação de conta dos usuários.
 * Implementa medidas de segurança contra CSRF e session fixation.
 *
 * @package App\Controllers
 * @extends Controller
 */
class AuthController extends Controller
{

    // --- ATRIBUTOS ---
    /**
     * Instância do serviço de autenticação
     * @var AuthService
     */
    private AuthService $authService;


    // --- MÉTODOS ---

    /**
     * Construtor da classe
     *
     */
    public function __construct()
    {
        $this->authService = new AuthService();
    }

    // --- MÉTODOS DE VISUALIZAÇÃO ---

    /**
     * Exibe a página de login
     *
     * @return void
     * @throws RandomException
     */
    public function exibirLogin(): void
    {

        // Verifica se o usuário já está autenticado
        if ($this->authService->estaAutenticado()) {
            $this->redirecionar('/inicio');
        }

        // Renderiza a página de login
        $this->renderizar('auth/login', [
            'token_csrf' => $this->gerarTokenCSRF()
        ], null);
    }

    /**
     * Exibe a página de "Esqueci minha senha"
     *
     * @return void
     * @throws RandomException (Gerador de token CSRF)
     */
    public function exibirEsqueciSenha(): void {

        // Verifica se o usuário já está autenticado
        if ($this->authService->estaAutenticado()) {
            $this->redirecionar('/inicio');
        }

        // Renderiza a página de "Esqueci minha senha"
        $this->renderizar('auth/esqueci-senha', [
            'token_csrf' => $this->gerarTokenCSRF()
        ], null);
    }

    /**
     * Exibe a página de "Redefinir senha"
     *
     * @param string $token_redefinicao
     * @return void
     * @throws RandomException (Gerador de token CSRF)
     * @throws Exception (Se o token não for válido)
     */
    public function exibirRedefinicaoSenha(string $token_redefinicao): void {

        try {

            // Verifica se o usuário já está autenticado
            if ($this->authService->estaAutenticado()) {
                $this->redirecionar('/inicio');
            }

            // Verifica se o token é válido e se o usuário existe
            $usuarioTokenRedefinicao = UsuarioToken::tokenRedefinicao($token_redefinicao, true)->first();

            // Verifica se o token existe
            if (!$usuarioTokenRedefinicao || $usuarioTokenRedefinicao->verificarExpirado()) {
                throw new Exception('Token de redefinição de senha inválido.');
            }

            // Renderiza a página de redefinição de senha
            $this->renderizar('auth/redefinir-senha', [
                'token_csrf' => $this->gerarTokenCSRF(),
                'token_redefinicao' => $token_redefinicao
            ], null);

        } catch (Exception $exception) {
            http_response_code(400);
            $this->renderizar('erros/erro-400', ['mensagem' => $exception->getMessage() ?? 'Erro ao tentar redefinir sua senha. Tente novamente mais tarde.'], null);
        }

    }

    // --- MÉTODOS DE REQUISIÇÕES (POST) ---

    /**
     * Processa a tentativa de login do usuário
     *
     * @param Request $request Objeto com dados da requisição
     * @return void
     * @throws Exception
     */
    public function autenticar(Request $request): void
    {
        try {

            // Verifica se o usuário já está autenticado
            if ($this->authService->estaAutenticado()) {
                $this->redirecionar('/inicio');
            }

            // Validação do token CSRF
            $this->validarTokenCSRF($request);

            // Obtenção dos dados do formulário
            $nomeAcesso = $request->post('nome_acesso');
            $senha = $request->post('senha');

            // Validação dos dados
            if (empty($nomeAcesso) || empty($senha)) {
                throw new Exception('Login e/ou senha inválidos.');
            }

            // Tentativa de autenticação
            $usuarioLogin = $this->authService->autenticar(
                $nomeAcesso,
                $request->post('senha'),
                $request->ip()
            );

            // Consulta na instância do usuário
            $usuario = Usuario::id($usuarioLogin->obterUsuarioId())->first();

            
            if (!$usuario) {
                throw new Exception('Login e/ou senha inválidos.');
            }

            // Verifica se o usuário está usando a senha padrão (Mudar@123)
            if ($usuarioLogin->verificarSenha($_ENV['SISTEMA_SENHA_PADRAO'] ?? 'Mudar@123')) {
                // Gera um token temporário para a alteração de senha
                $tokenAlteracao = bin2hex(random_bytes(32));

                // Armazena no token de usuário para validação
                UsuarioToken::where('usuario_id', $usuario->obterId())
                    ->where('tipo', 'alteracao_senha_obrigatoria')
                    ->where('status', true)
                    ->update(['status' => false]);

                $usuario->tokens()->create([
                    'tipo' => 'alteracao_senha_obrigatoria',
                    'token_hash' => $tokenAlteracao,
                    'data_expiracao' => date('Y-m-d H:i:s', strtotime('+30 minutes'))
                    ]);
                    
                    // Remove token CSRF
                    $this->removerTokenCSRF();
                    
                    // Redireciona para a página de alteração obrigatória de senha
                flash()->aviso('Você está usando a senha padrão. Por segurança, altere-a para continuar.');
                $this->redirecionar('/alterar-senha/' . $tokenAlteracao);
                return;
            }
            
            // Emite o token JWT (cookie httpOnly via setcookie)
            $this->authService->emitirToken($usuario);
            
            // Remove token CSRF
            $this->removerTokenCSRF();

            // Redireciona para o painel — o browser processa o Set-Cookie antes de seguir o redirect
            $this->redirecionar('/inicio');

        } catch (\Throwable $exception) {

            // Flash de erro e redireciona de volta ao login
            flash()->erro($exception->getMessage() ?: 'Erro ao tentar fazer login. Tente novamente mais tarde.');
            $this->redirecionar('/login');
        }
    }

    /**
     * Envia um link de redefinição de senha para o usuário
     *
     * @param Request $request
     * @return void
     */
    public function enviarLinkRedefinicao(Request $request): void
    {
        try {

            // Validação do token CSRF
            $this->validarTokenCSRF($request);

            // Obtenção do e-mail do usuário
            $email = $request->post('email');

            // Validação do e-mail (verifica se é válido e se o domínio existe)
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || !checkdnsrr(substr(strrchr($email, "@"), 1), "MX")) {
                throw new Exception('E-mail inválido.');
            }

            // Verificação se o e-mail existe
            $usuario = Usuario::email($email)->first();

            // Verificação se o usuário existe
            if (!empty($usuario)) {

                // Gera um token de redefinição de senha
                $token = UsuarioToken::gerarToken();

                // Obtém a instância de 'UsuarioLogin'
                $usuarioTokens = $usuario->tokens();

                // Resetar tokens de redefinição de senha anteriores
                UsuarioToken::where('usuario_id', $usuario->obterId())
                    ->where('tipo', 'redefinicao_senha')
                    ->where('status', true)
                    ->update(['status' => false]);

                $usuarioTokens->create([
                    'tipo' => 'redefinicao_senha',
                    'token_hash' => $token,
                    'data_expiracao' => date('Y-m-d H:i:s', strtotime('+1 hour'))
                ]);

                // Gera o link de redefinição de senha
                $link_redefinicao = obterURL('/redefinir-senha/' . $token);

                // Envia o link de redefinição de senha para o usuário
                $emailService = new EmailService();
                $enviado = $emailService->enviarEmailRedefinicaoSenha(
                    $email,
                    [
                        'link_redefinicao' => $link_redefinicao,
                        'usuario_nome_reduzido' => $usuario->obterNomeReduzido()
                    ]
                );
            }

            // Remove token CSRF (para evitar ataques CSRF)
            $this->removerTokenCSRF();

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Link de redefinição enviado com sucesso. Verifique a caixa de mensagens e spam do seu e-mail.'
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao tentar enviar o link de redefinição de senha. Tente novamente mais tarde.'
            ], 400);
        }
    }

    /**
     * Redefine a senha do usuário com base no token recebido
     *
     * @param Request $request
     * @return void
     */
    public function salvarNovaSenha(Request $request): void
    {
        try {

            // Validação do token CSRF
            $this->validarTokenCSRF($request);

            // Obtenção dos dados do formulário
            $senhaNova = $request->post('nova_senha');
            $senhaConfirmacao = $request->post('confirmar_nova_senha');
            $tokenRedefinicao = $request->post('token_redefinicao');

            // Validação dos campos obrigatórios
            if (empty($senhaNova) || empty($senhaConfirmacao) || empty($tokenRedefinicao)) {
                throw new Exception('Campos obrigatórios não preenchidos.');
            }

            // Verifica se a senha nova é a mesma que a senha de confirmação
            if ($senhaNova != $senhaConfirmacao) {
                throw new Exception('As senhas não coincidem. Verifique-as e tente novamente.');
            }

            // Verifica se a senha nova atende aos requisitos
            // 1. A senha deve possuir, pelo menos, 8 caracteres (e até 32)
            if (strlen($senhaNova) < 8 || strlen($senhaNova) > 32) {
                throw new Exception('A senha nova deve possuir, pelo menos, 8 caracteres (e até 32). Altere-a e tente novamente.');
            }

            // 2. A senha deve possuir uma letra minúscula e uma letra maiúscula
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z]).+$/', $senhaNova)) {
                throw new Exception('A senha nova deve possuir uma letra minúscula e uma letra maiúscula. Altere-a e tente novamente.');
            }

            // 3. A senha deve possuir um caractere especial
            if (!preg_match('/[^a-zA-Z0-9]/', $senhaNova)) {
                throw new Exception('A senha nova deve possuir um caractere especial. Altere-a e tente novamente.');
            }

            // Verificação se o token é válido
            $usuarioTokenRedefinicao = UsuarioToken::tokenRedefinicao($tokenRedefinicao, true)->first();

            if (!$usuarioTokenRedefinicao || $usuarioTokenRedefinicao->verificarExpirado()) {
                throw new Exception('Token de redefinição de senha inválido.');
            }

            // Verificação se o usuário existe
            $usuario = $usuarioTokenRedefinicao->usuario()->first();

            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            // Verificação se o usuário tem um login
            $usuarioLogin = UsuarioLogin::usuarioId($usuario->id)->first();

            if (!$usuarioLogin) {
                throw new Exception('Usuário não possui um login.');
            }

            // Atualização da senha do usuário
            $usuarioLogin->atribuirSenha($senhaNova);
            $usuarioLogin->salvar();

            $usuarioTokenRedefinicao->excluir();

            // Remove token CSRF (para evitar ataques CSRF)
            $this->removerTokenCSRF();

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Senha redefinida com sucesso. Faça login para continuar.'
            ]);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao tentar redefinir sua senha. Tente novamente mais tarde.'
            ], 400);
        }
    }

    /**
     * Faz o logout do usuário
     *
     * @return void
     */
    public function sair(): void
    {
        $this->authService->encerrarSessao();
        $this->redirecionar('/login');
    }

    // --- MÉTODOS DE ALTERAÇÃO OBRIGATÓRIA DE SENHA ---

    /**
     * Exibe a página de alteração obrigatória de senha (quando usando senha padrão)
     *
     * @param string $token
     * @return void
     */
    public function exibirAlterarSenhaObrigatoria(string $token): void
    {
        try {
            if (empty($token)) {
                throw new Exception('Token de alteração inválido.');
            }

            // Verifica se o token é válido
            $usuarioToken = UsuarioToken::where('token_hash', $token)
                ->where('tipo', 'alteracao_senha_obrigatoria')
                ->where('status', true)
                ->first();

            if (!$usuarioToken || $usuarioToken->verificarExpirado()) {
                throw new Exception('Token de alteração de senha expirado ou inválido. Faça login novamente.');
            }

            $this->renderizar('auth/alterar-senha', [
                'token_csrf' => $this->gerarTokenCSRF(),
                'token_alteracao' => $token
            ]);

        } catch (Exception $exception) {
            flash()->erro($exception->getMessage());
            $this->redirecionar('/login');
        }
    }

    /**
     * Alias de compatibilidade para rota legada.
     *
     * @param string $token
     * @return void
     */
    public function exibirAlterarSenha(string $token): void
    {
        $this->exibirAlterarSenhaObrigatoria($token);
    }

    /**
     * Processa a alteração obrigatória de senha
     *
     * @param Request $request
     * @return void
     */
    public function salvarSenhaObrigatoria(Request $request): void
    {
        try {

            // Validação do token CSRF
            $this->validarTokenCSRF($request);

            $senhaNova = $request->post('nova_senha');
            $senhaConfirmacao = $request->post('confirmar_nova_senha');
            $tokenAlteracao = $request->post('token_alteracao');

            if (empty($senhaNova) || empty($senhaConfirmacao) || empty($tokenAlteracao)) {
                throw new Exception('Campos obrigatórios não preenchidos.');
            }

            if ($senhaNova !== $senhaConfirmacao) {
                throw new Exception('As senhas não coincidem.');
            }

            if (strlen($senhaNova) < 8 || strlen($senhaNova) > 32) {
                throw new Exception('A senha deve ter entre 8 e 32 caracteres.');
            }

            if (!preg_match('/[^a-zA-Z0-9]/', $senhaNova)) {
                throw new Exception('A senha deve possuir pelo menos um caractere especial.');
            }

            // Não permitir que a nova senha seja a mesma que a senha padrão
            if ($senhaNova === ($_ENV['SISTEMA_SENHA_PADRAO'] ?? 'Mudar@123')) {
                throw new Exception('A nova senha não pode ser igual à senha padrão. Escolha uma senha diferente.');
            }

            // Verificar token
            $usuarioToken = UsuarioToken::where('token_hash', $tokenAlteracao)
                ->where('tipo', 'alteracao_senha_obrigatoria')
                ->where('status', true)
                ->first();

            if (!$usuarioToken || $usuarioToken->verificarExpirado()) {
                throw new Exception('Token expirado. Faça login novamente.');
            }

            $usuario = $usuarioToken->usuario()->first();
            if (!$usuario) {
                throw new Exception('Usuário não encontrado.');
            }

            $usuarioLogin = UsuarioLogin::usuarioId($usuario->id)->first();
            
            if (!$usuarioLogin) {
                throw new Exception('Login do usuário não encontrado.');
            }

            // Atualizar senha
            $usuarioLogin->atribuirSenha($senhaNova);
            $usuarioLogin->salvar();

            // Invalidar token
            $usuarioToken->excluir();

            // Remove token CSRF
            $this->removerTokenCSRF();

            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Senha alterada com sucesso! Faça login com sua nova senha.'
            ]);

        } catch (Exception $exception) {
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao alterar a senha.'
            ], 400);
        }
    }

    /**
     * Alias de compatibilidade para rota legada.
     *
     * @param Request $request
     * @return void
     */
    public function salvarSenhaAlterada(Request $request): void
    {
        $this->salvarSenhaObrigatoria($request);
    }
}