<?php

declare(strict_types=1);

/**
 * @file Controller.php
 * @description Classe-base para todos os controladores do sistema, responsável pelo fluxo de requisições do sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2026
 */

// Declaração de namespace
namespace App\Core;

// Importação de classes
use Exception;
use Random\RandomException;

/**
 * Classe Controller (abstrata)
 *
 * Gerencia o fluxo de requisições do sistema
 *
 * @package App\Core
 * @abstract
 */
abstract class Controller
{

    // --- ATRIBUTOS ---

    /**
     * Nome do cookie CSRF
     * (legível pelo JS para double-submit pattern)
     */
    private const COOKIE_CSRF = 'gaio_csrf';

    // --- MÉTODOS DE SEGURANÇA ---

    /**
     * Gera um token CSRF usando o padrão double-submit cookie
     * O token é armazenado em um cookie legível pelo JS e retornado para uso em formulários
     *
     * @return string
     * @throws RandomException (Se o gerador de token CSRF falhar)
     */
    protected function gerarTokenCSRF(): string
    {
        // Verifica se já existe um cookie CSRF válido
        if (isset($_COOKIE[self::COOKIE_CSRF]) && !empty($_COOKIE[self::COOKIE_CSRF])) {
            return $_COOKIE[self::COOKIE_CSRF];
        }

        // Gera um novo token CSRF
        $token = bin2hex(random_bytes(32));

        // Emite o cookie CSRF
        $seguro = ($_ENV['SISTEMA_AMBIENTE'] ?? 'desenvolvimento') === 'producao';

        // Opções do cookie
        $opcoes = [
            'expires' => 0, // Expira ao fechar o navegador
            'path' => '/',
            'secure' => $seguro,
            'httponly' => false,
            'samesite' => 'Lax',
        ];

        // Define o cookie
        setcookie(self::COOKIE_CSRF, $token, $opcoes);

        // Define no superglobal para a requisição atual
        $_COOKIE[self::COOKIE_CSRF] = $token;

        return $token;
    }

    /**
     * Valida o token CSRF enviado na requisição (double-submit cookie pattern)
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    protected function validarTokenCSRF(Request $request): void
    {
       // Obtém o token CSRF enviado no formulário
       $tokenEnviado = $request->post('token_csrf');

       // Obtém o token CSRF armazenado no cookie
       $tokenCookie = $_COOKIE[self::COOKIE_CSRF] ?? null;

       // Verifica se ambos existem e coincidem
       if (!$tokenEnviado || !$tokenCookie || !hash_equals($tokenCookie, $tokenEnviado)) {
           throw new Exception('CSRF token inválido.');
       }
    }

    /**
     * Remove o token CSRF limpando o cookie
     *
     * @return void
     */
    protected function removerTokenCSRF(): void
    {
        $seguro = ($_ENV['SISTEMA_AMBIENTE'] ?? 'desenvolvimento') === 'producao';

        // Opções do cookie
        $opcoes = [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => $seguro,
            'httponly' => false,
            'samesite' => 'Lax',
        ];

        setcookie(self::COOKIE_CSRF, '', $opcoes);

        unset($_COOKIE[self::COOKIE_CSRF]);
    }


    // --- MÉTODOS DE RESPOSTA ---

    /**
     * Renderiza uma view e envia para o cliente
     *
     * @param string $caminhoView
     * @param array $dados
     * @param ?string $layout
     * @return void
     */
    protected function renderizar(string $caminhoView, array $dados = [], ?string $layout = 'app'): void
    {
        // Injeta o token CSRF automaticamente em todas as views (se ainda não foi definido)
        if (!isset($dados['token_csrf'])) {
            $dados['token_csrf'] = $this->gerarTokenCSRF();
        }

        // Delega a renderização da view para a classe Response
        $conteudo = Response::renderizar($caminhoView, $dados, $layout);

        // Envia o conteúdo renderizado para o cliente
        echo $conteudo;
    }

    /**
     * Redireciona para uma URL específica
     *
     * @param string $url
     * @param int $codigoStatus
     * @return never
     */
    protected function redirecionar(string $url, int $codigoStatus = 302): never
    {
        // Delega o redirecionamento para a classe Response
        Response::redirecionar($url, $codigoStatus);
    }

    /**
     * Envia resposta em formato JSON
     *
     * @param array $dados
     * @param int $codigoHttp
     * @return never
     */
    protected function responderJSON(array $dados, int $codigoHttp = 200): never
    {
        // Delega a resposta em formato JSON para a classe Response
        Response::json($dados, $codigoHttp);
    }
}
