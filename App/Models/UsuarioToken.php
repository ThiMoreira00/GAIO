<?php

/**
 * @file UsuarioToken.php
 * @description Modelo responsável pelos tokens do usuário no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Random\RandomException;

/**
 * Classe UsuarioToken
 *
 * Modelo responsável pelos tokens do usuário no sistema
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $token_hash
 * @property string $tipo
 * @property bool $status
 * @property DateTime $data_expiracao
 * @property DateTime $data_registro
 */

class UsuarioToken extends Model
{

    /**
     * O nome da tabela associada ao modelo
     * @var string
     */
    protected $table = 'usuarios_tokens';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'token_hash',
        'tipo',
        'status',
        'data_expiracao',
        'data_registro'
    ];

    /**
     * Os atributos que devem ser ocultados ao serializar
     * @var array
     */
    protected $hidden = [
        'token_hash',
        'tipo',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     * O Eloquent converte automaticamente os valores ao acessá-los.
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'data_expiracao' => 'datetime',
        'data_registro' => 'datetime'
    ];


    // --- SCOPES ---

    /**
     * Filtro de tokens com base no ID do usuário
     *
     * @param $query
     * @param int $usuarioId
     * @return Builder
     */
    public function scopeUsuarioId($query, int $usuarioId): Builder
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Filtro de tokens com base no tipo
     *
     * @param $query
     * @param string $tipo
     * @return Builder
     */
    public function scopeTipo($query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Filtro de tokens com base no status
     *
     * @param $query
     * @param bool $status
     * @return Builder
     */
    public function scopeStatus($query, bool $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Filtro de token com base no token hash
     *
     * @param $query
     * @param string $tokenHash
     * @return Builder
     */
    public function scopeTokenHash($query, string $tokenHash): Builder
    {
        return $query->where('token_hash', $tokenHash);
    }

    /**
     * Filtro de token com base no tipo de token hash (redefinição) + status
     *
     * @param $query
     * @param string $tipo
     * @param bool $status
     * @return Builder
     */
    public function scopeTokenRedefinicao($query, string $tokenHash, bool $status): Builder
    {
        return $query->where('tipo', 'redefinicao_senha')
            ->where('status', $status)
            ->where('token_hash', $tokenHash);
    }


    // --- RELACIONAMENTOS ---

    /**
     * Define a relação inversa com o usuário.
     * Vários tokens PERTENCE A um usuário.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }


    // --- ASSESSORES (GETTERS) E MUTADORES (SETTERS) ---

    /**
     * Assessor (getter) para obter o ID do token
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário do token
     *
     * @return int
     */
    public function obterUsuarioId(): int {
        return $this->usuario_id;
    }

    /**
     * Assessor (getter) para obter o token do usuário
     *
     * @return string
     */
    public function obterTokenHash(): string {
        return $this->token_hash;
    }

    /**
     * Assessor (getter) para obter o tipo do token
     *
     * @return string
     */
    public function obterTipo(): string {
        return $this->tipo;
    }

    /**
     * Assessor (getter) para obter o status do token
     *
     * @return boolean
     */
    public function obterStatus(): bool {
        return $this->status;
    }

    /**
     * Assessor (getter) para obter a data de expiração do token
     *
     * @return DateTime
     */
    public function obterDataExpiracao(): DateTime {
        return $this->data_expiracao;
    }

    /**
     * Assessor (getter) para obter a data de registro do token
     *
     * @return DateTime
     */
    public function obterDataRegistro(): DateTime {
        return $this->data_registro;
    }

    /**
     * Mutador (setter) para atribuir o ID do token.
     *
     * @param int $id O ID do token.
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário do token
     *
     * @param int $usuario_id
     * @return void
     */
    public function atribuirUsuarioId(int $usuario_id): void {
        $this->usuario_id = $usuario_id;
    }

    /**
     * Mutador (setter) para atribuir o hash do token
     *
     * @param string $token_hash
     * @return void
     */
    public function atribuirTokenHash(string $token_hash): void {
        $this->token_hash = $token_hash;
    }

    /**
     * Mutador (setter) para atribuir o tipo do token
     *
     * @param string $tipo
     * @return void
     */
    public function atribuirTipo(string $tipo): void {
        $this->tipo = $tipo;
    }

    /**
     * Mutador (setter) para atribuir o status do token
     *
     * @param boolean $status
     * @return void
     */
    public function atribuirStatus(bool $status): void {
        $this->status = $status;
    }

    /**
     * Mutador (setter) para atribuir a data de expiração do token
     *
     * @param DateTime $data_expiracao
     * @return void
     */
    public function atribuirDataExpiracao(DateTime $data_expiracao): void {
        $this->data_expiracao = $data_expiracao;
    }

    /**
     * Mutador (setter) para atribuir a data de registro do token
     *
     * @param DateTime $data_registro
     * @return void
     */
    public function atribuirDataRegistro(DateTime $data_registro): void {
        $this->data_registro = $data_registro;
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Função para verificar se o token está expirado
     * @return boolean
     */
    public function verificarExpirado() {
        return $this->data_expiracao < new DateTime() || !$this->status;
    }


    // --- MÉTODOS ESTÁTICOS ---

    /**
     * Função para gerar um token aleatório
     *
     * @return string
     * @throws RandomException
     */
    public static function gerarToken(): string
    {
        $bytes = random_bytes(128);
        $token = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');

        return $token;
    }




}
