<?php

/**
 * @file TurmaHorario.php
 * @description Modelo responsável pelos horários das turmas no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe TurmaHorario
 *
 * Modelo responsável pelos horários das turmas no sistema
 *
 * @property int $id
 * @property int $turma_id
 * @property int $espaco_id
 * @property int $tempo_aula_id
 *
 * @package App\Models
 * @extends Model
 */
class TurmaHorario extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'turmas_horarios';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'turma_id',
        'espaco_id',
        'tempo_aula_id'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'turma_id' => 'int',
        'espaco_id' => 'int',
        'tempo_aula_id' => 'int'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um horário de turma está associado a uma turma
     *
     * @return BelongsTo
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    /**
     * Um horário de turma está associado a um espaço
     *
     * @return BelongsTo
     */
    public function espaco(): BelongsTo
    {
        return $this->belongsTo(Espaco::class, 'espaco_id');
    }

    /**
     * Um horário de turma está associado a um tempo de aula
     *
     * @return BelongsTo
     */
    public function tempoAula(): BelongsTo
    {
        return $this->belongsTo(TempoAula::class, 'tempo_aula_id');
    }

    // --- SCOPES (FILTROS) ---

    /**
     * Filtro por id
     *
     * @param $query
     * @param int $id
     * @return Builder
     */
    public function scopeId($query, int $id): Builder
    {
        return $query->where('id', $id);
    }

    /**
     * Filtro por turma_id
     *
     * @param $query
     * @param int $turmaId
     * @return Builder
     */
    public function scopeTurmaId($query, int $turmaId): Builder
    {
        return $query->where('turma_id', $turmaId);
    }

    /**
     * Filtro por espaco_id
     *
     * @param $query
     * @param int $espacoId
     * @return Builder
     */
    public function scopeEspacoId($query, int $espacoId): Builder
    {
        return $query->where('espaco_id', $espacoId);
    }

    /**
     * Filtro por tempo_aula_id
     *
     * @param $query
     * @param int $tempoAulaId
     * @return Builder
     */
    public function scopeTempoAulaId($query, int $tempoAulaId): Builder
    {
        return $query->where('tempo_aula_id', $tempoAulaId);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do horário da turma
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da turma associada
     * 
     * @return int
     */
    public function obterTurmaId(): int
    {
        return $this->turma_id;
    }

    /**
     * Assessor (getter) para obter o ID do espaço associado
     * 
     * @return int
     */
    public function obterEspacoId(): int
    {
        return $this->espaco_id;
    }

    /**
     * Assessor (getter) para obter o ID do tempo de aula associado
     * 
     * @return int
     */
    public function obterTempoAulaId(): int
    {
        return $this->tempo_aula_id;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para definir o ID da turma associada
     * 
     * @param int $turmaId
     * @return void
     */
    public function atribuirTurmaId(int $turmaId): void
    {
        $this->turma_id = $turmaId;
    }

    /**
     * Mutador (setter) para definir o ID do espaço associado
     * 
     * @param int $espacoId
     * @return void
     */
    public function atribuirEspacoId(int $espacoId): void
    {
        $this->espaco_id = $espacoId;
    }

    /**
     * Mutador (setter) para definir o ID do tempo de aula associado
     * 
     * @param int $tempoAulaId
     * @return void
     */
    public function atribuirTempoAulaId(int $tempoAulaId): void
    {
        $this->tempo_aula_id = $tempoAulaId;
    }

}