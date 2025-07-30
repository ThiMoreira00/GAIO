<?php

namespace App\Core;

/**
 * Classe Request
 *
 * Encapsula a requisição HTTP, fornecendo uma API limpa para acessar
 * dados de GET, POST, SERVER e parâmetros da rota.
 * Realiza a sanitização automática dos dados de entrada.
 */
class Request
{
    private array $getParams;
    private array $postParams;
    private array $serverParams;
    private array $routeParams;
    private array $fileParams;

    /**
     * @param array $routeParams Parâmetros extraídos da URI pelo Router (ex: ['id' => 123]).
     */
    public function __construct(
        array $get = null,
        array $post = null,
        array $server = null,
        array $files = null,
        array $routeParams = []
    ) {
        $this->getParams = $this->sanitizar($get ?? $_GET);
        $this->postParams = $this->sanitizar($post ?? $_POST);
        $this->serverParams = $server ?? $_SERVER;
        $this->fileParams = $files ?? $_FILES;
        $this->routeParams = $this->sanitizar($routeParams);
    }


    public function method(): string
    {
        return $this->server('REQUEST_METHOD');
    }

    public function uri(): string
    {
        return parse_url($this->server('REQUEST_URI'), PHP_URL_PATH);
    }

    public function get(string $chave, $default = null)
    {
        return $this->getParams[$chave] ?? $default;
    }

    public function post(string $chave, $default = null)
    {
        return $this->postParams[$chave] ?? $default;
    }

    public function file(string $chave, $default = null)
    {
        return $this->fileParams[$chave] ?? $default;
    }

    public function files(): array
    {
        return $this->fileParams;
    }

    public function all(): array
    {
        return array_merge($this->getParams, $this->postParams);
    }

    public function parametroRota(string $chave, $default = null)
    {
        return $this->routeParams[$chave] ?? $default;
    }

    public function parametrosRota(): array
    {
        return $this->routeParams;
    }

    public function server(string $chave, $default = null)
    {
        return $this->serverParams[$chave] ?? $default;
    }

    public function ip()
    {
        return $this->server('REMOTE_ADDR');
    }

    public function userAgent(): ?string
    {
        return $this->server('HTTP_USER_AGENT');
    }

    /**
     * Determina o navegador com base na string User-Agent.
     *
     * @return $string
     */
    public function navegador(): string
    {
        $userAgent = $this->userAgent() ?? '';

        return match (true) {
            (bool) preg_match('/Edg/i', $userAgent) => 'Edge',
            (bool) preg_match('/MSIE|Trident/i', $userAgent) => 'Internet Explorer',
            (bool) preg_match('/Firefox/i', $userAgent) => 'Firefox',
            (bool) preg_match('/OPR|Opera/i', $userAgent) => 'Opera',
            (bool) preg_match('/Chrome/i', $userAgent) => 'Chrome',
            (bool) preg_match('/Safari/i', $userAgent) => 'Safari',
            default => 'Outro',
        };
    }

    /**
     * Determina o sistema operacional com base na string User-Agent.
     *
     * @return string
     */
    public function sistemaOperacional(): string
    {
        $userAgent = $this->userAgent() ?? '';

        return match (true) {
            (bool) preg_match('/windows|win32/i', $userAgent) => 'Windows',
            (bool) preg_match('/macintosh|mac os x/i', $userAgent) => 'macOS',
            (bool) preg_match('/linux/i', $userAgent) => 'Linux',
            (bool) preg_match('/android/i', $userAgent) => 'Android',
            (bool) preg_match('/iphone|ipad|ipod/i', $userAgent) => 'iOS',
            default => 'Outro',
        };
    }

    /**
     * Obtém o tipo MIME do arquivo enviado
     *
     * @param string $chave Nome do campo do arquivo no formulário
     * @return string|null Retorna o tipo MIME do arquivo ou null se não existir
     */
    public function obterTipo(string $chave): ?string
    {
        $arquivo = $this->file($chave);

        if (!$arquivo || !isset($arquivo['type'])) {
            return null;
        }

        return $arquivo['type'];
    }

    public function obterTamanho(string $chave): ?int
    {
        $arquivo = $this->file($chave);

        if (!$arquivo || !isset($arquivo['size'])) {
            return null;
        }

        return $arquivo['size'];
    }

    private function sanitizar(mixed $dados): mixed
    {
        if (is_array($dados)) {
            return array_map([$this, 'sanitizar'], $dados);
        }
        if (!is_string($dados)) {
            return $dados;
        }
        return strip_tags(trim($dados));
    }
}
