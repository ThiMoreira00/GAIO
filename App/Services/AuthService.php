<?php

/**
 * @file AuthService.php
 * @description Serviço responsável pelas funções que envolvem a autenticação e o controle do usuário logado no sistema. 
 * @author Thiago Moreira
 * @copyright Copyright (c) 2026
 */

// Declaração de namespace
namespace App\Services;

// Importação de classes
use App\Models\Usuario;
use App\Models\UsuarioLogin;
use App\Models\LoginTentativa;
use App\Services\EmailService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use DateTime;
use Exception;

/**
 * Classe AuthService
 *
 * Serviço responsável pelas funções que envolvem a autenticação e o controle do usuário logado no sistema.
 *
 * @package App\Services
 */
class AuthService
{

    // --- ATRIBUTOS ---

    /**
     * Nome do cookie que armazena o JWT
     */
    private const COOKIE_JWT = 'gaio_token';

    /**
     * Algoritmo de assinatura do JWT
     */
    private const JWT_ALGORITMO = 'HS256';


    // --- PROPRIEDADES ESTÁTICAS ---

    /**
     * Claims decodificadas do JWT (cache em memória para a requisição)
     * @var object|null
     */
    private static ?object $claims = null;

    /**
     * Indica se a inicialização do JWT já foi executada nesta requisição
     * @var bool
     */
    private static bool $inicializado = false;

    /**
     * Cache de permissões carregadas do banco (lazy-load por requisição)
     * @var array|null
     */
    private static ?array $permissoesCache = null;

    // --- MÉTODOS DE AUTENTICAÇÃO ---

    /**
     * Autentica um usuário com email e senha
     *
     * @param string $nomeAcesso
     * @param string $senha
     * @param string $ip
     * @return UsuarioLogin
     * @throws PHPMailerException
     */
    public function autenticar(string $nomeAcesso, string $senha, string $ip): UsuarioLogin
    {
        // Busca a instância do 'UsuarioLogin' para obter o acesso as credenciais
        $usuarioLogin = UsuarioLogin::buscarPorNomeAcesso($nomeAcesso);

        // Cria o registro de tentativa de login
        $tentativa = LoginTentativa::firstOrCreate([
            'login_id' => $usuarioLogin ? $usuarioLogin->obterId() : null,
            'identificador' => $ip
        ]);

        // Verifica se o usuário está desbloqueado
        if ($tentativa->obterTentativas() >= LoginTentativa::LIMITE_TENTATIVAS && !empty($tentativa->obterDataBloqueio()) && $tentativa->obterDataBloqueio()->getTimestamp() < time()) {

            // Reseta tentativas (apenas com base no IP e no usuário - se aplicável -)
            $this->resetarTentativas($ip, $usuarioLogin);
        }

        // Verifica se o usuário está bloqueado por IP
        $this->validarBloqueioPorIP($ip, $usuarioLogin);

        // Verifica se o usuário está bloqueado por conta
        $this->validarBloqueioPorUsuario($tentativa);

        // Verifica se o usuário existe
        if (!$usuarioLogin) {
            $this->registrarTentativaFalha($tentativa);
            throw new Exception('Login e/ou senha inválidos.');
        }

        // Verifica se a senha está correta
        if (!$usuarioLogin->verificarSenha($senha)) {
            $this->registrarTentativaFalha($tentativa);

            // Verifica se a tentativa está bloqueada
            
            if ($tentativa->estaBloqueada()) {

                /** @var Usuario $usuario */
                $usuario = $usuarioLogin->usuario()->first();
                $usuarioEmailPessoal = $usuario->obterEmailPessoal();

                register_shutdown_function(function () use ($usuarioEmailPessoal) {
                    $mail = new EmailService();
                    $mail->enviarEmailBloqueio($usuarioEmailPessoal);
                });
            }
            throw new PHPMailerException("Login e/ou senha inválida.");
        }

        $this->resetarTentativas($ip, $usuarioLogin);

        return $usuarioLogin;
    }

    /**
     * Validação para se o usuário / IP teve bloqueio por IP
     *
     * @param string $ip
     * @param ?UsuarioLogin $usuarioLogin
     * @throws Exception
     * @return void
     */
    private function validarBloqueioPorIP(string $ip, ?UsuarioLogin $usuarioLogin): void
    {
        $tentativas = LoginTentativa::where(function ($query) use ($ip) {
            $query->where('identificador', $ip)
                ->where('tentativas', '>', 0);
        })
            ->where(function ($query) use ($usuarioLogin) {
                $query->whereNull('login_id');
                if ($usuarioLogin) {
                    $query->orWhere('login_id', '<>', $usuarioLogin->obterId());
                }
            })->orderBy('data_atualizado', 'desc');

        // Resetar tentativas se o tempo de bloqueio já passou para usuário nulo
        /** @var LoginTentativa $tentativa */
        foreach ($tentativas->get() as $tentativa) {
            if ($tentativa->login_id === null && $tentativa->estaBloqueada()) {
                $bloqueadoAte = $tentativa->obterDataBloqueio();
                if ($bloqueadoAte && $bloqueadoAte < new DateTime()) {
                    $tentativa->resetarBloqueio();
                    $tentativa->salvar();
                }
            }
        }

        // Verificar bloqueio
        /** @var LoginTentativa $tentativa */
        foreach ($tentativas->get() as $tentativa) {
            if ($tentativa->estaBloqueada()) {
                throw new Exception(sprintf(
                    'Seu IP foi bloqueado temporariamente até %s após múltiplas tentativas de login incorretas. Tente novamente mais tarde.',
                    $tentativa->obterDataBloqueio()?->format('d/m/Y H:i')
                ));
            }
        }
    }

    /**
     * Valida se o bloqueio foi feito com base na conta do usuário.
     *
     * @param LoginTentativa $tentativa Instância de tentativa de login.
     * @throws Exception Caso a conta esteja bloqueada.
     * @return void
     */
    private function validarBloqueioPorUsuario(LoginTentativa $tentativa): void
    {
        if ($tentativa->estaBloqueada()) {
            throw new Exception(sprintf(
                'Sua conta foi bloqueada temporariamente até %s após múltiplas tentativas de login incorretas. Tente novamente mais tarde.', $tentativa->obterDataBloqueio()?->format('d/m/Y H:i')));
        }
    }

    /**
     * Registra uma tentativa de login falha e bloqueia se exceder o limite.
     *
     * @param LoginTentativa $tentativa Instância de tentativa de login.
     * @return void
     */
    private function registrarTentativaFalha(LoginTentativa $tentativa): void
    {
        $tentativa->incrementarTentativas();
        $tentativa->salvar();

        if ($tentativa->obterTentativas() >= LoginTentativa::LIMITE_TENTATIVAS) {
            $tentativa->bloquearPorMinutos(LoginTentativa::BLOQUEIO_MINUTOS);
            $tentativa->salvar();
        }
    }

    /**
     * Reseta tentativas e bloqueios de login para o IP e usuário informados.
     *
     * @param string $ip Endereço IP.
     * @param ?UsuarioLogin $usuarioLogin Instância do login do usuário.
     * @return void
     */
    private function resetarTentativas(string $ip, ?UsuarioLogin $usuarioLogin): void
    {
        LoginTentativa::where(function($query) use ($ip, $usuarioLogin) {
            $query->where('identificador', $ip)
                ->orWhere('login_id', $usuarioLogin?->obterId())
                ->orWhere('login_id', null);
        })->update([
            'tentativas' => 0,
            'data_bloqueio' => null
        ]);
    }

    
    // --- MÉTODOS DE JWT ---

    /**
     * Inicializa o JWT a partir do cookie httpOnly.
     * Decodifica o token e armazena as claims em memória.
     * Deve ser chamado no início de cada requisição (via middleware).
     *
     * @return void
     */
    public static function inicializarJWT(): void
    {
        // Evita inicialização duplicada na mesma requisição
        if (self::$inicializado) {
            return;
        }

        self::$inicializado = true;
        self::$claims = null;

        // Verifica se o cookie JWT existe
        if (!isset($_COOKIE[self::COOKIE_JWT])) {
            return;
        }

        try {

            // Decodifica o JWT usando a chave secreta
            $chave = self::obterChaveSecreta();
            self::$claims = JWT::decode(
                $_COOKIE[self::COOKIE_JWT],
                new Key($chave, self::JWT_ALGORITMO)
            );
            
        } catch (ExpiredException $e) {            
            self::limparCookieJWT();
            self::registrarLog('JWT expirado: ' . $e->getMessage());

        } catch (Exception $e) {
            self::limparCookieJWT();
            self::registrarLog('Erro ao decodificar JWT: ' . $e->getMessage());

        }
    }

    /**
     * Verifica se existe um usuário autenticado via JWT.
     *
     * @return bool
     */
    public static function estaAutenticado(): bool
    {
        if (!self::$inicializado) {
            self::inicializarJWT();
        }

        return self::$claims !== null && isset(self::$claims->sub);
    }

    /**
     * Retorna o usuário autenticado via JWT, se houver.
     *
     * @return Usuario|null
     */
    public static function obterUsuarioAutenticado(): ?Usuario
    {
        if (!self::estaAutenticado()) {
            return null;
        }

        return Usuario::buscarPorId(self::$claims->sub);
    }

    /**
     * Emite um JWT para o usuário autenticado e o armazena em um cookie httpOnly.
     * Substitui o antigo `iniciarSessao()`.
     *
     * @param Usuario $usuario
     * @return string JWT emitido
     */
    public function emitirToken(Usuario $usuario): string
    {
        try {
            // Carregar grupos do usuário
            $usuario->load('grupos');

            $grupos = array_map('strtoupper', $usuario->grupos->pluck('nome')->toArray());

            $agora = time();
            $expiracao = $agora + self::obterTempoExpiracao();

            $payload = [
                'iss' => $_ENV['SISTEMA_LINK'] ?? 'gaio',
                'sub' => $usuario->obterId(),
                'nome' => $usuario->obterNomeReduzido(),
                'grupos' => $grupos,
                'perfil_atual' => null,
                'iat' => $agora,
                'exp' => $expiracao,
            ];

            $jwt = JWT::encode($payload, self::obterChaveSecreta(), self::JWT_ALGORITMO);

            // Emite o cookie httpOnly com o JWT
            self::emitirCookieJWT($jwt, $expiracao);

            // Atualiza claims em memória para uso imediato na mesma requisição
            self::$claims = (object) $payload;
            self::$inicializado = true;
            self::$permissoesCache = null;

            return $jwt;

        } catch (Exception $e) {
            self::registrarLog('Falha ao emitir JWT: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Encerra a sessão do usuário removendo o cookie JWT.
     * Substitui o antigo `logout()`.
     *
     * @return void
     */
    public function encerrarSessao(): void
    {
        self::limparCookieJWT();
        self::$claims = null;
        self::$inicializado = false;
        self::$permissoesCache = null;
    }

    // --- MÉTODOS DE ACESSO ÀS CLAIMS ---

    /**
     * Retorna os grupos do usuário autenticado a partir das claims do JWT.
     *
     * @return array
     */
    public static function obterGrupos(): array
    {
        if (!self::estaAutenticado()) {
            return [];
        }

        return (array) (self::$claims->grupos ?? []);
    }

    /**
     * Retorna as permissões do usuário autenticado.
     * Carrega do banco de dados sob demanda (lazy-load) com cache por requisição.
     *
     * @return array
     */
    public static function obterPermissoes(): array
    {
        if (!self::estaAutenticado()) {
            return [];
        }

        if (self::$permissoesCache !== null) {
            return self::$permissoesCache;
        }

        $usuario = Usuario::buscarPorId(self::$claims->sub);

        if (!$usuario) {
            self::$permissoesCache = [];
            return [];
        }

        $usuario->load('grupos.permissoes');

        $permissoes = [];
        foreach ($usuario->grupos as $grupo) {
            $permissoes = array_merge($permissoes, $grupo->permissoes->pluck('codigo')->toArray());
        }

        self::$permissoesCache = array_values(array_unique($permissoes));

        return self::$permissoesCache;
    }

    /**
     * Retorna o perfil de acesso atual do usuário.
     *
     * @return string|null
     */
    public static function obterPerfilAtual(): ?string
    {
        if (!self::estaAutenticado()) {
            return null;
        }

        return self::$claims->perfil_atual ?? null;
    }

    /**
     * Define o perfil de acesso atual do usuário e re-emite o token JWT.
     *
     * @param array $perfil
     * @return void
     */
    public static function definirPerfilAtual(array $perfil): void
    {
        if (!self::estaAutenticado()) {
            return;
        }

        // Atualiza a claim do perfil atual
        $payload = (array) self::$claims;
        $payload['perfil_atual'] = $perfil;

        // Re-emite o token com o perfil atualizado
        $jwt = JWT::encode($payload, self::obterChaveSecreta(), self::JWT_ALGORITMO);
        self::emitirCookieJWT($jwt, $payload['exp']);

        // Atualiza claims em memória
        self::$claims = (object) $payload;
    }

    /**
     * Retorna o nome do usuário autenticado a partir das claims.
     *
     * @return string|null
     */
    public static function obterNomeUsuario(): ?string
    {
        if (!self::estaAutenticado()) {
            return null;
        }

        return self::$claims->nome ?? null;
    }


    // --- MÉTODOS AUXILIARES PRIVADOS ---

    /**
     * Obtém a chave secreta JWT do .env
     *
     * @return string
     * @throws Exception
     */
    private static function obterChaveSecreta(): string
    {
        $chave = $_ENV['JWT_SECRET'] ?? null;

        if (empty($chave)) {
            throw new Exception('JWT_SECRET não configurado no arquivo .env');
        }

        return $chave;
    }

    /**
     * Obtém o tempo de expiração do JWT em segundos
     *
     * @return int
     */
    private static function obterTempoExpiracao(): int
    {
        return (int) ($_ENV['JWT_EXPIRACAO'] ?? 28800); // 8 horas padrão
    }

    /**
     * Emite o cookie httpOnly com o JWT
     *
     * @param string $jwt
     * @param int $expiracao Timestamp de expiração
     * @return void
     */
    private static function emitirCookieJWT(string $jwt, int $expiracao): void
    {
        $ehProducao = ($_ENV['SISTEMA_AMBIENTE'] ?? 'desenvolvimento') === 'producao';

        if ($ehProducao) {
            $opcoes = [
                'expires' => $expiracao,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ];
        } else {
            // Desenvolvimento
            $opcoes = [
                'expires' => $expiracao,
                'path' => '/',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ];
        }

        setcookie(self::COOKIE_JWT, $jwt, $opcoes);

        // Debug: tamanho total do cookie (nome=valor)
        $cookieTotal = self::COOKIE_JWT . '=' . $jwt;

        // Debug: mostra o header exato
        $headersList = headers_list();
        $cookieHeaders = array_filter($headersList, fn($h) => stripos($h, 'Set-Cookie') === 0);
        $cookieHeader = $cookieHeaders ? array_values($cookieHeaders)[0] : 'NONE';

        // Define no superglobal para a requisição atual
        $_COOKIE[self::COOKIE_JWT] = $jwt;
    }

    private static function limparCookieJWT(): void
    {
        $ehProducao = ($_ENV['SISTEMA_AMBIENTE'] ?? 'desenvolvimento') === 'producao';

        // Usar as mesmas opções para remover o cookie
        if ($ehProducao) {
            $opcoes = [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ];
        } else {
            $opcoes = [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ];
        }

        setcookie(self::COOKIE_JWT, '', $opcoes);
        unset($_COOKIE[self::COOKIE_JWT]);
    }

    private static function registrarLog(string $mensagem): void
    {
        // TODO: Adicionar log para o banco de dados, e não apenas para o arquivo
        error_log('[AuthService] ' . date('Y-m-d H:i:s') . ' - ' . $mensagem);
    }

    // --- MÉTODOS LEGADOS (compatibilidade) ---

    /**
     * @deprecated Use emitirToken() ao invés deste método
     */
    public function login(Usuario $usuario): void
    {
        $this->emitirToken($usuario);
    }

    /**
     * @deprecated Use encerrarSessao() ao invés deste método
     */
    public function logout(): void
    {
        $this->encerrarSessao();
    }
}
