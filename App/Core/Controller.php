<?php

declare(strict_types=1);

/**
 * @file Controller.php
 * @description Classe-base para todos os "controllers" do sistema, responsável pelo fluxo de requisições.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
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

    // --- MÉTODOS DE SEGURANÇA ---

    /**
     * Gera um token CSRF
     *
     * @return string
     * @throws RandomException (Se o gerador de token CSRF falhar)
     */
    protected function gerarTokenCSRF(): string
    {
        // Verifica se já existe um token CSRF na sessão
        if (!isset($_SESSION['token_csrf'])) {

            // Gera um novo token CSRF
            $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
        }

        // Retorna o token CSRF atual
        return $_SESSION['token_csrf'];
    }

    /**
     * Valida o token CSRF enviado na requisição
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    protected function validarTokenCSRF(Request $request): void
    {
//        // Obtém o token CSRF enviado na requisição
//        $tokenEnviado = $request->post('token_csrf');
//
//        // Obtém o token CSRF armazenado na sessão
//        $tokenSessao = $_SESSION['token_csrf'] ?? null;
//
//        // Verifica se o token CSRF enviado é válido
//        if (!$tokenEnviado || $tokenEnviado !== $tokenSessao) {
//            throw new Exception('CSRF token inválido.');
//        }
    }

    /**
     * Remove o token CSRF da sessão
     *
     * @return void
     */
    protected function removerTokenCSRF(): void
    {
        unset($_SESSION['token_csrf']);
    }


    // --- MÉTODOS DE RESPOSTA ---

    /**
     * Renderiza uma view e envia para o cliente
     *
     * @param string $caminhoView
     * @param array $dados
     * @param string $layout
     * @return void
     */
    protected function renderizar(string $caminhoView, array $dados = [], string $layout = 'app'): void
    {
        // Delega a renderização da view para a classe Response
        $conteudo = Response::renderizar($caminhoView, $dados, $layout);

        // Envia o conteúdo renderizado para o cliente
        echo $conteudo;
    }

    /**
     * Redireciona para uma URL específica
     *
     * @param string $url
     * @return never
     */
    protected function redirecionar(string $url, int $codigoStatus = 200): never
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
