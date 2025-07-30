<?php

namespace App\Core;

use Exception;
use Random\RandomException;

/**
 * Classe abstrata Controller
 *
 * Classe base para todos os controllers da aplicação. Responsável por
 * controlar o fluxo da requisição, aplicar regras de segurança como CSRF
 * e orquestrar a geração da resposta, delegando a construção final
 * para a classe Response.
 *
 * @package App\Core
 */
abstract class Controller
{

    /**
     * Gera um token CSRF.
     * (Responsabilidade do Controller, pois está ligado à segurança da sessão/formulário)
     *
     * @return string
     * @throws RandomException
     */
    protected function gerarTokenCSRF(): string
    {
        if (empty($_SESSION['token_csrf'])) {
            $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['token_csrf'];
    }

    /**
     * Valida o token CSRF enviado na requisição.
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    protected function validarTokenCSRF(Request $request): void
    {
        $tokenEnviado = $request->post('token_csrf');
        $tokenSessao = $_SESSION['token_csrf'] ?? null;

        if (!$tokenEnviado) {
            throw new Exception('CSRF token inválido.');
        }

        if ($tokenEnviado !== $tokenSessao) {
            throw new Exception('CSRF token inválido');
        }
    }

    /**
     * Remove o token CSRF da sessão.
     *
     * @return void
     */
    protected function removerTokenCSRF(): void
    {
        unset($_SESSION['token_csrf']);
    }

    /**
     * Sanitiza uma string para evitar XSS.
     *
     * @param string $string
     * @return string
     */


    // --- MÉTODOS DE RESPOSTA ---

    /**
     * Renderiza uma view e envia para o cliente.
     * Orquestra a renderização e o cache (se aplicável).
     *
     * @param string $caminhoView Caminho relativo para o arquivo da view.
     * @param array $dados Dados a serem passados para a view.
     * @param string $layout Nome do arquivo de layout a ser usado.
     * @return void
     */
    protected function renderizar(string $caminhoView, array $dados = [], string $layout = 'app'): void
    {
        // 1. Delega a renderização da view para a classe Response.
        $conteudo = Response::renderizar($caminhoView, $dados, $layout);

        // 3. Envia o conteúdo final para o cliente.
        echo $conteudo;
    }

    /**
     * Redireciona para uma URL específica.
     * (Wrapper para o método da classe Response)
     *
     * @param string $url URL de destino.
     * @return never
     */
    protected function redirecionar(string $url): never
    {
        Response::redirecionar($url);
    }

    /**
     * Envia resposta em formato JSON.
     * (Wrapper para o método da classe Response)
     *
     * @param mixed $dados Dados a serem convertidos para JSON.
     * @param int $codigoHttp Código de status HTTP.
     * @return never
     */
    protected function responderJson(mixed $dados, int $codigoHttp = 200): never
    {
        Response::json($dados, $codigoHttp);
    }
}