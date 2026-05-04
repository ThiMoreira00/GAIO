<?php

/**
 * @file UsuarioLogin.php
 * @description Modelo responsável pelo login do usuário no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\UsuarioLoginStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe UsuarioLogin
 *
 * Modelo responsável pelo login do usuário no sistema
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $nome_acesso
 * @property string $senha_hash
 * @property UsuarioLoginStatus $status
 */

class UsuarioLogin extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo
     * @var string
     */
    protected $table = 'usuarios_logins';

    /**
     * Os atributos que podem ser atribuídos em massa
     * @var array<int, string>
     */
    protected $fillable = [
        'usuario_id',
        'nome_acesso',
        'senha_hash',
        'status'
    ];

    /**
     * Os atributos que devem ser ocultados ao serializar
     * @var array<int, string>
     */
    protected $hidden = [
        'senha_hash'
    ];


    // --- SCOPES ---

    /**
     * Filtro de usuários com base no nome de acesso
     *
     * @param $query
     * @param string $nomeAcesso
     * @return Builder
     */
    public function scopeNomeAcesso($query, string $nomeAcesso): Builder
    {
        return $query->where('nome_acesso', $nomeAcesso);
    }


    // --- RELACIONAMENTOS ---

    /**
     * Define a relação inversa com o usuário (uma conta PERTENCE A um usuário)
     * TODO: Relacionamento?
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do login do usuário
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário
     *
     * @return int
     */
    public function obterUsuarioId(): int {
        return $this->usuario_id;
    }

    /**
     * Assessor (getter) para obter o nome de acesso do usuário
     *
     * @return string
     */
    public function obterNomeAcesso(): string {
        return $this->nome_acesso;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do login do usuário
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário
     *
     * @param int $usuarioId
     * @return void
     */
    public function atribuirUsuarioId(int $usuarioId): void {
        $this->usuario_id = $usuarioId;
    }

    /**
     * Mutador (setter) para atribuir o nome de acesso do usuário
     *
     * @param string $nomeAcesso
     * @return void
     */
    public function atribuirNomeAcesso(string $nomeAcesso): void {
        $this->nome_acesso = $nomeAcesso;
    }

    /**
     * Mutador (setter) para atribuir o status do login do usuário
     *
     * @param UsuarioLoginStatus $status
     * @return void
     */
    public function atribuirStatus(UsuarioLoginStatus $status): void {
        $this->status = $status;
    }

    /**
     * Mutador (setter) para atribuir a senha do usuário
     *
     * @param string $senha
     * @return void
     */
    public function atribuirSenha(string $senha): void {
        $this->senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Função para verificar se a senha fornecida corresponde à senha armazenada
     *
     * @param string $senha
     * @return bool
     */
    public function verificarSenha(string $senha): bool
    {
        return password_verify($senha, $this->senha_hash);
    }

    /**
     * Função para buscar um login pelo nome de acesso
     *
     * @param string $nomeAcesso
     * @return ?UsuarioLogin
     */
    public static function buscarPorNomeAcesso(string $nomeAcesso): ?UsuarioLogin
    {
        return self::where('nome_acesso', $nomeAcesso)->first();
    }

}
