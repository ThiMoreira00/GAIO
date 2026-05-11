<?php

declare(strict_types=1);

/**
 * @file Response.php
 * @description Classe-base para todas as respostas do sistema, responsável pela geração e envio de respostas HTTP.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2026
 */

// Declaração de namespace
namespace App\Core;

// Importação de classes
use RuntimeException;

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

    // --- ATRIBUTOS ---

    /**
     * Caminho base das views
     * @var string
     */
    private static string $baseViewPath = __DIR__ . "/../../resources/views/";


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
     * Define um header HTTP
     *
     * @param string $nome
     * @param string $valor
     * @return void
     */
    public static function header(string $nome, string $valor): void
    {
        header(sprintf('%s: %s', $nome, $valor));
    }

    /**
     * Configura CORS com base em origens permitidas (seguro)
     *
     * @param array $origensPermitidas
     * @return void
     */
    public static function cors(array $origensPermitidas = []): void
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (!empty($origensPermitidas) && in_array($origin, $origensPermitidas, true)) {
            self::header('Access-Control-Allow-Origin', $origin);
            self::header('Access-Control-Allow-Credentials', 'true');
        }
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

        self::header('Content-Type', 'application/json; charset=utf-8');

        try {
            echo json_encode($dados, JSON_THROW_ON_ERROR);

        } catch (JsonException) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Erro ao gerar resposta JSON'
            ]);
        }

        exit;
    }

    /**
     * Redireciona o cliente para uma nova URL
     *
     * @param string $url
     * @param int $codigoStatus Código HTTP (padrão: 302 Found para redirecionamento básico)
     * @return never
     */
    public static function redirecionar(string $url, int $codigoStatus = 302): never
    {
        self::atribuirCodigoStatus($codigoStatus);
        self::header('Location', $url);
        exit;
    }

    /**
     * Renderiza uma view e envia ao cliente
     *
     * @param string $caminhoView
     * @param array $dados
     * @param string|null $layout
     * @return never
     */
    public static function view(string $caminhoView, array $dados = [], ?string $layout = 'app'): never
    {
        self::atribuirCodigoStatus(200);
        echo self::renderizar($caminhoView, $dados, $layout);
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
        $caminhoCompleto = sprintf('%s%s.php', self::$baseViewPath, $caminhoView);

        if (!file_exists($caminhoCompleto)) {
            throw new RuntimeException("View não encontrada: {$caminhoView}");
        }

        // Dados disponíveis na view via variáveis extraídas
        extract($dados, EXTR_SKIP);

        // Renderização da view
        ob_start();
        require $caminhoCompleto;
        $conteudo = ob_get_clean();

        if (!$layout || empty($layout)) {
            return $conteudo;
        }

        // Renderização do layout
        $caminhoLayout = self::$baseViewPath . "_layouts/{$layout}.php";

        if (!file_exists($caminhoLayout)) {
            return $conteudo;
        }

        ob_start();
        require $caminhoLayout;
        return ob_get_clean();
    }

    /**
     * Renderiza uma página de erro com base no código HTTP
     *
     * @param int $codigo
     * @return never
     */
    public static function erro(int $codigo): never
    {
        self::atribuirCodigoStatus($codigo);

        $caminho = sprintf('%s erros/erro-%d.php', self::$baseViewPath, $codigo);

        if (file_exists($caminho)) {
            require $caminho;
        } else {
            require sprintf('%s erros/erro-generico.php', self::$baseViewPath);
        }

        exit;
    }

    
    // --- HELPERS DE SEGURANÇA ---

    /**
     * Escapa saída para evitar XSS
     *
     * @param string|null $valor
     * @return string
     */
    public static function e(?string $valor): string
    {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Retorna conteúdo bruto (sem escape)
     *
     * @param string|null $valor
     * @return string
     */
    public static function raw(?string $valor): string
    {
        return $valor ?? '';
    }
}