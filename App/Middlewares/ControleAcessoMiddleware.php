<?php

declare(strict_types=1);

namespace App\Middlewares;

/**
 * CLASSE MIDDLEWARE - ControleAcessoMiddleware
 *
 * Valida o acesso às rotas:
 * - Apenas por tipo de conta.
 * - Apenas por permissões específicas.
 * - Por uma combinação de tipo de conta e permissões.
 */

readonly class ControleAcessoMiddleware
{
    /**
     * @param string|null $tipoExigido O tipo de conta ('usuario', 'aluno', 'professor', 'administrador') necessário.
     * @param array<string>|null $permissoesExigidas Lista de chaves de permissão ('GAIO_CURSO_CRIAR').
     * @param string $modoPermissao 'TODAS' (padrão) exige todas as permissões da lista. 'QUALQUER' exige pelo menos uma.
     */
    public function __construct(private ?string $tipoExigido = null, private ?array $permissoesExigidas = null, private string $modoPermissao = 'TODAS') {
        // Valida o modo de permissão para evitar erros
        if (!in_array(strtoupper($this->modoPermissao), ['TODAS', 'QUALQUER'])) {
            throw new \InvalidArgumentException("Modo de permissão inválido. Use 'TODAS' ou 'QUALQUER'.");
        }
    }

    /**
     * Executa a lógica do middleware.
     */
    public function executar(): void
    {
        $this->verificarAutenticacao();
        $this->verificarTipoDeConta();
        $this->verificarPermissoes();
    }

    // TODO: Talvez colocar isso em um Middleware de autenticação?
    private function verificarAutenticacao(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }
    }

    private function verificarTipoDeConta(): void
    {
        if ($this->tipoExigido !== null && ($_SESSION['usuario_tipo'] ?? null) !== $this->tipoExigido) {
            $this->negarAcesso('Tipo de conta inválido.');
        }
    }

    /**
     * Valida se o usuário possui as permissões necessárias.
     */
    private function verificarPermissoes(): void
    {
        if ($this->permissoesExigidas === null) {
            return; // Nenhuma permissão exigida, acesso permitido.
        }

        $permissoesDoUsuario = $_SESSION['usuario_permissoes'] ?? [];
        $possuiPermissao = (strtoupper($this->modoPermissao) === 'TODAS')
            ? $this->usuarioTemTodasAsPermissoes($permissoesDoUsuario)
            : $this->usuarioTemQualquerPermissao($permissoesDoUsuario);

        if (!$possuiPermissao) {
            $this->negarAcesso("Permissões insuficientes.");
        }
    }

    /**
     * Verifica se o usuário tem TODAS as permissões exigidas.
     */
    private function usuarioTemTodasAsPermissoes(array $permissoesDoUsuario): bool
    {
        $permissoesFaltantes = array_diff($this->permissoesExigidas, $permissoesDoUsuario);
        return empty($permissoesFaltantes);
    }

    /**
     * Verifica se o usuário tem PELO MENOS UMA das permissões exigidas.
     */
    private function usuarioTemQualquerPermissao(array $permissoesDoUsuario): bool
    {
        $permissoesEncontradas = array_intersect($this->permissoesExigidas, $permissoesDoUsuario);
        return !empty($permissoesEncontradas);
    }

    private function negarAcesso(string $motivo = 'Acesso Negado'): void
    {
        http_response_code(403);
        die("{$motivo} Você não tem autorização para acessar esta página.");
    }
}
