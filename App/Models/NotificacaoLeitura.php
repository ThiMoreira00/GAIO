<?php

/**
 * @file NotificacaoLeitura.php
 * @description Pivot responsável pelas leituras das notificações disparadas no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Pivot;
use App\Core\Model;
use DateTime;

/**
 * Classe NotificacaoLeitura
 *
 * Pivot responsável pelas leituras das notificações disparadas no sistema
 *
 * @property int $id
 * @property int $notificacao_id
 * @property int $usuario_id
 * @property DateTime $data_leitura
 *
 * @package App\Models
 * @extends Pivot
 */
class NotificacaoLeitura extends Pivot
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao pivot
     * @var string
     */
    protected $table = 'notificacoes_leituras';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'notificacao_id',
        'usuario_id',
        'data_leitura'
    ];

    /**
     * Converte atributos para tipos nativos do PHP
     * @var array
     */
    protected $casts = [
        'data_leitura' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Define a relação inversa com o usuário (uma leitura PERTENCE A um usuário)
     * TODO: Relacionamento?
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Define a relação inversa com a notificação (uma leitura POSSUI uma notificação)
     * TODO: Relacionamento?
     *
     * @return BelongsTo
     */
    public function notificacao(): BelongsTo
    {
        return $this->belongsTo(Notificacao::class, 'notificacao_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da leitura
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da notificação
     *
     * @return int
     */
    public function obterNotificacaoId(): int {
        return $this->notificacao_id;
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
     * Assessor (getter) para obter a data da leitura
     *
     * @return DateTime
     */
    public function obterDataLeitura(): DateTime {
        return $this->data_leitura;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID da leitura
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID da notificação
     *
     * @param int $notificacaoId
     * @return void
     */
    public function atribuirNotificacaoId(int $notificacaoId): void {
        $this->notificacao_id = $notificacaoId;
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
     * Mutador (setter) para atribuir a data da leitura
     *
     * @param DateTime $dataLeitura
     * @return void
     */
    public function atribuirDataLeitura(DateTime $dataLeitura): void {
        $this->data_leitura = $dataLeitura;
    }
}