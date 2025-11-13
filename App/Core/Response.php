<?php

/**
 * @file Response.php
 * @description Classe-base para todos os "responses" do sistema, responsável por todas as respostas e visualizações para serem exibidas ao cliente.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Core;

// Importação de classes
use Exception;

/**
 * Classe Response
 *
 * Encapsula a lógica para gerar e enviar respostas HTTP.
 *
 * @package App\Core
 * @abstract
 */
abstract class Response
{

    // --- MÉTODOS ---

    /**
     * Define o código de status da resposta HTTP
     *
     * @param int $codigo
     * @return void
     */
    public static function atribuirCodigoStatus(int $codigo): void
    {
        http_response_code($codigo);
    }

    /**
     * Envia uma resposta em formato JSON
     *
     * @param array $dados
     * @param int $codigoStatus
     * @return never
     */
    public static function json(array $dados, int $codigoStatus = 200): never
    {
        self::atribuirCodigoStatus($codigoStatus);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados);
        exit;
    }

    /**
     * Redireciona o cliente para uma nova URL
     *
     * @param string $url
     * @return never
     */
    public static function redirecionar(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Renderiza uma view e a retorna como uma string HTML
     *
     * @param string $caminhoView
     * @param array $dados
     * @param string|null $layout
     * @return string
     */
    public static function renderizar(string $caminhoView, array $dados = [], ?string $layout = 'app'): string
    {

        try {
            // Extrai as variáveis para ficarem disponíveis na view
            extract($dados);

            // Verifica se a view existe
            $caminhoCompleto = __DIR__ . "/../../resources/views/{$caminhoView}.php";

            if (!file_exists($caminhoCompleto)) {
                self::atribuirCodigoStatus(500);
                throw new Exception("Visualização '{$caminhoView}' não encontrada.");
            }

            // Inicia o buffer de saída para capturar o conteúdo da view
            ob_start();
            require $caminhoCompleto;
            $conteudo = ob_get_clean();

            // Lógica para desativar o layout em rotas de autenticação
            if (str_contains($caminhoView, 'auth/') || str_contains($caminhoView, 'erros/')) {
                $layout = null;
                $caminhoView = str_replace('auth/', '', $caminhoView);
                $caminhoView = str_replace('erros/', '', $caminhoView);
            }

            // Se houver layout, inclui o layout e retorna o conteúdo da view
            if ($layout) {
                ob_start();
                require __DIR__ . "/../../resources/views/layouts/{$layout}.php";
                return ob_get_clean();
            }

            // Se não houver layout, retorna apenas o conteúdo da view
            return $conteudo;

        } catch (Exception $exception) {

            // Lógica para tratamento de erros
            self::atribuirCodigoStatus(500);
            self::renderizar('erros/erro-500', ['mensagem' => $exception->getMessage()]);
            exit;
        }
    }
}
