<?php

namespace App\Core;

use App\Core\Request;

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
     * @var array
     */
    protected static array $rotas = [];

    /**
     * Armazena as propriedades do grupo de rotas atual (prefixo, middleware).
     * @var array
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

        $rotaEncontrada = null;
        $parametros = [];
        $metodoPermitidoParaUri = false;

        foreach (self::$rotas as $rota) {
            // Converte a URI da rota em uma expressão regular para capturar parâmetros
            // Ex: /usuarios/{id} se torna /usuarios/([a-zA-Z0-9_]+)
            $padraoRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $rota['uri']);
            $padraoRegex = '#^' . $padraoRegex . '$#';

            // Verifica se a URI da requisição casa com o padrão da rota
            if (preg_match($padraoRegex, $uriRequisicao, $matches)) {
                // Se a URI casou, verifica se o método HTTP está correto
                if ($metodoRequisicao === $rota['metodo']) {
                    $rotaEncontrada = $rota;
                    // Filtra para obter apenas os parâmetros nomeados (ex: 'id' => '123')
                    $parametros = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    break; // Para o loop ao encontrar a rota correta
                }
                // Se a URI casou, mas o método não, marca para possível erro 405
                $metodoPermitidoParaUri = true;
            }
        }

        if ($rotaEncontrada) {
            self::executarAcao($rotaEncontrada, $parametros);
        } elseif ($metodoPermitidoParaUri) {
            // A URI existe, mas o método não é permitido
            self::enviarErro(405, 'Método não permitido.');
        } else {
            // Nenhuma rota casou com a URI
            self::enviarErro(404, 'Página não encontrada.');
        }
    }

    /**
     * Executa a ação de uma rota (middlewares e controller/closure).
     *
     * @param array $rota A rota encontrada.
     * @param array $parametros Os parâmetros extraídos da URI.
     */
    private static function executarAcao(array $rota, array $parametros): void
    {
        // Executa middlewares
        foreach ($rota['middleware'] as $middleware) {

            if (is_string($middleware)) {
                (new $middleware)->executar();
            } elseif (is_object($middleware) && method_exists($middleware, 'executar')) {
                $middleware->executar();
            }
        }

        $acao = $rota['acao'];

        if ($acao instanceof \Closure) {
            call_user_func_array($acao, $parametros);
            return;
        }

        if (is_string($acao)) {
            [$nomeController, $nomeMetodo] = explode('@', $acao);
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

            // ====> Aqui fazemos a reflexão para injetar o Request
            $refMetodo = new \ReflectionMethod($controller, $nomeMetodo);
            $args = [];

            foreach ($refMetodo->getParameters() as $param) {
                $tipo = $param->getType();
                if ($tipo && $tipo instanceof \ReflectionNamedType && $tipo->getName() === \App\Core\Request::class) {
                    $args[] = new Request(); // Cria a instância do Request
                } else {
                    // Assume que os outros são parâmetros de rota
                    $args[] = array_shift($parametros);
                }
            }

            // Executa o método com os argumentos resolvidos
            $refMetodo->invokeArgs($controller, $args);
        }
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
        $caminhoViewErro = __DIR__ . "/../../resources/views/erros/erro-$codigo.php";

        if (file_exists($caminhoViewErro)) {
            include $caminhoViewErro;
        } else {
            // Página de erro genérica
            echo "<h1>Erro $codigo</h1>";
            echo "<p>$mensagem</p>";
        }
        // Verifica se existe alguma view com
        exit;
    }
}


//namespace App\Core;
//
///**
// * Classe de Roteamento Avançada
// *
// * Gerencia as rotas da aplicação, suportando grupos aninhados, prefixos e middlewares.
// * O roteador mapeia uma URI e um método HTTP para uma ação de um Controller.
// */
//class Router
//{
//    /**
//     * Armazena todas as rotas registradas.
//     * A estrutura é: ['/uri']['GET'] = ['controller' => ..., 'action' => ..., 'middlewares' => [...]].
//     * @var array
//     */
//    private static $rotas = [];
//
//    /**
//     * Pilha de prefixos para grupos de rotas.
//     * Usamos uma pilha para suportar grupos aninhados.
//     * @var array
//     */
//    private static $pilhaPrefixos = [];
//
//    /**
//     * Pilha de middlewares para grupos de rotas.
//     * @var array
//     */
//    private static $pilhaMiddlewares = [];
//
//    /**
//     * Namespace base para os controllers.
//     * @var string
//     */
//    private static $namespace = 'App\\Controllers\\';
//
//    /**
//     * Adiciona uma rota à coleção. Este é o método base usado por get(), post(), etc.
//     *
//     * @param string $metodo O método HTTP (GET, POST, PUT, DELETE).
//     * @param string $uri A URI da rota.
//     * @param string|callable $acao Ação no formato 'Controller@metodo' ou uma Closure.
//     */
//    private static function adicionarRota(string $metodo, string $uri, $acao): void
//    {
//        // Constrói o prefixo completo a partir da pilha
//        $prefixo = implode('', self::$pilhaPrefixos);
//
//        // Garante que a URI final não tenha barras duplas e remove a barra final, se houver.
//        $uriFinal = rtrim($prefixo . '/' . ltrim($uri, '/'), '/');
//        $uriFinal = $uriFinal === '' ? '/' : $uriFinal;
//
//        // Concatena os middlewares da pilha
//        $middlewares = array_merge(...self::$pilhaMiddlewares);
//
//        // Resolve a ação
//        if (is_string($acao)) {
//            list($controller, $action) = explode('@', $acao, 2);
//        } else {
//            // Se for uma Closure, não há controller/action definidos
//            $controller = $acao;
//            $action = null;
//        }
//
//        self::$rotas[$uriFinal][$metodo] = [
//            'controller' => $controller,
//            'action' => $action,
//            'middlewares' => $middlewares
//        ];
//    }
//
//    // Métodos públicos para registrar rotas
//    public static function get(string $uri, $acao): void
//    {
//        self::adicionarRota('GET', $uri, $acao);
//    }
//
//    public static function post(string $uri, $acao): void
//    {
//        self::adicionarRota('POST', $uri, $acao);
//    }
//
//    public static function put(string $uri, $acao): void
//    {
//        self::adicionarRota('PUT', $uri, $acao);
//    }
//
//    public static function delete(string $uri, $acao): void
//    {
//        self::adicionarRota('DELETE', $uri, $acao);
//    }
//
//    /**
//     * Agrupa rotas sob um conjunto de atributos comuns (prefixo, middleware).
//     *
//     * @param array $opcoes Opções como ['prefixo' => '...', 'middleware' => ...].
//     * @param callable $callback A função que define as rotas dentro do grupo.
//     */
//    public static function grupo(array $opcoes, callable $callback): void
//    {
//        // Adiciona o prefixo atual à pilha
//        $prefixo = isset($opcoes['prefixo']) ? '/' . trim($opcoes['prefixo'], '/') : '';
//        self::$pilhaPrefixos[] = $prefixo;
//
//        // Adiciona os middlewares atuais à pilha
//        $middleware = isset($opcoes['middleware']) ? (is_array($opcoes['middleware']) ? $opcoes['middleware'] : [$opcoes['middleware']]) : [];
//        self::$pilhaMiddlewares[] = $middleware;
//
//        // Executa o callback para registrar as rotas do grupo
//        call_user_func($callback);
//
//        // Remove o último prefixo e middleware da pilha para restaurar o estado anterior
//        array_pop(self::$pilhaPrefixos);
//        array_pop(self::$pilhaMiddlewares);
//    }
//
//    /**
//     * Procura a rota correspondente à requisição atual e a executa.
//     * Este é o coração do roteador.
//     */
//    public static function despachar(): void
//    {
//        $uriRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//        $uriRequisicao = $uriRequisicao === '' || $uriRequisicao === '/' ? '/' : rtrim($uriRequisicao, '/');
//        $metodoRequisicao = strtoupper($_SERVER['REQUEST_METHOD']);
//
//        $rotaEncontrada = null;
//        $parametros = [];
//        $metodoPermitidoParaUri = false;
//
//        foreach (self::$rotas as $uriPadrao => $metodos) {
//            $padraoRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $uriPadrao);
//            $padraoRegex = '#^' . $padraoRegex . '$#';
//
//            if (preg_match($padraoRegex, $uriRequisicao, $matches)) {
//                if (isset($metodos[$metodoRequisicao])) {
//                    $rotaEncontrada = $metodos[$metodoRequisicao];
//                    $parametros = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
//                    break;
//                }
//                $metodoPermitidoParaUri = true;
//            }
//        }
//
//        if ($rotaEncontrada) {
//            self::executarRota($rotaEncontrada, $parametros);
//        } elseif ($metodoPermitidoParaUri) {
//            self::enviarErro(405, 'Método não permitido para esta URI.');
//        } else {
//            self::enviarErro(404, 'Página não encontrada.');
//        }
//    }
//
//    /**
//     * Executa a ação da rota, incluindo middlewares.
//     *
//     * @param array $rota Os dados da rota (controller, action, middlewares).
//     * @param array $parametros Os parâmetros extraídos da URI.
//     */
//    private static function executarRota(array $rota, array $parametros): void
//    {
//        // 1. Executar Middlewares
//        foreach ($rota['middlewares'] as $middleware) {
//            // Assumimos que o middleware tem um método 'handle'
//            if (method_exists($middleware, 'handle')) {
//                $podeContinuar = call_user_func([$middleware, 'handle']);
//                if (!$podeContinuar) {
//                    // O middleware interrompeu a requisição (ex: redirecionou ou exibiu erro)
//                    return;
//                }
//            }
//        }
//
//        // 2. Executar a Ação Principal (Controller ou Closure)
//        $controller = $rota['controller'];
//        $action = $rota['action'];
//
//        if (is_callable($controller)) {
//            // Se for uma Closure, apenas a invocamos
//            call_user_func_array($controller, $parametros);
//            return;
//        }
//
//        $nomeCompletoController = self::$namespace . $controller;
//
//        if (!class_exists($nomeCompletoController)) {
//            self::enviarErro(500, "Controller '{$nomeCompletoController}' não encontrado.");
//            return;
//        }
//
//        $instanciaController = new $nomeCompletoController();
//
//        if (!method_exists($instanciaController, $action)) {
//            self::enviarErro(500, "Método '{$action}' não encontrado no controller '{$nomeCompletoController}'.");
//            return;
//        }
//
//        call_user_func_array([$instanciaController, $action], $parametros);
//    }
//
//    /**
//     * Envia uma resposta de erro HTTP.
//     *
//     * @param int $codigo O código de status HTTP.
//     * @param string $mensagem A mensagem de erro.
//     */
//    private static function enviarErro(int $codigo, string $mensagem): void
//    {
//        http_response_code($codigo);
//        // Em um sistema real, você renderizaria uma view de erro.
//        echo "<h1>Erro {$codigo}</h1><p>{$mensagem}</p>";
//        // Você pode adicionar um die() aqui se quiser garantir que nada mais seja executado.
//        // die();
//    }
//}
