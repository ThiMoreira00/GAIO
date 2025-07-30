<?php

namespace App\Core;

/**
 * Classe Response
 *
 * Encapsula a lógica para enviar respostas HTTP para o cliente.
 * É responsável por definir cabeçalhos, códigos de status e enviar o conteúdo
 * final, seja ele HTML, JSON ou um redirecionamento.
 *
 * @package App\Core
 */
class Response
{
    /**
     * Define o código de status da resposta HTTP.
     *
     * @param int $code O código de status (ex: 200, 404, 500).
     * @return void
     */
    public static function atribuirCodigoStatus(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Envia uma resposta em formato JSON.
     *
     * Define os cabeçalhos apropriados, converte os dados para JSON e
     * finaliza a execução do script.
     *
     * @param array $data Dados a serem convertidos para JSON.
     * @param int $statusCode Código de status HTTP.
     * @return never
     */
    public static function json(array $data, int $statusCode = 200): never
    {
        self::atribuirCodigoStatus($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Redireciona o cliente para uma nova URL.
     *
     * Envia o cabeçalho de 'Location' e finaliza a execução do script.
     *
     * @param string $url URL de destino.
     * @return never
     */
    public static function redirecionar(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Renderiza uma view e a retorna como uma string.
     *
     * Esta função processa o modelo da view com os dados fornecidos e o
     * encapsula em um layout (se especificado), retornando o HTML final.
     *
     * @param string $viewPath Caminho relativo para o arquivo da view (sem .php).
     * @param array $data Dados a serem extraídos e passados para a view.
     * @param string|null $layout Nome do arquivo de layout a ser usado. Null para nenhum.
     * @return string O conteúdo HTML renderizado.
     */
    public static function renderizar(string $caminhoView, array $data = [], ?string $layout = 'app'): string
    {
        // Extrai as variáveis para ficarem disponíveis na view.
        extract($data);

        $caminhoCompleto = __DIR__ . "/../../resources/views/{$caminhoView}.php";
        if (!file_exists($caminhoCompleto)) {
            self::atribuirCodigoStatus(500);
            // Em produção, seria melhor lançar uma exceção que resulta numa página de erro amigável.
            die("ERRO: View não encontrada em {$caminhoCompleto}");
        }

        // Inicia o buffer de saída para capturar o conteúdo da view.
        ob_start();
        require $caminhoCompleto;
        $conteudo = ob_get_clean();

        // Lógica para desativar o layout em rotas de autenticação.
        if (str_contains($caminhoView, 'auth/') || str_contains($caminhoView, 'erros/')) {
            $layout = null;
            $caminhoView = str_replace('auth/', '', $caminhoView);
            $caminhoView = str_replace('erros/', '', $caminhoView);
        }

        if ($layout) {
            // Se um layout for especificado, o conteúdo principal é injetado nele.
            // O layout também precisa de um buffer para ser capturado como string.
            ob_start();
            // A variável $content estará disponível dentro do arquivo de layout.

            require __DIR__ . "/../../resources/views/layouts/{$layout}.php";
            return ob_get_clean();
        }

        // Se não houver layout, retorna apenas o conteúdo da view.
        return $conteudo;
    }
}
