<?php

declare(strict_types=1);

/**
 * @file ControleAcessoMiddleware.php
 * @description Middleware responsável por validar as permissões de acesso
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Middlewares;

// Importação de classes
use App\Core\Response;
use App\Models\Grupo;
use InvalidArgumentException;

/**
 * Classe ControleAcessoMiddleware
 *
 * Valida o acesso às rotas:
 * - Apenas por tipo de conta
 * - Apenas por permissões específicas
 * - Por uma combinação de tipo de conta e permissões
 * - Múltiplos perfis de acesso para a mesma rota
 *
 * @package App\Middlewares
 * @readonly
 */
readonly class ControleAcessoMiddleware
{

    // --- PROPRIEDADES ---

    /**
     * @var string|array|null
     */
    private string|array|null $tipoContaExigido;

    /**
     * @var array<string>|null
     */
    private ?array $permissoesExigidas;

    /**
     * @var string
     */
    private string $modoPermissao;

    /**
     * @var bool
     */
    private bool $multiplosPerfis;

    // --- MÉTODOS ---

    /**
     * Construtor da classe
     *
     * Aceita dois formatos:
     * 1. Formato simples (compatibilidade): tipo_conta (string), permissões, modo
     *    new ControleAcessoMiddleware('ADMINISTRADOR')
     *    new ControleAcessoMiddleware('ALUNO', ['visualizar_turmas'])
     *
     * 2. Formato múltiplos perfis: array de perfis
     *    new ControleAcessoMiddleware([
     *        ['tipo_conta' => 'ALUNO'],
     *        ['tipo_conta' => 'PROFESSOR'],
     *        ['tipo_conta' => 'ADMINISTRADOR', 'permissoes' => ['gerenciar_turmas']]
     *    ])
     *
     * @param string|array|null $tipoContaExigido String para perfil único, Array para múltiplos perfis
     * @param array<string>|null $permissoesExigidas
     * @param string $modoPermissao
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(string|array|null $tipoContaExigido = null, ?array $permissoesExigidas = null, string $modoPermissao = 'TODAS') {
        // Detecta se é múltiplos perfis
        $this->multiplosPerfis = is_array($tipoContaExigido) && isset($tipoContaExigido[0]) && is_array($tipoContaExigido[0]);

        if ($this->multiplosPerfis) {
            // Formato novo: array de perfis
            $this->validarMultiplosPerfis($tipoContaExigido);
            $this->tipoContaExigido = $tipoContaExigido;
            $this->permissoesExigidas = null;
            $this->modoPermissao = 'TODAS';
        } else {
            // Formato antigo: compatibilidade
            $this->tipoContaExigido = $tipoContaExigido;
            $this->permissoesExigidas = $permissoesExigidas;
            $this->modoPermissao = $modoPermissao;

            // Valida o modo de permissão para evitar erros
            if (!in_array(strtoupper($this->modoPermissao), ['TODAS', 'QUALQUER'])) {
                throw new InvalidArgumentException("Modo de permissão inválido. Use 'TODAS' ou 'QUALQUER'.");
            }

            // Verifica se as permissões exigidas são um array
            if ($this->permissoesExigidas !== null && !is_array($this->permissoesExigidas)) {
                throw new InvalidArgumentException("Permissões exigidas devem ser um array.");
            }
        }
    }

    /**
     * Valida múltiplos perfis de acesso
     *
     * @param array $perfis
     * @return void
     * @throws InvalidArgumentException
     */
    private function validarMultiplosPerfis(array $perfis): void
    {
        if (empty($perfis)) {
            throw new InvalidArgumentException("É necessário definir ao menos um perfil de acesso.");
        }

        foreach ($perfis as $index => $perfil) {
            if (!is_array($perfil)) {
                throw new InvalidArgumentException("Cada perfil de acesso deve ser um array.");
            }

            if (empty($perfil['tipo_conta']) && empty($perfil['permissoes'])) {
                throw new InvalidArgumentException("Perfil #{$index}: é necessário definir 'tipo_conta' ou 'permissoes'.");
            }

            if (isset($perfil['permissoes']) && !is_array($perfil['permissoes'])) {
                throw new InvalidArgumentException("Perfil #{$index}: 'permissoes' deve ser um array.");
            }

            if (isset($perfil['modo']) && !in_array(strtoupper($perfil['modo']), ['TODAS', 'QUALQUER'])) {
                throw new InvalidArgumentException("Perfil #{$index}: modo inválido. Use 'TODAS' ou 'QUALQUER'.");
            }
        }
    }

    /**
     * Executa a lógica do middleware
     *
     * @return bool Retorna true se o acesso foi permitido, false caso contrário
     */
    public function executar(): bool
    {
        if ($this->multiplosPerfis) {
            return $this->executarMultiplosPerfis();
        } else {
            return $this->executarPerfilSimples();
        }
    }

    /**
     * Executa validação para perfil simples (modo compatibilidade)
     *
     * @return bool
     */
    private function executarPerfilSimples(): bool
    {
        if (!$this->verificarTipoDeConta()) {
            return false;
        }
        if (!$this->verificarPermissoes()) {
            return false;
        }
        return true;
    }

    /**
     * Executa validação para múltiplos perfis
     *
     * @return bool
     */
    private function executarMultiplosPerfis(): bool
    {
        $gruposDoUsuario = $_SESSION['usuario_grupos'] ?? [];
        $permissoesDoUsuario = $_SESSION['usuario_permissoes'] ?? [];

        // Tenta atender cada perfil de acesso
        foreach ($this->tipoContaExigido as $perfil) {
            if ($this->perfilAtendido($perfil, $gruposDoUsuario, $permissoesDoUsuario)) {
                // Armazena o perfil atendido na sessão para uso posterior
                $_SESSION['perfil_acesso_atual'] = [
                    'tipo_conta' => $perfil['tipo_conta'] ?? null,
                    'permissoes' => $perfil['permissoes'] ?? null
                ];
                return true;
            }
        }

        // Nenhum perfil foi atendido
        return false;
    }

    /**
     * Verifica se o usuário tem o tipo de conta exigido
     *
     * @return bool
     */
    private function verificarTipoDeConta(): bool
    {
        if ($this->tipoContaExigido !== null &&
            !in_array(strtoupper($this->tipoContaExigido), $_SESSION['usuario_grupos'], true)) {
            return false;
        }
        return true;
    }

    /**
     * Valida se o usuário possui as permissões necessárias
     *
     * @return bool
     */
    private function verificarPermissoes(): bool
    {
        // Se não houver permissões exigidas, não faz nada
        if ($this->permissoesExigidas === null) {
            return true;
        }

        // Verifica se o usuário tem TODAS as permissões exigidas
        $permissoesDoUsuario = $_SESSION['usuario_permissoes'] ?? [];
        $possuiPermissao = strtoupper($this->modoPermissao) === 'TODAS'
            ? $this->usuarioTemTodasAsPermissoes($permissoesDoUsuario)
            : $this->usuarioTemQualquerPermissao($permissoesDoUsuario);

        // Retorna se possui as permissões
        return $possuiPermissao;
    }

    /**
     * Verifica se o usuário tem TODAS as permissões exigidas
     *
     * @param array<string> $permissoesDoUsuario
     * @return bool
     */
    private function usuarioTemTodasAsPermissoes(array $permissoesDoUsuario): bool
    {
        $permissoesFaltantes = array_diff($this->permissoesExigidas, $permissoesDoUsuario);
        return count($permissoesFaltantes) === 0;
    }

    /**
     * Verifica se o usuário tem PELO MENOS UMA das permissões exigidas
     *
     * @param array<string> $permissoesDoUsuario
     * @return bool
     */
    private function usuarioTemQualquerPermissao(array $permissoesDoUsuario): bool
    {
        $permissoesEncontradas = array_intersect($this->permissoesExigidas, $permissoesDoUsuario);
        return count($permissoesEncontradas) > 0;
    }

    /**
     * Verifica se um perfil específico é atendido pelo usuário
     *
     * @param array{tipo_conta?: string, permissoes?: array<string>, modo?: string} $perfil
     * @param array<string> $gruposDoUsuario
     * @param array<string> $permissoesDoUsuario
     * @return bool
     */
    private function perfilAtendido(array $perfil, array $gruposDoUsuario, array $permissoesDoUsuario): bool
    {
        // Verifica tipo de conta (se exigido)
        if (isset($perfil['tipo_conta'])) {
            $tipoContaValido = in_array(strtoupper($perfil['tipo_conta']), $gruposDoUsuario, true);
            if (!$tipoContaValido) {
                return false;
            }
        }

        // Verifica permissões (se exigidas)
        if (isset($perfil['permissoes']) && !empty($perfil['permissoes'])) {
            $modo = strtoupper($perfil['modo'] ?? 'TODAS');
            
            if ($modo === 'TODAS') {
                // Usuário deve ter TODAS as permissões
                $permissoesFaltantes = array_diff($perfil['permissoes'], $permissoesDoUsuario);
                if (count($permissoesFaltantes) > 0) {
                    return false;
                }
            } else {
                // Usuário deve ter PELO MENOS UMA permissão
                $permissoesEncontradas = array_intersect($perfil['permissoes'], $permissoesDoUsuario);
                if (count($permissoesEncontradas) === 0) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Função para negar o acesso
     *
     * @param string $motivo
     * @return void
     */
    private function negarAcesso(string $motivo = 'Acesso Negado'): void
    {
        Response::redirecionar('/403');
        exit;
    }

    /**
     * Retorna o tipo de conta do perfil atual
     *
     * @return string|null
     */
    public static function getPerfilAtual(): ?string
    {
        return $_SESSION['perfil_acesso_atual']['tipo_conta'] ?? null;
    }

    /**
     * Verifica se o perfil atual é de um tipo específico
     *
     * @param string $tipoConta
     * @return bool
     */
    public static function isPerfilAtual(string $tipoConta): bool
    {
        $perfilAtual = self::getPerfilAtual();
        return $perfilAtual !== null && strtoupper($perfilAtual) === strtoupper($tipoConta);
    }
}
