<?php

declare(strict_types=1);

/**
 * @file AcessoMiddleware.php
 * @description Middleware responsável por verificar se o usuário tem acesso a uma rota específica com base em permissões. Suporta múltiplas regras (OR) e wildcard (*).
 * @author Thiago Moreira
 * @copyright Copyright (c) 2026
 */

// Declaração de namespace
namespace App\Middlewares;

// Importação de classes
use App\Services\AuthService;
use App\Core\Response;
use Closure;

/**
 * Classe AcessoMiddleware
 *
 * Verifica se o usuário autenticado possui pelo menos uma das permissões exigidas.
 *
 * Suporta:
 *  - OR de permissões: turma.visualizar,turma.editar
 *  - Wildcard: turma.*, turma.avaliacoes.*
 *  - Permissões diretas: GAIO_TURMA_VISUALIZAR
 *
 * Exemplos:
 *  - ->middleware('AcessoMiddleware:turma.visualizar')
 *  - ->middleware('AcessoMiddleware:turma.visualizar,turma.editar')
 *  - ->middleware('AcessoMiddleware:turma.*')
 *  - ->middleware('AcessoMiddleware:GAIO_TURMA_VISUALIZAR')
 *
 * @package App\Middlewares
 */
class AcessoMiddleware
{
    /**
     * Executa o middleware
     *
     * @param mixed $request
     * @param Closure $next
     * @param string ...$regras
     * @return mixed
     */
    public function handle($request, Closure $next, string ...$regras)
    {
        // Obtém permissões do usuário autenticado
        $permissoesUsuario = AuthService::obterPermissoes();

        // Se não houver regras definidas, permite acesso
        if (empty($regras)) {
            return $next($request);
        }

        // Percorre cada regra (OR)
        foreach ($regras as $regra) {

            // Normaliza regra para padrão interno
            $regraNormalizada = $this->normalizarPermissao($regra);

            // Verifica contra todas permissões do usuário
            foreach ($permissoesUsuario as $permissaoUsuario) {

                if ($this->matchPermissao($regraNormalizada, $permissaoUsuario)) {
                    return $next($request);
                }
            }
        }

        // Se nenhuma regra for atendida → nega acesso
        return $this->negarAcesso();
    }

    /**
     * Verifica se uma permissão do usuário atende à regra (com suporte a wildcard)
     *
     * @param string $regra
     * @param string $permissaoUsuario
     * @return bool
     */
    private function matchPermissao(string $regra, string $permissaoUsuario): bool
    {
        // Sem wildcard → comparação direta
        if (!str_contains($regra, '*')) {
            return $regra === $permissaoUsuario;
        }

        // Converte wildcard (*) para regex
        $pattern = str_replace('\*', '.*', preg_quote($regra, '/'));

        return (bool) preg_match('/^' . $pattern . '$/', $permissaoUsuario);
    }

    /**
     * Converte regra para padrão interno (GAIO_*)
     *
     * Suporta:
     *  - turma.visualizar → GAIO_TURMA_VISUALIZAR
     *  - turma.* → GAIO_TURMA_*
     *  - GAIO_TURMA_VISUALIZAR → mantém
     *
     * @param string $regra
     * @return string
     */
    private function normalizarPermissao(string $regra): string
    {
        $regra = strtoupper(trim($regra));

        // Se já está no padrão interno
        if (str_starts_with($regra, 'GAIO_')) {
            return $regra;
        }

        // Preserva wildcard temporariamente
        $regra = str_replace('*', '__WILDCARD__', $regra);

        // Converte separadores "." para "_"
        $partes = explode('.', $regra);

        $regra = 'GAIO_' . implode('_', array_map('strtoupper', $partes));

        // Restaura wildcard
        return str_replace('__WILDCARD__', '*', $regra);
    }

    /**
     * Negar acesso padrão
     *
     * @return void
     */
    private function negarAcesso(): void
    {
        Response::redirecionar('/403');
        exit;
    }
}