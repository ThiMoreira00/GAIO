<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * Classe Router
 *
 * Responsável por gerenciar, mapear e despachar as rotas da aplicação.
 * Suporta parâmetros dinâmicos, grupos de rotas, middlewares e diferentes métodos HTTP.
 */
abstract class Router
{
    /**
     * Armazena todas as rotas registradas.
     * @var array<string>
     */
    protected static array $rotas = [];

    /**
     * Armazena as propriedades do grupo de rotas atual (prefixo, middleware).
     * @var array<string>
     */
    protected static array $grupoAtual = [];

    /**
     * Adiciona uma rota à coleção de rotas.
     *
     * @param string $metodo O método HTTP (GET, POST, PUT, DELETE).
     * @param string $uri A URI da rota (ex: /usuarios/{id}).
     * @param string|\Closure $acao A ação a ser executada (ex: 'UsuarioController@mostrar' ou uma função anônima).
     * @param array $opcoes Opções adicionais, como middlewares.
     */
    public static function adicionar(string $metodo, string $uri, $acao, array $opcoes = []): void
    {
        // $prefixoGrupo = self::$grupoAtual['prefixo'] ?? '';
        // $uri = rtrim($prefixoGrupo, '/') . '/' . ltrim($uri, '/');
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');

        $middlewaresGrupo = self::$grupoAtual['middleware'] ?? [];
        if (!is_array($middlewaresGrupo)) {
            $middlewaresGrupo = [$middlewaresGrupo];
        }

        $middlewaresRota = $opcoes['middleware'] ?? [];
        if (!is_array($middlewaresRota)) {
            $middlewaresRota = [$middlewaresRota];
        }

        // Remove arrays vazios que podem ter sido criados
        $middlewaresGrupo = array_filter($middlewaresGrupo);
        $middlewaresRota = array_filter($middlewaresRota);

        $middlewares = array_merge($middlewaresGrupo, $middlewaresRota);

        self::$rotas[] = [
            'metodo' => strtoupper($metodo),
            'uri' => $uri,
            'acao' => $acao,
            'middleware' => $middlewares,
        ];
    }

    /**
     * Atalho para adicionar uma rota GET.
     */
    public static function get(string $uri, $acao, array $opcoes = []): void
    {
        self::adicionar('GET', $uri, $acao, $opcoes);
    }

    /**
     * Atalho para adicionar uma rota POST.
     */
    public static function post(string $uri, $acao, array $opcoes = []): void
    {
        self::adicionar('POST', $uri, $acao, $opcoes);
    }

    /**
     * Atalho para adicionar uma rota PUT.
     */
    public static function put(string $uri, $acao, array $opcoes = []): void
    {
        self::adicionar('PUT', $uri, $acao, $opcoes);
    }

    /**
     * Atalho para adicionar uma rota DELETE.
     */
    public static function delete(string $uri, $acao, array $opcoes = []): void
    {
        self::adicionar('DELETE', $uri, $acao, $opcoes);
    }

    /**
     * Cria um grupo de rotas com propriedades compartilhadas (prefixo, middleware).
     *
     * @param array $opcoes Opções do grupo (ex: ['prefixo' => '/admin', 'middleware' => ...]).
     * @param \Closure $callback A função que define as rotas dentro do grupo.
     */
    public static function grupo(array $opcoes, \Closure $callback): void
    {
        $grupoAnterior = self::$grupoAtual;

        // $prefixoAnterior = $grupoAnterior['prefixo'] ?? '';
        // $opcoes['prefixo'] = rtrim($prefixoAnterior, '/') . '/' . ltrim($opcoes['prefixo'] ?? '', '/');

        // LÓGICA CORRIGIDA
        $middlewaresAnteriores = $grupoAnterior['middleware'] ?? [];
        if (!is_array($middlewaresAnteriores)) {
            $middlewaresAnteriores = [$middlewaresAnteriores];
        }

        $middlewaresGrupoAtual = $opcoes['middleware'] ?? [];
        if (!is_array($middlewaresGrupoAtual)) {
            $middlewaresGrupoAtual = [$middlewaresGrupoAtual];
        }

        // Remove arrays vazios que podem ter sido criados
        $middlewaresAnteriores = array_filter($middlewaresAnteriores);
        $middlewaresGrupoAtual = array_filter($middlewaresGrupoAtual);

        $opcoes['middleware'] = array_merge($middlewaresAnteriores, $middlewaresGrupoAtual);

        self::$grupoAtual = $opcoes;

        call_user_func($callback);

        self::$grupoAtual = $grupoAnterior;
    }

    /**
     * Despacha a rota correspondente à requisição atual.
     * Encontra a rota, executa os middlewares e chama a ação correspondente.
     */
    public static function despachar(): void
    {
        $uriRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriRequisicao = $uriRequisicao === '/' ? '/' : rtrim($uriRequisicao, '/');
        $metodoRequisicao = strtoupper($_SERVER['REQUEST_METHOD']);

        $resultado = self::encontrarRotaCorrespondente($uriRequisicao, $metodoRequisicao);

        if ($resultado['rotaEncontrada']) {
            self::executarAcao($resultado['rotaEncontrada'], $resultado['parametros']);
        } elseif ($resultado['metodoPermitidoParaUri']) {
            self::enviarErro(405, 'Método não permitido.');
        } else {
            self::enviarErro(404, 'Página não encontrada.');
        }
    }

    /**
     * Executa a ação de uma rota (middlewares e controller/closure).
     *
     * @param array<string> $rota A rota encontrada.
     * @param array<string> $parametros Os parâmetros extraídos da URI.
     */
    private static function executarAcao(array $rota, array $parametros): void
    {
        self::executarMiddlewares($rota['middleware']);
        $acao = $rota['acao'];

        if ($acao instanceof Closure) {
            call_user_func_array($acao, $parametros);
            return;
        }

        self::executarAcaoController($acao, $parametros);
    }

    /**
     * Encontra a rota que corresponde à URI e ao método da requisição.
     * @return array{'rotaEncontrada': ?array, 'parametros': array, 'metodoPermitidoParaUri': bool}
     */
    private static function encontrarRotaCorrespondente(string $uriRequisicao, string $metodoRequisicao): array
    {
        $rotaEncontrada = null;
        $parametros = [];
        $metodoPermitidoParaUri = false;

        foreach (self::$rotas as $rota) {
            $padraoRegex = '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $rota['uri']) . '$#';

            if (preg_match($padraoRegex, $uriRequisicao, $matches)) {
                if ($metodoRequisicao === $rota['metodo']) {
                    $rotaEncontrada = $rota;
                    $parametros = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    break;
                }
                $metodoPermitidoParaUri = true;
            }
        }
        return compact('rotaEncontrada', 'parametros', 'metodoPermitidoParaUri');
    }

    /**
     * Executa a pilha de middlewares de uma rota.
     */
    private static function executarMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware) && class_exists($middleware)) {
                (new $middleware())->executar();
            } elseif (is_object($middleware) && method_exists($middleware, 'executar')) {
                $middleware->executar();
            }
        }
    }

    /**
     * Manipula e executa uma ação de controller no formato 'Controller@metodo'.
     */
    private static function executarAcaoController(string $acao, array $parametros): void
    {
        [$nomeController, $nomeMetodo] = explode('@', $acao, 2);
        $nomeClasseController = "App\\Controllers\\{$nomeController}";

        if (!class_exists($nomeClasseController)) {
            self::enviarErro(500, "Controller '{$nomeClasseController}' não encontrado.");
            return;
        }

        $controller = new $nomeClasseController();

        if (!method_exists($controller, $nomeMetodo)) {
            self::enviarErro(500, "Método '{$nomeMetodo}' não encontrado no controller '{$nomeClasseController}'.");
            return;
        }

        $args = self::resolverArgumentosMetodo($controller, $nomeMetodo, $parametros);
        $controller->{$nomeMetodo}(...$args);
    }

    /**
     * Usa Reflection para injetar dependências e parâmetros da rota nos métodos do controller.
     */
    private static function resolverArgumentosMetodo(object $controller, string $nomeMetodo, array $parametros): array
    {
        $args = [];
        $refMetodo = new ReflectionMethod($controller, $nomeMetodo);

        foreach ($refMetodo->getParameters() as $param) {
            $tipo = $param->getType();
            if ($tipo instanceof ReflectionNamedType && !$tipo->isBuiltin() && $tipo->getName() === Request::class) {
                $args[] = new Request();
            } else if (!empty($parametros)) {
                $args[] = array_shift($parametros);
            }
        }
        return $args;
    }

    /**
     * Normaliza e mescla arrays de middlewares.
     */
    private static function normalizarEMesclarMiddlewares(mixed $a, mixed $b): array
    {
        $a = is_array($a) ? $a : ($a ? [$a] : []);
        $b = is_array($b) ? $b : ($b ? [$b] : []);
        return array_merge($a, $b);
    }

    /**
     * Envia uma resposta de erro HTTP.
     *
     * @param int $codigo O código de status HTTP.
     * @param string $mensagem A mensagem de erro.
     */
    private static function enviarErro(int $codigo, string $mensagem): void
    {
        http_response_code($codigo);
        $caminhoViewErro = __DIR__ . "/../../resources/views/erros/erro-{$codigo}.php";

        if (file_exists($caminhoViewErro)) {
            include $caminhoViewErro;
        } else {
            // Página de erro genérica
            echo "<h1>Erro {$codigo}</h1>";
            echo "<p>{$mensagem}</p>";
        }
        exit;
    }
}