<?php

/**
 * @file Router.php
 * @description Classe-base para controle de todos os roteamentos do sistema, responsável por mapear e despachar as requisições.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Core;

// Importação de classes
use Closure;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;

/**
 * Classe Router
 *
 * Responsável por gerenciar, mapear e despachar as rotas da aplicação.
 *
 * @package App\Core
 * @abstract
 */
abstract class Router
{

    // --- ATRIBUTOS ---

    /**
     * Armazena todas as rotas registradas
     * @var array<string>
     */
    protected static array $rotas = [];

    /**
     * Armazena as propriedades do grupo de rotas atual (prefixo, middleware)
     * @var array<string>
     */
    protected static array $grupoAtual = [];

    /**
     * Adiciona uma rota à coleção de rotas
     *
     * @param string $metodo O método HTTP (GET, POST, PUT, DELETE)
     * @param string $uri A URI da rota (ex: /usuarios/{id})
     * @param string|Closure $acao A ação a ser executada (ex: 'UsuarioController@mostrar' ou uma função anônima)
     * @param array $opcoes Opções adicionais, como middlewares
     * @return void
     */
    public static function adicionar(string $metodo, string $uri, string|Closure $acao, array $opcoes = []): void
    {

        // Sanitiza a URI removendo barra final, exceto para a rota raiz
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');

        // Obtém middlewares do grupo atual
        $middlewaresGrupo = self::$grupoAtual['middleware'] ?? [];
        if (!is_array($middlewaresGrupo)) {
            $middlewaresGrupo = [$middlewaresGrupo];
        }

        // Obtém middlewares específicos da rota
        $middlewaresRota = $opcoes['middleware'] ?? [];
        if (!is_array($middlewaresRota)) {
            $middlewaresRota = [$middlewaresRota];
        }

        // Remove arrays vazios que podem ter sido criados
        $middlewaresGrupo = array_filter($middlewaresGrupo);
        $middlewaresRota = array_filter($middlewaresRota);

        // Mescla os middlewares do grupo e da rota
        $middlewares = array_merge($middlewaresGrupo, $middlewaresRota);

        // Extrai apenas as restrições de parâmetros (exclui middleware)
        $restricoesParametros = array_filter($opcoes, function($chave) {
            return $chave !== 'middleware';
        }, ARRAY_FILTER_USE_KEY);

        // Adiciona a rota à coleção de rotas
        self::$rotas[] = [
            'metodo' => strtoupper($metodo),
            'uri' => $uri,
            'acao' => $acao,
            'middleware' => $middlewares,
            'parametros' => $restricoesParametros // Armazena APENAS as restrições de parâmetros
        ];

    }

    /**
     * Atalho para adicionar uma rota GET
     *
     * @param string $uri
     * @param string|Closure $acao
     * @param array $opcoes
     * @return void
     */
    public static function get(string $uri, string|Closure $acao, array $opcoes = []): void
    {
        self::adicionar('GET', $uri, $acao, $opcoes);
    }

    /**
     * Atalho para adicionar uma rota POST
     *
     * @param string $uri
     * @param string|Closure $acao
     * @param array $opcoes
     * @return void
     */
    public static function post(string $uri, string|Closure $acao, array $opcoes = []): void
    {
        self::adicionar('POST', $uri, $acao, $opcoes);
    }

    /**
     * Atalho para adicionar uma rota PUT
     *
     * @param string $uri
     * @param string|Closure $acao
     * @param array $opcoes
     * @return void
     */
    public static function put(string $uri, string|Closure $acao, array $opcoes = []): void
    {
        self::adicionar('PUT', $uri, $acao, $opcoes);
    }

    /**
     * Atalho para adicionar uma rota DELETE
     *
     * @param string $uri
     * @param string|Closure $acao
     * @param array $opcoes
     * @return void
     */
    public static function delete(string $uri, string|Closure $acao, array $opcoes = []): void
    {
        self::adicionar('DELETE', $uri, $acao, $opcoes);
    }

    /**
     * Cria um grupo de rotas com propriedades compartilhadas (prefixo, middleware)
     *
     * @param array $opcoes
     * @param Closure $callback
     * @return void
     */
    public static function grupo(array $opcoes, Closure $callback): void
    {
        // Armazena o grupo atual antes de modificá-lo
        $grupoAnterior = self::$grupoAtual;

        // Obtém os middlewares do grupo anterior
        $middlewaresAnteriores = $grupoAnterior['middleware'] ?? [];
        if (!is_array($middlewaresAnteriores)) {
            $middlewaresAnteriores = [$middlewaresAnteriores];
        }

        // Obtém os middlewares do grupo atual
        $middlewaresGrupoAtual = $opcoes['middleware'] ?? [];
        if (!is_array($middlewaresGrupoAtual)) {
            $middlewaresGrupoAtual = [$middlewaresGrupoAtual];
        }

        // Remove arrays vazios que podem ter sido criados
        $middlewaresAnteriores = array_filter($middlewaresAnteriores);
        $middlewaresGrupoAtual = array_filter($middlewaresGrupoAtual);

        // Mescla os middlewares anteriores com os do grupo atual
        $opcoes['middleware'] = array_merge($middlewaresAnteriores, $middlewaresGrupoAtual);

        // Define o novo grupo como atual
        self::$grupoAtual = $opcoes;

        // Executa a função que define as rotas do grupo
        call_user_func($callback);

        // Restaura o grupo anterior
        self::$grupoAtual = $grupoAnterior;

    }

    /**
     * Despacha a rota correspondente à requisição atual
     *
     * @return void
     * @throws ReflectionException
     */
    public static function despachar(): void
    {
        // Obtém e sanitiza a URI da requisição
        $uriRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriRequisicao = $uriRequisicao === '/' ? '/' : rtrim($uriRequisicao, '/');

        // Obtém o método HTTP da requisição
        $metodoRequisicao = strtoupper($_SERVER['REQUEST_METHOD']);

        // Inicializa variáveis de controle
        $rotaEncontrada = null;
        $parametros = [];
        $metodoPermitidoParaUri = false;

        // Percorre todas as rotas registradas
        foreach (self::$rotas as $rota) {
            // Converte parâmetros da rota em expressão regular considerando as restrições
            $padraoRegex = $rota['uri'];
            
            // Se há parâmetros com restrições, aplica elas
            if (!empty($rota['parametros'])) {
                foreach ($rota['parametros'] as $param => $restricao) {
                    $padraoRegex = str_replace('{' . $param . '}', '(?P<' . $param . '>' . $restricao . ')', $padraoRegex);
                }
            }
            
            // Para parâmetros sem restrições específicas, usa o padrão padrão
            $padraoRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $padraoRegex);
            $padraoRegex = '#^' . $padraoRegex . '$#';

            // Verifica correspondência entre URI e padrão da rota
            if (preg_match($padraoRegex, $uriRequisicao, $matches)) {
                // Verifica se o método HTTP corresponde
                if ($metodoRequisicao === $rota['metodo']) {
                    $rotaEncontrada = $rota;
                    // Extrai os parâmetros nomeados da URI
                    $parametros = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    break;
                }
                // Marca que a URI existe mas com método diferente
                $metodoPermitidoParaUri = true;
            }
        }

        // Processa o resultado da busca
        if ($rotaEncontrada) {
            self::executarAcao($rotaEncontrada, $parametros);
        } elseif ($metodoPermitidoParaUri) {
            // Retorna erro 405 quando o método não é permitido
            self::enviarErro(405, 'Método não permitido.');
        } else {
            // Retorna erro 404 quando a rota não existe
            self::enviarErro(404, 'Página não encontrada.');
        }
    }

    /**
     * Executa a ação de uma rota (middlewares e controller/closure).
     *
     * @param array<string> $rota A rota encontrada.
     * @param array<string> $parametros Os parâmetros extraídos da URI.
     * @throws ReflectionException
     */
    private static function executarAcao(array $rota, array $parametros): void
    {

        // Executa os middlewares associados à rota
        $middlewares = $rota['middleware'] ?? [];
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }
        
        foreach ($middlewares as $middleware) {
            // Verifica se o middleware é uma string e executa
            if (is_string($middleware)) {
                (new $middleware())->executar();
            }
            // Verifica se é um objeto com método executar e o invoca
            elseif (is_object($middleware) && method_exists($middleware, 'executar')) {
                $middleware->executar();
            }
        }

        $acao = $rota['acao'];

        // Executa a ação se for uma Closure
        if ($acao instanceof Closure) {
            call_user_func_array($acao, $parametros);
            return;
        }

        // Processa a ação se for uma string no formato 'Controller@metodo'
        if (is_string($acao)) {
            // Obtém o nome do controller e método da string
            [$nomeController, $nomeMetodo] = explode('@', $acao);
            $nomeClasseController = "App\\Controllers\\{$nomeController}";

            // Verifica se o controller existe
            if (!class_exists($nomeClasseController)) {
                self::enviarErro(500, "Controller '{$nomeClasseController}' não encontrado.");
                return;
            }

            // Instancia o controller
            $controller = new $nomeClasseController();

            // Verifica se o método existe no controller
            if (!method_exists($controller, $nomeMetodo)) {
                self::enviarErro(500, "Método '{$nomeMetodo}' não encontrado no controller '{$nomeClasseController}'.");
                return;
            }

            // Obtém informações do método via reflexão
            $reflexaoMetodo = new ReflectionMethod($controller, $nomeMetodo);
            $args = [];

            // Processa os parâmetros do método
            foreach ($reflexaoMetodo->getParameters() as $param) {
                $tipo = $param->getType();

                // Se o método espera um Request, injeta o objeto já com os parâmetros da rota
                if ($tipo && $tipo instanceof ReflectionNamedType && $tipo->getName() === Request::class) {
                    $args[] = new Request(null, null, null, null, $parametros);

                } else {
                    // continua com array_shift se for outro tipo
                    $args[] = array_shift($parametros);
                }

            }


            // Executa o método do controller com os argumentos resolvidos
            $reflexaoMetodo->invokeArgs($controller, $args);
        }
    }

    /**
     * Envia uma resposta de erro HTTP
     *
     * @param int $codigoHTTP
     * @param string $mensagem
     */
    private static function enviarErro(int $codigoHTTP, string $mensagem): void
    {
        http_response_code($codigoHTTP);
        
        // Renderiza e exibe a página de erro
        echo Response::renderizar("erros/erro-{$codigoHTTP}", ['mensagem' => $mensagem]);
        exit;
    }
}