<?php

declare(strict_types=1);

/**
 * @file AuthMiddleware.php
 * @description Middleware responsável por validar os trâmites de autenticação via JWT
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Middlewares;

// Importação de classes
use App\Core\Response;
use App\Services\AuthService;
use Closure;

/**
 * Classe AuthMiddleware
 *
 * Middleware responsável por validar os trâmites de autenticação.
 * Inicializa o JWT e verifica se o usuário está autenticado.
 *
 * @package App\Middlewares
 * @readonly
 */
readonly class AuthMiddleware
{

    // --- MÉTODOS ---

    /**
     * Executa o middleware.
     * Inicializa o JWT a partir do cookie e verifica autenticação.
     *
     * @param mixed $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        // Inicializa o JWT a partir do cookie (decodifica e cache em memória)
        AuthService::inicializarJWT();

        // Verifica se o usuário está autenticado
        if (!AuthService::estaAutenticado()) {
            Response::redirecionar('/login');
        }

        return $next($request);
    }
}
