<?php

/**
 * @file Notificacao.php
 * @description Modelo responsável pelas notificações disparadas no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Classe Notificacao
 *
 * Modelo responsável pelas notificações disparadas no sistema
 *
 * @property int $id
 * @property int $modelo_id
 * @property ?int $autor_id
 * @property string $titulo
 * @property string $mensagem
 * @property DateTime $data_registro
 *
 * @package App\Models
 * @extends Model
 */
class Notificacao extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'notificacoes';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'modelo_id',
        'autor_id',
        'titulo',
        'mensagem',
        'data_registro'
    ];

    /**
     * Converte atributos para tipos nativos do PHP
     * @var array
     */
    protected $casts = [
        'data_registro' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma notificação pertence a um modelo de notificação
     * TODO: Relacionamento?
     *
     * @return BelongsTo
     */
    public function modelo(): BelongsTo
    {
        return $this->belongsTo(NotificacaoModelo::class, 'modelo_id');
    }

    /**
     * Uma notificação possui um ou mais destinos
     * TODO: Relacionamento?
     *
     * @return HasMany
     */
    public function destinos(): HasMany
    {
        return $this->hasMany(NotificacaoDestino::class, 'notificacao_id');
    }

    /**
     * Uma notificação pode ser lida por vários usuários
     * TODO: Relacionamento?
     *
     * @return HasMany
     */
    public function leituras(): HasMany
    {
        return $this->hasMany(NotificacaoLeitura::class, 'notificacao_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da notificação
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do modelo da notificação
     *
     * @return int
     */
    public function obterModeloId(): int {
        return $this->modelo_id;
    }

    /**
     * Assessor (getter) para obter o ID do autor da notificação
     *
     * @return ?int
     */
    public function obterAutorId(): ?int {
        return $this->autor_id;
    }

    /**
     * Assessor (getter) para obter o título da notificação
     *
     * @return string
     */
    public function obterTitulo(): string {
        return $this->titulo;
    }

    /**
     * Assessor (getter) para obter a mensagem da notificação
     *
     * @return string
     */
    public function obterMensagem(): string {
        return $this->mensagem;
    }

    /**
     * Assessor (getter) para obter a data de registro da notificação
     *
     * @return DateTime
     */
    public function obterDataRegistro(): DateTime {
        return $this->data_registro;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID da notificação
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do modelo da notificação
     *
     * @param int $modelo_id
     * @return void
     */
    public function atribuirModeloId(int $modelo_id): void {
        $this->modelo_id = $modelo_id;
    }

    /**
     * Mutador (setter) para atribuir o ID do autor da notificação
     *
     * @param ?int $autor_id
     * @return void
     */
    public function atribuirAutorId(?int $autor_id): void {
        $this->autor_id = $autor_id;
    }

    /**
     * Mutador (setter) para atribuir o título da notificação
     *
     * @param string $titulo
     * @return void
     */
    public function atribuirTitulo(string $titulo): void {
        $this->titulo = $titulo;
    }

    /**
     * Mutador (setter) para atribuir a mensagem da notificação
     *
     * @param string $mensagem
     * @return void
     */
    public function atribuirMensagem(string $mensagem): void {
        $this->mensagem = $mensagem;
    }

    /**
     * Mutador (setter) para atribuir a data de registro da notificação
     *
     * @param DateTime $data_registro
     * @return void
     */
    public function atribuirDataRegistro(DateTime $data_registro): void {
        $this->data_registro = $data_registro;
    }
}