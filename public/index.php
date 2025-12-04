<?php

// Definindo o tipo de retorno para todas as funções
declare(strict_types=1);

// Importação de classes
use Dotenv\Dotenv;
use App\Helper\NotificadorHelper;

// Define o idioma padrão para datas e horários
setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/', null, false, true);
    session_start();
}

// Define a política CSP (Content-Security-Policy) conforme ambiente
if ($_ENV['SISTEMA_AMBIENTE'] ?? 'desenvolvimento' === 'producao') {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://apis.google.com; img-src 'self' data:; style-src 'self' https://fonts.googleapis.com;");
} else {
    // Ambiente desenvolvimento: política mais aberta
    header("Content-Security-Policy: default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; script-src * 'unsafe-inline' 'unsafe-eval'; img-src * data: blob:; style-src * 'unsafe-inline';");
}

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as variáveis de ambiente do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Inicializa a conexão com o banco de dados
require_once __DIR__ . '/../config/Connection.php';

// Carrega as rotas do sistema
require_once __DIR__ . '/../routes/web.php';


// --- FUNÇÕES AUXILIARES ---

/**
 * Função auxiliar que funciona como um atalho para o NotificadorFlash
 *
 * @return NotificadorHelper
 */
function flash(): NotificadorHelper
{
    static $instancia = null;
    if ($instancia === null) {
        $instancia = new NotificadorHelper();
    }
    return $instancia;
}

/**
 * Função auxiliar para obter a URL completa do sistema
 *
 * @param string $url
 * @return string
 */
function obterURL($url)
{
    return $_ENV['SISTEMA_LINK'] . $url;
}

/**
 * Função auxiliar pra sanitizar entradas de usuário
 * 
 * @param string $input
 * @return string
 */
function sanitizar(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}