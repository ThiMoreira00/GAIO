<?php

declare(strict_types=1);

/**
 * @file AutenticacaoMiddleware.php
 * @description Middleware responsável por validar os trâmites de autenticação
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Middlewares;

// Importação de classes
use App\Core\Response;
use App\Services\AutenticacaoService;

/**
 * Classe AutenticacaoMiddleware
 *
 * Middleware responsável por validar os trâmites de autenticação
 *
 * @package App\Middlewares
 * @readonly
 */
readonly class AutenticacaoMiddleware
{

    // --- ATRIBUTOS ---

    /**
     * Instância do serviço de autenticação
     * @var AutenticacaoService
     */
    private AutenticacaoService $autenticacaoService;


    // --- MÉTODOS ---

    /**
     * Construtor da classe
     *
     * @return void
     */
    public function __construct()
    {
        $this->autenticacaoService = new AutenticacaoService();
    }

    /**
     * Executa o middleware
     *
     * @return void
     */
    public function executar(): void
    {
        // Verifica se o usuário está autenticado
        if (!$this->autenticacaoService->estaAutenticado()) {
            Response::redirecionar('/login');
        }
    }
}
