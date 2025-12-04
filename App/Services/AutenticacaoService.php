<?php

/**
 * @file AutenticacaoService.php
 * @description Serviço responsável pelas funções que envolvem a autenticação e o controle do usuário logado no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Services;

// Importação de classes
use App\Models\Usuario;
use App\Models\UsuarioLogin;
use App\Models\LoginTentativa;
use DateTime;
use Exception;
use parallel\Runtime;

/**
 * Classe AutenticacaoService
 *
 * Serviço responsável pelas funções que envolvem a autenticação e o controle do usuário logado no sistema
 *
 * @package App\Services
 */
class AutenticacaoService
{

    /**
     * Autentica um usuário com email e senha
     *
     * @param string $nomeAcesso
     * @param string $senha
     * @param string $ip
     * @return UsuarioLogin
     * @throws \PHPMailer\PHPMailer\Exception
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

                $usuarioEmailPessoal = $usuarioLogin->usuario()->first()->obterEmailPessoal();

                // 2. Registramos a tarefa para ser executada no final da requisição.
                register_shutdown_function(function () use ($usuarioEmailPessoal) {
                    // Este código só será executado DEPOIS que a resposta for enviada ao usuário.
                    // Pode ser necessário incluir o autoloader ou garantir que a classe EmailService
                    // esteja disponível. A forma mais segura é usar o namespace completo.

                    // Nós instanciamos o serviço de e-mail aqui dentro.
                    $mail = new \App\Services\EmailService();
                    $mail->enviarEmailBloqueio($usuarioEmailPessoal);
                });
            }
            throw new Exception("Login e/ou senha inválida.");
        }

        $this->resetarTentativas($ip, $usuarioLogin);

        return $usuarioLogin;
    }

    /**
     * Validação para se o usuário / IP teve bloqueio por IP
     *
     * @param string $ip
     * @param ?UsuarioLogin $usuarioLogin
     * @throws \Exception
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
     * @throws DateMalformedStringException
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

    /**
     * Verifica se existe um usuário autenticado na sessão.
     *
     * @return bool
     */
    public static function estaAutenticado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    /**
     * Retorna o usuário autenticado na sessão, se houver.
     *
     * @return Usuario|null
     */
    public static function usuarioAutenticado(): ?Usuario
    {
        if (!self::estaAutenticado()) {
            return null;
        }

        return Usuario::buscarPorId($_SESSION['usuario_id']);
    }

    public function iniciarSessao(Usuario $usuario): void
    {
        // Carregar grupos com permissões
        $usuario->load('grupos.permissoes');
        
        $_SESSION['usuario_id'] = $usuario->obterId();
        $_SESSION['usuario_nome'] = $usuario->obterNomeReduzido();
        $_SESSION['usuario_grupos'] = array_map('strtoupper', $usuario->grupos->pluck('nome')->toArray());
        
        // Carregar permissões de todos os grupos do usuário
        $permissoes = [];
        foreach ($usuario->grupos as $grupo) {
            $permissoesGrupo = $grupo->permissoes->pluck('codigo')->toArray();
            $permissoes = array_merge($permissoes, $permissoesGrupo);
        }
        $_SESSION['usuario_permissoes'] = array_unique($permissoes);
    }

    /**
     * Realiza logout do usuário autenticado.
     *
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['usuario_id']);
        unset($_SESSION['usuario_nome']);
        unset($_SESSION['usuario_tipo']);
        unset($_SESSION['token_csrf']);
        session_destroy();
    }


}
