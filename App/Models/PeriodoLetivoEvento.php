<?php

/**
 * @file PeriodoLetivoEvento.php
 * @description Modelo responsável pela associação entre períodos letivos e eventos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

/**
 * Classe PeriodoLetivoEvento
 *
 * Modelo responsável pela associação entre períodos letivos e eventos
 *
 * @property int $id
 * @property int $evento_id
 * @property int $periodo_letivo_id
 *
 * @package App\Models
 * @extends Model
 */
class PeriodoLetivoEvento extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'periodos_letivos_eventos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'evento_id',
        'periodo_letivo_id'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'evento_id' => 'integer',
        'periodo_letivo_id' => 'integer'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um período letivo evento pertence a um evento
     *
     * @return BelongsTo
     */
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    /**
     * Um período letivo evento pertence a um período letivo
     *
     * @return BelongsTo
     */
    public function periodoLetivo(): BelongsTo
    {
        return $this->belongsTo(PeriodoLetivo::class, 'periodo_letivo_id');
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
     * Filtro por ID do evento
     *
     * @param $query
     * @param int $eventoId
     * @return Builder
     */
    public function scopeEventoId($query, int $eventoId): Builder
    {
        return $query->where('evento_id', $eventoId);
    }

    /**
     * Filtro por ID do período letivo
     *
     * @param $query
     * @param int $periodoLetivoId
     * @return Builder
     */
    public function scopePeriodoLetivoId($query, int $periodoLetivoId): Builder
    {
        return $query->where('periodo_letivo_id', $periodoLetivoId);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de período letivo evento
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do evento
     * 
     * @return int
     */
    public function obterEventoId(): int
    {
        return $this->evento_id;
    }

    /**
     * Assessor (getter) para obter o ID do período letivo
     * 
     * @return int
     */
    public function obterPeriodoLetivoId(): int
    {
        return $this->periodo_letivo_id;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de período letivo evento
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do evento
     *
     * @param int $eventoId
     * @return void
     */
    public function atribuirEventoId(int $eventoId): void
    {
        $this->evento_id = $eventoId;
    }

    /**
     * Mutador (setter) para atribuir o ID do período letivo
     *
     * @param int $periodoLetivoId
     * @return void
     */
    public function atribuirPeriodoLetivoId(int $periodoLetivoId): void
    {
        $this->periodo_letivo_id = $periodoLetivoId;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função estática para buscar eventos de um período letivo
     * 
     * @param int $periodoLetivoId
     * @return Collection
     */
    public static function buscarEventosPorPeriodo(int $periodoLetivoId): Collection
    {
        return self::where('periodo_letivo_id', $periodoLetivoId)->get();
    }

    /**
     * Função estática para buscar períodos letivos de um evento
     * 
     * @param int $eventoId
     * @return Collection
     */
    public static function buscarPeriodosPorEvento(int $eventoId): Collection
    {
        return self::where('evento_id', $eventoId)->get();
    }

}