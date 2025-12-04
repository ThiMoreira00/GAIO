<?php

/**
 * @file NotificacaoDestino.php
 * @description Modelo responsável pelos destinos das notificações disparadas no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Classe NotificacaoDestino
 *
 * Modelo responsável pelos destinos das notificações disparadas no sistema
 *
 * @property int $id
 * @property int $notificacao_id
 * @property int $destinatario_id
 * @property string $destinatario_tipo
 *
 * @package App\Models
 * @extends Model
 */
class NotificacaoDestino extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM)---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'notificacoes_destinos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'notificacao_id',
        'destinatario_id',
        'destinatario_tipo'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * O registro de destino pertence a uma notificação
     * TODO: Relacionamento?
     *
     * @return BelongsTo
    */
    public function notificacao(): BelongsTo
    {
        return $this->belongsTo(Notificacao::class, 'notificacao_id');
    }

    /**
     * Retorna o model do destinatário
     * TODO: Verificar relacionamento = Relacionamento Polimófico
     *
     * @return MorphTo
    */
    public function destinatario(): MorphTo
    {
        return $this->morphTo();
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro
     *
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da notificação
     *
     * @return int
     */
    public function obterNotificacaoId(): int
    {
        return $this->notificacao_id;
    }

    /**
     * Assessor (getter) para obter o ID do destinatário
     *
     * @return int
     */
    public function obterDestinatarioId(): int
    {
        return $this->destinatario_id;
    }

    /**
     * Assessor (getter) para obter o tipo do destinatário
     *
     * @return string
     */
    public function obterDestinatarioTipo(): string
    {
        return $this->destinatario_tipo;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID da notificação
     *
     * @param int $notificacao_id
     * @return void
     */
    public function atribuirNotificacaoId(int $notificacao_id): void
    {
        $this->notificacao_id = $notificacao_id;
    }

    /**
     * Mutador (setter) para atribuir o ID do destinatário
     *
     * @param int $destinatario_id
     * @return void
     */
    public function atribuirDestinatarioId(int $destinatario_id): void
    {
        $this->destinatario_id = $destinatario_id;
    }

    /**
     * Mutador (setter) para atribuir o tipo do destinatário
     *
     * @param string $destinatario_tipo
     * @return void
     */
    public function atribuirDestinatarioTipo(string $destinatario_tipo): void
    {
        $this->destinatario_tipo = $destinatario_tipo;
    }
}