<?php

/**
 * @file MatrizCurricular.php
 * @description Modelo responsável pelas matrizes curriculares registradas no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\MatrizCurricularStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe Matriz Curricular
 *
 * Modelo responsável pelas matrizes curriculares do sistema
 *
 * @property int $id
 * @property int $curso_id
 * @property int $quantidade_periodos
 * @property Date $data_vigencia
 * @property string $status
 *
 * @package App\Models
 * @extends Model
 */
class MatrizCurricular extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'matrizes_curriculares';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'curso_id',
        'quantidade_periodos',
        'data_vigencia',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'quantidade_periodos' => 'integer',
        'data_vigencia' => 'date',
        'status' => 'string'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma matriz curricular pertence a um curso (Curso)
     *
     * @return BelongsTo
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
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
     * Filtro por status (vigente, arquivado)
     *
     * @param $query
     * @param string $status
     * @return Builder
     */
    public function scopeStatus($query, string $status): Builder
    {
        return $query->where('status', $status);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da matriz curricular
     *
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do curso associado a matriz
     *
     * @return int
     */
    public function obterCursoId(): int
    {
        return $this->curso_id;
    }

    /**
     * Assessor (getter) para obter a quantidade de períodos da matriz
     *
     * @return int
     */
    public function obterQuantidadePeriodos(): int
    {
        return $this->quantidade_periodos;
    }

    /**
     * Assessor (getter) para obter a data de vigência da matriz
     * 
     * @return Date
     */
    public function obterDataVigencia(): Date
    {
        return $this->data_vigencia;
    }

    /**
     * Assessor (getter) para obter o status da matriz curricular
     *
     * @return MatrizCurricularStatus
     */
    public function obterStatus(): MatrizCurricularStatus
    {   
        return MatrizCurricularStatus::fromName($this->status);
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID da matriz
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do curso associado a matriz
     *
     * @param int $curso_id
     * @return void
     */
    public function atribuirCursoId(int $curso_id): void
    {
        $this->curso_id = $curso_id;
    }

    /**
     * Mutador (setter) para atribuir a quantidade de períodos da matriz
     *
     * @param int $quantidade_periodos
     * @return void
     */
    public function atribuirQuantidadePeriodos(int $quantidade_periodos): void
    {
        $this->quantidade_periodos = $quantidade_periodos;
    }

    /**
     * Mutador (setter) para atribuir a data de vigência da matriz
     * 
     * @param Date $data_vigencia
     * @return void
     */
    public function atribuirDataVigencia(Date $data_vigencia): void
    {
        $this->data_vigencia = $data_vigencia;
    }

    /**
     * Mutador (setter) para atribuir o status do curso
     *
     * @param MatrizCurricularStatus|string $status
     * @return void
     */
    public function atribuirStatus($status): void
    {
        $this->status = $status instanceof MatrizCurricularStatus ? $status->value : $status;
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Arquiva o curso (altera o status para "arquivado")
     *
     * @return void
     */
    public function arquivar(): void
    {
        $this->attributes['status'] = MatrizCurricularStatus::ARQUIVADO->value;
    }

    /**
     * Tornar a matriz curricular vigente (altera o status para "vigente")
     *
     * @return void
     */
    public function tornarVigente(): void
    {
        $this->attributes['status'] = MatrizCurricularStatus::VIGENTE->value;
    }

}