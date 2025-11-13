<?php

/**
 * @file Request.php
 * @description Classe-base para todos os "requests" do sistema, responsável pelas requisições com base no roteamento.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Core;

/**
 * Classe Request
 *
 * Encapsula a lógica para acessar os dados da requisição HTTP.
 *
 * @package App\Core
 */
class Request
{

    // --- ATRIBUTOS ---

    /**
     * Parâmetros GET da requisição
     * @var array|mixed 
     */
    private array $parametrosGET;

    /**
     * Parâmetros POST da requisição
     * @var array|mixed 
     */
    private array $parametrosPOST;

    /**
     * Parâmetros do servidor da requisição
     * @var array 
     */
    private array $parametrosSERVER;

    /**
     * Parâmetros da rota da requisição
     * @var array|mixed
     */
    private array $parametrosRota;
    
    /**
     * Parâmetros de arquivos da requisição
     * @var array|mixed
     */
    private array $parametrosFILES;


    // --- MÉTODOS ---

    /**
     * Construtor da classe
     *
     * @param array|null $get Parâmetros GET da requisição.
     * @param array|null $post Parâmetros POST da requisição.
     * @param array|null $server Parâmetros do servidor da requisição.
     * @param array|null $files Parâmetros de arquivos da requisição.
     * @param array $parametrosRota Parâmetros extraídos da URI pelo Router (ex: ['id' => 123]).
     */
    public function __construct(?array $get = null, ?array $post = null, ?array $server = null, ?array $files = null, array $parametrosRota = []) {
        $this->parametrosGET = $this->sanitizar($get ?? $_GET);
        $this->parametrosPOST = $this->sanitizar($post ?? $_POST);
        $this->parametrosSERVER = $server ?? $_SERVER;
        $this->parametrosFILES = $files ?? $_FILES;
        $this->parametrosRota = $this->sanitizar($parametrosRota);

        $this->parametrosGET = array_merge($this->parametrosRota, $this->parametrosGET);
    }

    /**
     * Obtém o método HTTP da requisição
     *
     * @return string
     */
    public function method(): string
    {
        return $this->server('REQUEST_METHOD');
    }

    /**
     * Obtém o URI da requisição
     *
     * @return string
     */
    public function uri(): string
    {
        return parse_url($this->server('REQUEST_URI'), PHP_URL_PATH);
    }

    /**
     * Obtém um parâmetro da requisição GET
     *
     * @param string $chave
     * @param $default
     * @return mixed|null
     */
    public function get(string $chave, $default = null): mixed
    {
        return $this->parametrosGET[$chave] ?? $default;
    }

    /**
     * Obtém um parâmetro da requisição POST
     *
     * @param string $chave
     * @param $default
     * @return mixed|null
     */
    public function post(string $chave, $default = null): mixed
    {
        return $this->parametrosPOST[$chave] ?? $default;
    }

    /**
     * Obtém um parâmetro de arquivo da requisição
     *
     * @param string $chave
     * @param $default
     * @return mixed|null
     */
    public function file(string $chave, $default = null): mixed
    {
        return $this->parametrosFILES[$chave] ?? $default;
    }

    /**
     * Obtém todos os parâmetros de arquivos da requisição
     *
     * @return array
     */
    public function files(): array
    {
        return $this->parametrosFILES;
    }

    /**
     * Obtém todos os parâmetros da requisição (GET e POST)
     *
     * @return array
     */
    public function all(): array
    {
        return array_merge($this->parametrosGET, $this->parametrosPOST);
    }

    /**
     * Obtém um parâmetro da rota da requisição
     *
     * @param string $chave
     * @param $default
     * @return mixed|null
     */
    public function parametroRota(string $chave, $default = null): mixed
    {
        return $this->parametrosRota[$chave] ?? $default;
    }

    /**
     * Obtém todos os parâmetros da rota da requisição
     *
     * @return array
     */
    public function parametrosRota(): array
    {
        return $this->parametrosRota;
    }

    /**
     * Obtém um parâmetro do servidor da requisição
     *
     * @param string $chave
     * @param $default
     * @return mixed|null
     */
    public function server(string $chave, $default = null): mixed
    {
        return $this->parametrosSERVER[$chave] ?? $default;
    }

    /**
     * Obtém o IP da requisição
     *
     * @return string
     */
    public function ip(): string
    {
        return $this->server('REMOTE_ADDR');
    }

    /**
     * Obtém a string User-Agent da requisição
     *
     * @return string|null
     */
    public function userAgent(): ?string
    {
        return $this->server('HTTP_USER_AGENT');
    }

    /**
     * Determina o navegador com base na string User-Agent
     *
     * @return string
     */
    public function navegador(): string
    {
        // Verifica se o User-Agent está definido
        $userAgent = $this->userAgent() ?? '';

        // Verifica se o User-Agent contém uma string específica
        return match (true) {
            (bool) preg_match('/Edg/i', $userAgent) => 'Edge',
            (bool) preg_match('/MSIE|Trident/i', $userAgent) => 'Internet Explorer',
            (bool) preg_match('/Firefox/i', $userAgent) => 'Firefox',
            (bool) preg_match('/OPR|Opera/i', $userAgent) => 'Opera',
            (bool) preg_match('/Chrome/i', $userAgent) => 'Chrome',
            (bool) preg_match('/Safari/i', $userAgent) => 'Safari',
            default => 'Outro'
        };
    }

    /**
     * Determina o sistema operacional com base na string User-Agent
     *
     * @return string
     */
    public function sistemaOperacional(): string
    {
        // Verifica se o User-Agent está definido
        $userAgent = $this->userAgent() ?? '';

        // Verifica se o User-Agent contém uma string específica
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
     * @param string $chave
     * @return string|null
     */
    public function obterTipoArquivo(string $chave): ?string
    {
        // Verifica se o arquivo existe e se tem um tipo
        $arquivo = $this->file($chave);

        if (!$arquivo || !isset($arquivo['type'])) {
            return null;
        }

        // Retorna o tipo MIME do arquivo
        return $arquivo['type'];
    }

    /**
     * Obtém o tamanho do arquivo enviado
     *
     * @param string $chave
     * @return int|null
     */
    public function obterTamanhoArquivo(string $chave): ?int
    {
        // Verifica se o arquivo existe e se tem um tamanho
        $arquivo = $this->file($chave);

        if (!$arquivo || !isset($arquivo['size'])) {
            return null;
        }

        // Retorna o tamanho do arquivo (em bytes)
        return $arquivo['size'];
    }

    /**
     * Obtém o nome do arquivo enviado
     *
     * @param string $chave
     * @return string|null
     */
    public function obterNomeArquivo(string $chave): ?string
    {
        // Verifica se o arquivo existe e se tem um nome
        $arquivo = $this->file($chave);

        if (!$arquivo || !isset($arquivo['name'])) {
            return null;
        }

        // Retorna o nome do arquivo
        return $arquivo['name'];
    }

    /**
     * Obtém o caminho temporário do arquivo enviado
     *
     * @param string $chave
     * @return string|null
     */
    public function obterCaminhoTemporarioArquivo(string $chave): ?string
    {
        // Verifica se o arquivo existe e se tem um caminho temporário
        $arquivo = $this->file($chave);

        if (!$arquivo || !isset($arquivo['tmp_name'])) {
            return null;
        }

        // Retorna o caminho temporário do arquivo
        return $arquivo['tmp_name'];
    }

    /**
     * Sanitiza os dados recebidos da requisição
     *
     * @param mixed $dados
     * @return mixed
     */
    private function sanitizar(mixed $dados): mixed
    {
        // Verifica se os dados são um array ou uma string
        if (is_array($dados)) {

            // Sanitiza cada elemento do array
            return array_map([$this, 'sanitizar'], $dados);
        }

        // Sanitiza a string
        if (!is_string($dados)) {
            return $dados;
        }

        // Remove tags HTML e espaços em branco
        return strip_tags(trim($dados));
    }
}
