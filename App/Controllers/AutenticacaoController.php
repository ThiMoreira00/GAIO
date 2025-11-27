<?php

/**
 * @file AutenticacaoController.php
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
use App\Services\AutenticacaoService;
use App\Services\EmailService;
use App\Services\NotificacaoService;
use Exception;
use Random\RandomException;

/**
 * Classe AutenticacaoController
 *
 * Gerencia autenticação, registro e recuperação de conta dos usuários.
 * Implementa medidas de segurança contra CSRF e session fixation.
 *
 * @package App\Controllers
 * @extends Controller
 */
class AutenticacaoController extends Controller
{

    // --- ATRIBUTOS ---
    /**
     * Instância do serviço de autenticação
     * @var AutenticacaoService
     */
    private AutenticacaoService $autenticacaoService;


    // --- MÉTODOS ---

    /**
     * Construtor da classe
     *
     */
    public function __construct()
    {
        $this->autenticacaoService = new AutenticacaoService();
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
        if ($this->autenticacaoService->estaAutenticado()) {
            $this->redirecionar('/inicio');
        }

        // Renderiza a página de login
        $this->renderizar('auth/login', [
            'token_csrf' => $this->gerarTokenCSRF()
        ]);
    }

    /**
     * Exibe a página de "Esqueci minha senha"
     *
     * @return void
     * @throws RandomException (Gerador de token CSRF)
     */
    public function exibirEsqueciSenha(): void {

        // Verifica se o usuário já está autenticado
        if ($this->autenticacaoService->estaAutenticado()) {
            $this->redirecionar('/inicio');
        }

        // Renderiza a página de "Esqueci minha senha"
        $this->renderizar('auth/esqueci-senha', [
            'token_csrf' => $this->gerarTokenCSRF()
        ]);
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
            if ($this->autenticacaoService->estaAutenticado()) {
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
            ]);

        } catch (Exception $exception) {
            http_response_code(400);
            $this->renderizar('erros/erro-400', ['mensagem' => $exception->getMessage() ?? 'Erro ao tentar redefinir sua senha. Tente novamente mais tarde.']);;
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
            if ($this->autenticacaoService->estaAutenticado()) {
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
            // Retorna a instância do login do usuário autenticado ou lança uma exceção
            $usuarioLogin = $this->autenticacaoService->autenticar(
                $nomeAcesso,
                $request->post('senha'),
                $request->ip()
            );

            // Consulta na instância do usuário
            $usuario = Usuario::id($usuarioLogin->obterUsuarioId())->first();

            if (!$usuario) {
                throw new Exception('Login e/ou senha inválidos. A');
            }

            // Criação de notificação de login para o usuário
            NotificacaoService::criar('LOGIN_NOVO_DETECTADO', [$usuario], [
                'navegador' => $request->navegador(),
                'sistema_operacional' => $request->sistemaOperacional(),
            ]);

            // Inicia sessão
            $this->autenticacaoService->iniciarSessao($usuario);

            // Remove token CSRF (para evitar ataques CSRF)
            $this->removerTokenCSRF();

            // Retorna resposta JSON
            $this->responderJSON([
                'status' => 'sucesso',
                'mensagem' => 'Login efetuado com sucesso.'
            ], 200);

        } catch (Exception $exception) {

            // Retorna resposta JSON com mensagem de erro
            $this->responderJSON([
                'status' => 'erro',
                'mensagem' => $exception->getMessage() ?? 'Erro ao tentar fazer login. Tente novamente mais tarde.'
            ], 400);

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
            if ($usuario) {

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
                $emailService->enviarEmailRedefinicaoSenha(
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
            if (preg_match('/^(?=.*[a-z])(?=.*[A-Z]).+$/', $senhaNova)) {
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
            $usuarioLogin = $usuario->login()->first();

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
        $this->autenticacaoService->logout();
        $this->redirecionar('/login');
    }
}