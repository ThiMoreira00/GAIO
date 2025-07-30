<?php

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
     * @param array|null $permissoesExigidas Lista de chaves de permissão ('GAIO_CURSO_CRIAR').
     * @param string $modoPermissao 'TODAS' (padrão) exige todas as permissões da lista. 'QUALQUER' exige pelo menos uma.
     */
    public function __construct(
        private ?string $tipoExigido = null,
        private ?array  $permissoesExigidas = null,
        private string  $modoPermissao = 'TODAS'
    ) {
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
        // 1. Validação de Autenticação (base para todas as outras)
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /login');
            exit;
        }

        // 2. Validação por Tipo de Conta (se exigido)
        if ($this->tipoExigido !== null && $this->tipoExigido !== 'usuario') {
            if (($_SESSION['usuario_tipo'] ?? null) !== $this->tipoExigido) {
                $this->negarAcesso("Tipo de conta inválido.");
            }
        }

        // 3. Validação por Permissões (se exigidas)
        if ($this->permissoesExigidas !== null) {
            $permissoesDoUsuario = $_SESSION['usuario_permissoes'] ?? [];
            $possuiPermissao = false;

            if (strtoupper($this->modoPermissao) === 'TODAS') {
                // Modo 'TODAS': Verifica se o usuário tem TODAS as permissões da lista.
                // A diferença entre as permissões exigidas e as que o usuário tem deve ser um array vazio.
                $permissoesFaltantes = array_diff($this->permissoesExigidas, $permissoesDoUsuario);
                if (empty($permissoesFaltantes)) {
                    $possuiPermissao = true;
                }
            } else { // Modo 'QUALQUER'
                // Modo 'QUALQUER': Verifica se o usuário tem PELO MENOS UMA das permissões da lista.
                // A interseção entre as permissões exigidas e as que o usuário tem não deve ser vazia.
                $permissoesEncontradas = array_intersect($this->permissoesExigidas, $permissoesDoUsuario);
                if (!empty($permissoesEncontradas)) {
                    $possuiPermissao = true;
                }
            }

            if (!$possuiPermissao) {
                $this->negarAcesso("Permissões insuficientes.");
            }
        }
    }

    /**
     * Método auxiliar para centralizar a resposta de acesso negado.
     */
    private function negarAcesso(string $motivo = "Acesso Negado"): void
    {
        http_response_code(403); // Código "Forbidden"
        // Em um sistema real, renderizar uma view de erro seria mais elegante.
        die("{$motivo} Você não tem autorização para acessar esta página.");
    }
}