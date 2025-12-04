<?php

/**
 * @file ComponenteCurricular.php
 * @description Modelo responsável pelos componentes curriculares das matrizes
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\ComponenteCurricularTipo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe ComponenteCurricular
 *
 * Modelo responsável pelos componentes curriculares das matrizes
 *
 * @property int $id
 * @property int $matriz_curricular_id
 * @property string $nome
 * @property int $creditos
 * @property int $carga_horaria
 * @property int $periodo
 * @property string $tipo
 *
 * @package App\Models
 * @extends Model
 */
class ComponenteCurricular extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'componentes_curriculares';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'matriz_curricular_id',
        'nome',
        'creditos',
        'carga_horaria',
        'periodo',
        'tipo'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'matriz_curricular_id' => 'integer',
        'nome' => 'string',
        'creditos' => 'integer',
        'carga_horaria' => 'integer',
        'periodo' => 'integer',
        'tipo' => 'string'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um componente curricular pertence a uma disciplina
     *
     * @return BelongsTo
     */
    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class, 'disciplina_id');
    }

    /**
     * Um componente curricular pertence a uma matriz curricular
     *
     * @return BelongsTo
     */
    public function matrizCurricular(): BelongsTo
    {
        return $this->belongsTo(MatrizCurricular::class, 'matriz_curricular_id');
    }

    /**
     * Um componente curricular possui várias turmas
     *
     * @return HasMany
     */
    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class, 'componente_curricular_id');
    }

    /**
     * Um componente curricular pode ter zero ou mais pré-requisitos
     * 
     * @return HasMany
     */
    public function preRequisitos(): HasMany
    {
        return $this->hasMany(ComponentePreRequisito::class, 'componente_curricular_id');
    }

    /**
     * Um componente curricular pode ser pré-requisito para zero ou mais componentes curriculares
     * 
     * @return HasMany
     */
    public function componentesDependentes(): HasMany
    {
        return $this->hasMany(ComponentePreRequisito::class, 'pre_requisito_id');
    }

    /**
     * Um componente curricular pode ter várias equivalências
     * 
     * @return HasMany
     */
    public function equivalencias(): HasMany
    {
        return $this->hasMany(ComponenteEquivalencia::class, 'componente_curricular_id');
    }

    /**
     * Um componente curricular pode ser equivalente para vários outros componentes curriculares
     * 
     * @return HasMany
     */
    public function componentesEquivalentes(): HasMany
    {
        return $this->hasMany(ComponenteEquivalencia::class, 'componente_equivalente_id');
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
     * Filtro por ID da disciplina
     *
     * @param $query
     * @param int $disciplinaId
     * @return Builder
     */
    public function scopeDisciplinaId($query, int $disciplinaId): Builder
    {
        return $query->where('disciplina_id', $disciplinaId);
    }

    /**
     * Filtro por ID da matriz curricular
     *
     * @param $query
     * @param int $matrizId
     * @return Builder
     */
    public function scopeMatrizCurricularId($query, int $matrizId): Builder
    {
        return $query->where('matriz_curricular_id', $matrizId);
    }

    /**
     * Filtro por período
     *
     * @param $query
     * @param int $periodo
     * @return Builder
     */
    public function scopePeriodo($query, int $periodo): Builder
    {
        return $query->where('periodo', $periodo);
    }

    /**
     * Filtro por tipo
     *
     * @param $query
     * @param ComponenteCurricularTipo $tipo
     * @return Builder
     */
    public function scopeTipo($query, ComponenteCurricularTipo $tipo): Builder
    {
        return $query->where('tipo', $tipo->value);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do componente curricular
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da matriz curricular
     * 
     * @return int
     */
    public function obterMatrizCurricularId(): int
    {
        return $this->matriz_curricular_id;
    }

    /**
     * Assessor (getter) para obter o nome do componente curricular
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter os créditos do componente curricular
     * 
     * @return int
     */
    public function obterCreditos(): int
    {
        return $this->creditos;
    }
    
    /**
     * Assessor (getter) para obter a carga horária do componente curricular
     * 
     * @return int
     */
    public function obterCargaHoraria(): int
    {
        return $this->carga_horaria;
    }

    /**
     * Assessor (getter) para obter o período do componente curricular
     * 
     * @return int
     */
    public function obterPeriodo(): int
    {
        return $this->periodo;
    }

    /**
     * Assessor (getter) para obter o tipo do componente curricular
     * 
     * @return ComponenteCurricularTipo
     */
    public function obterTipo(): ComponenteCurricularTipo
    {
        return $this->tipo instanceof ComponenteCurricularTipo ? $this->tipo : ComponenteCurricularTipo::fromName($this->tipo);
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID da matriz curricular
     *
     * @param int $matrizCurricularId
     * @return void
     */
    public function atribuirMatrizCurricularId(int $matrizCurricularId): void
    {
        $this->matriz_curricular_id = $matrizCurricularId;
    }

    /**
     * Mutador (setter) para atribuir o nome do componente curricular
     * 
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir os créditos do componente curricular
     * 
     * @param int $creditos
     * @return void
     */
    public function atribuirCreditos(int $creditos): void
    {
        $this->creditos = $creditos;
    }

    /**
     * Mutador (setter) para atribuir a carga horária do componente curricular
     * 
     * @param int $cargaHoraria
     * @return void
     */
    public function atribuirCargaHoraria(int $cargaHoraria): void
    {
        $this->carga_horaria = $cargaHoraria;
    }

    /**
     * Mutador (setter) para atribuir o período
     *
     * @param int $periodo
     * @return void
     */
    public function atribuirPeriodo(int $periodo): void
    {
        $this->periodo = $periodo;
    }

    /**
     * Mutador (setter) para atribuir o tipo do componente curricular
     *
     * @param ComponenteCurricularTipo $tipo
     * @return void
     */
    public function atribuirTipo(ComponenteCurricularTipo $tipo): void
    {
        $this->tipo = $tipo->name;
    }

}