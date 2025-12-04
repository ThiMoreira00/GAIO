<?php

declare(strict_types=1);

/**
 * @file AjaxMiddleware.php
 * @description Middleware responsável por validar se a requisição é uma API call (AJAX, Fetch, etc.)
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

namespace App\Middlewares;

use App\Core\Response;

/**
 * Classe AjaxMiddleware
 *
 * Middleware responsável por validar se a requisição foi feita via AJAX/Fetch
 * e se origina do mesmo host, para previnir CSRF.
 *
 * @package App\Middlewares
 * @readonly
 */
readonly class AjaxMiddleware
{

    // --- MÉTODOS ---

    /**
     * Executa o middleware.
     *
     * Este método verifica duas condições principais:
     * 1. Se o tipo da requisição é apropriado para uma API (checa os cabeçalhos 'X-Requested-With' e 'Accept')
     * 2. Se a origem da requisição é segura (compara 'Origin' ou 'Referer' com o 'Host' atual)
     *
     * @return void
     */
    public function executar(): void
    {
        // --- 1. Verificação do Tipo de Requisição ---

        // Verificação tradicional para bibliotecas como jQuery
        $eAjaxLegado = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Verificação moderna para APIs (ex: fetch) que pedem JSON. É mais confiável.
        $cabecalhoAccept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $eRequisicaoApi = str_contains(strtolower($cabecalhoAccept), 'application/json');

        // A requisição é válida se atender a pelo menos um dos critérios.
        $tipoRequisicaoValido = $eAjaxLegado || $eRequisicaoApi;

        // --- 2. Verificação da Origem (Segurança) ---

        $hostServidor = $_SERVER['HTTP_HOST'] ?? '';
        $origem = $_SERVER['HTTP_ORIGIN'] ?? '';
        $referencia = $_SERVER['HTTP_REFERER'] ?? '';

        // Priorizamos o cabeçalho 'Origin', que é mais seguro. Se não existir, usamos o 'Referer'
        $fonteRequisicao = !empty($origem) ? $origem : $referencia;
        $origemValida = false;

        // Extrair host do HTTP_HOST (remover porta se presente) para comparação correta
        $hostServidorHost = '';
        if (!empty($hostServidor)) {
            // Se HTTP_HOST já possui porta (ex: localhost:8080), parse_url funciona adicionando esquema
            $hostServidorHost = parse_url('http://' . $hostServidor, PHP_URL_HOST) ?: $hostServidor;
        }

        // Comparamos o host de origem com o host do servidor (ignorando porta)
        if (!empty($fonteRequisicao) && !empty($hostServidorHost)) {
            $hostDaFonte = parse_url($fonteRequisicao, PHP_URL_HOST);
            if ($hostDaFonte === $hostServidorHost) {
                $origemValida = true;
            }
        }

        // Se nenhum dos critérios (tipo de requisição válido OU origem válida) for atendido, bloqueamos o acesso
        if (!($tipoRequisicaoValido || $origemValida)) {
            // Log temporário para diagnóstico: gravar cabeçalhos relevantes e rota
            try {
                $logDir = __DIR__ . '/../../storage/tmp';
                if (!is_dir($logDir)) {
                    @mkdir($logDir, 0775, true);
                }

                $dadosLog = [
                    'timestamp' => date('c'),
                    'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                    'http_host' => $_SERVER['HTTP_HOST'] ?? null,
                    'http_origin' => $_SERVER['HTTP_ORIGIN'] ?? null,
                    'http_referer' => $_SERVER['HTTP_REFERER'] ?? null,
                    'http_accept' => $_SERVER['HTTP_ACCEPT'] ?? null,
                    'x_requested_with' => $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null,
                    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
                ];

                $logLine = json_encode($dadosLog, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
                @file_put_contents($logDir . '/ajax_middleware_rejections.log', $logLine, FILE_APPEND | LOCK_EX);
            } catch (\Throwable $e) {
                // Não fazer nada se o logging falhar
            }

            Response::json(['erro' => 'Acesso Proibido'], 403);
            exit;
        }
    }
}

