<?php

/**
 * @file CursoPeriodoLetivo.php
 * @description Pivot responsável pela relação entre cursos e períodos letivos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * Classe CursoPeriodoLetivo
 *
 * Pivot responsável pela relação entre cursos e períodos letivos
 *
 * @property int $id
 * @property int $curso_id
 * @property int $periodo_letivo_id
 *
 * @package App\Models
 * @extends Pivot
 */
class CursoPeriodoLetivo extends Pivot
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'cursos_periodos_letivos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'curso_id',
        'periodo_letivo_id'
    ];
    

    // --- RELACIONAMENTOS ---

    /**
     * Relacionamento com o modelo Curso (um curso PODE ter vários períodos letivos)
     * TODO: Relacionamento?
     * 
     * @return BelongsTo
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    /**
     * Relacionamento com o modelo PeriodoLetivo (um período letivo PODE pertencer a vários cursos)
     * TODO: Relacionamento?
     * 
     * @return BelongsTo
     */
    public function periodoLetivo(): BelongsTo
    {
        return $this->belongsTo(PeriodoLetivo::class, 'periodo_letivo_id');
    }
}