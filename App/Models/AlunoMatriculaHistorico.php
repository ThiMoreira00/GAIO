<?php

/**
 * @file AlunoMatriculaHistorico.php
 * @description Modelo responsável pelo histórico nas matrículas dos alunos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\AlunoMatriculaStatus;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe AlunoMatriculaHistorico
 *
 * Modelo responsável pelo histórico nas matrículas dos alunos
 *
 * @property int $id
 * @property int $aluno_matricula_id
 * @property string $observacao
 * @property AlunoMatriculaStatus $status
 * @property DateTime $data_registro
 *
 * @package App\Models
 * @extends Model
 */
class AlunoMatriculaHistorico extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos_matriculas_historicos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'aluno_matricula_id',
        'observacao',
        'status',
        'data_registro'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'aluno_matricula_id' => 'integer',
        'observacao' => 'string',
        'status' => AlunoMatriculaStatus::class,
        'data_registro' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um histórico pertence a uma matrícula
     * 
     * @return BelongsTo
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(AlunoMatricula::class, 'aluno_matricula_id');
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
     * Filtro por ID do registro de matrícula do aluno
     *
     * @param $query
     * @param int $alunoMatriculaId
     * @return Builder
     */
    public function scopeAlunoMatriculaId($query, int $alunoMatriculaId): Builder
    {
        return $query->where('aluno_matricula_id', $alunoMatriculaId);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro do histórico de matrícula
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do registro da matrícula
     * 
     * @return int
     */
    public function obterAlunoMatriculaId(): int
    {
        return $this->aluno_matricula_id;
    }

    /**
     * Assessor (getter) para obter a observação
     * 
     * @return string
     */
    public function obterObservacao(): string
    {
        return $this->observacao;
    }

    /**
     * Assessor (getter) para obter o status
     * 
     * @return AlunoMatriculaStatus
     */
    public function obterStatus(): AlunoMatriculaStatus
    {
        return $this->status;
    }

    /**
     * Assessor (getter) para obter a data de registro
     * 
     * @return DateTime
     */
    public function obterDataRegistro(): DateTime
    {
        return $this->data_registro;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro do histórico de matrícula
     * 
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do registro da matrícula
     *
     * @param int $alunoMatriculaId
     * @return void
     */
    public function atribuirAlunoMatriculaId(int $alunoMatriculaId): void
    {
        $this->aluno_matricula_id = $alunoMatriculaId;
    }

    /**
     * Mutador (setter) para atribuir o status
     *
     * @param AlunoMatriculaStatus $status
     * @return void
     */
    public function atribuirStatus(AlunoMatriculaStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Mutador (setter) para atribuir a observação
     *
     * @param string $observacao
     * @return void
     */
    public function atribuirObservacao(string $observacao): void
    {
        $this->observacao = $observacao;
    }

    /**
     * Mutador (setter) para atribuir a data de registro
     *
     * @param DateTime $dataRegistro
     * @return void
     */
    public function atribuirDataRegistro(DateTime $dataRegistro): void
    {
        $this->data_registro = $dataRegistro;
    }

}