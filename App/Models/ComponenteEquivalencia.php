<?php

/**
 * @file ComponenteEquivalencia.php
 * @description Modelo responsável pelas equivalências entre componentes curriculares
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
 * Classe ComponenteEquivalencia
 *
 * Modelo responsável pelas equivalências entre componentes curriculares
 *
 * @property int $id
 * @property int $componente_curricular_id
 * @property int $componente_equivalente_id
 
 * @package App\Models
 * @extends Model
 */
class ComponenteEquivalencia extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'componentes_equivalencias';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'componente_curricular_id',
        'componente_equivalente_id'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'componente_curricular_id' => 'integer',
        'componente_equivalente_id' => 'integer'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma equivalência pertence a um componente curricular
     *
     * @return BelongsTo
     */
    public function componenteCurricular(): BelongsTo
    {
        return $this->belongsTo(ComponenteCurricular::class, 'componente_curricular_id');
    }

    /**
     * Uma equivalência pertence a um componente curricular equivalente
     *
     * @return BelongsTo
     */
    public function componenteEquivalente(): BelongsTo
    {
        return $this->belongsTo(ComponenteCurricular::class, 'componente_equivalente_id');
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
     * Filtro por ID do componente curricular
     * 
     * @param $query
     * @param int $componenteCurricularId
     * @return Builder
     */
    public function scopeComponenteCurricularId($query, int $componenteCurricularId): Builder
    {
        return $query->where('componente_curricular_id', $componenteCurricularId);
    }

    /**
     * Filtro por ID do componente curricular equivalente
     * 
     * @param $query
     * @param int $componenteEquivalenteId
     * @return Builder
     */
    public function scopeComponenteEquivalenteId($query, int $componenteEquivalenteId): Builder
    {
        return $query->where('componente_equivalente_id', $componenteEquivalenteId);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de equivalência
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do componente curricular
     * 
     * @return int
     */
    public function obterComponenteCurricularId(): int
    {
        return $this->componente_curricular_id;
    }

    /**
     * Assessor (getter) para obter o ID do componente curricular equivalente
     * 
     * @return int
     */
    public function obterComponenteEquivalenteId(): int
    {
        return $this->componente_equivalente_id;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do componente curricular
     *
     * @param int $componenteCurricularId
     * @return void
     */
    public function atribuirComponenteCurricularId(int $componenteCurricularId): void
    {
        $this->componente_curricular_id = $componenteCurricularId;
    }

    /**
     * Mutador (setter) para atribuir o ID do componente curricular equivalente
     *
     * @param int $componenteEquivalenteId
     * @return void
     */
    public function atribuirComponenteEquivalenteId(int $componenteEquivalenteId): void
    {
        $this->componente_equivalente_id = $componenteEquivalenteId;
    }
}