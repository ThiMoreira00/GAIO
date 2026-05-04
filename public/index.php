<?php

declare(strict_types=1);

// Desabilita o cache limiter padrão que interfere com cookies
session_cache_limiter('');

// Importação de classes
use Dotenv\Dotenv;
use App\Helper\NotificadorHelper;
use App\Services\AuthService;
use App\Core\Response;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Dotenv\Exception\InvalidPathException;

// Define o idioma padrão para datas e horários
setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');

// Define o fuso horário com base no .env (ou padrão Brasília)
date_default_timezone_set($_ENV['SISTEMA_TIMEZONE'] ?? 'America/Sao_Paulo');

require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as variáveis de ambiente do arquivo .env
try {

    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

} catch (InvalidPathException $e) {

    throw new InvalidPathException("Arquivo .env não encontrado. O sistema está em modo de instalação. Por favor, crie o arquivo .env com as configurações necessárias.");

}

// Inicializa a conexão com o banco de dados
require_once __DIR__ . '/../config/Connection.php';

$container = new Container();

$request = Request::capture();
$container->instance('request', $request);

$events = new Dispatcher($container);

$router = new Router($events, $container);

require __DIR__ . '/../routes/web.php';

try {

    $response = $router->dispatch($request);

    if ($response instanceof SymfonyResponse) {
        $response->send();
        exit;
    }

    if (is_string($response)) {
        echo $response;
        exit;
    }

    if (is_array($response) || is_object($response)) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    echo (string) $response;



} catch (\Throwable $e) {

    http_response_code(500);

    echo Response::renderizar('erros/erro-500', [
        'mensagem' => $e->getMessage()
    ]);
}


/**
 * Função auxiliar que funciona como um atalho para o NotificadorFlash
 *
 * @return NotificadorHelper
 */
function flash(): NotificadorHelper
{
    return new NotificadorHelper();
}

/**
 * Função auxiliar para obter a URL completa do sistema
 */
function obterURL(string $url): string
{
    return ($_ENV['SISTEMA_LINK'] ?? '') . $url;
}

/**
 * Função auxiliar pra sanitizar entradas de usuário
 */
function sanitizar(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
