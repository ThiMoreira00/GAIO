<?php

/**
 * @file TurmaFrequencia.php
 * @description Modelo responsável pelas frequências dos alunos nas turmas no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\TurmaFrequenciaSituacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe TurmaFrequencia
 *
 * Modelo responsável pelas frequências dos alunos nas turmas no sistema
 *
 * @property int $id
 * @property int $aluno_matricula_id
 * @property int $turma_dia_letivo_id
 * @property int $tempo_aula_id
 * @property TurmaFrequenciaSituacao $situacao
 *
 * @package App\Models
 * @extends Model
 */
class TurmaFrequencia extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'turma_frequencias';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'aluno_matricula_id',
        'turma_dia_letivo_id',
        'tempo_aula_id',
        'situacao'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'aluno_matricula_id' => 'int',
        'turma_dia_letivo_id' => 'int',
        'tempo_aula_id' => 'int',
        'situacao' => TurmaFrequenciaSituacao::class
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma frequência de turma está associada a uma matrícula de aluno
     *
     * @return BelongsTo
     */
    public function alunoMatricula(): BelongsTo
    {
        return $this->belongsTo(AlunoMatricula::class, 'aluno_matricula_id');
    }

    /**
     * Uma frequência de turma está associada a um dia letivo da turma
     *
     * @return BelongsTo
     */
    public function turmaDiaLetivo(): BelongsTo
    {
        return $this->belongsTo(TurmaDiaLetivo::class, 'turma_dia_letivo_id');
    }

    /**
     * Uma frequência de turma está associada a um tempo de aula
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
     * Filtro por aluno_matricula_id
     *
     * @param $query
     * @param int $alunoMatriculaId
     * @return Builder
     */
    public function scopeAlunoMatriculaId($query, int $alunoMatriculaId): Builder
    {
        return $query->where('aluno_matricula_id', $alunoMatriculaId);
    }

    /**
     * Filtro por turma_dia_letivo_id
     *
     * @param $query
     * @param int $turmaDiaLetivoId
     * @return Builder
     */
    public function scopeTurmaDiaLetivoId($query, int $turmaDiaLetivoId): Builder
    {
        return $query->where('turma_dia_letivo_id', $turmaDiaLetivoId);
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

    /**
     * Filtro por situacao
     *
     * @param $query
     * @param TurmaFrequenciaSituacao $situacao
     * @return Builder
     */
    public function scopeSituacao($query, TurmaFrequenciaSituacao $situacao): Builder
    {
        return $query->where('situacao', $situacao);
    }

    /**
     * Filtro para frequências com presença
     *
     * @param $query
     * @return Builder
     */
    public function scopePresente($query): Builder
    {
        return $query->where('situacao', TurmaFrequenciaSituacao::PRESENTE);
    }

    /**
     * Filtro para frequências com falta
     *
     * @param $query
     * @return Builder
     */
    public function scopeFalta($query): Builder
    {
        return $query->where('situacao', TurmaFrequenciaSituacao::FALTA);
    }

    /**
     * Filtro para frequências com falta justificada
     *
     * @param $query
     * @return Builder
     */
    public function scopeFaltaJustificada($query): Builder
    {
        return $query->where('situacao', TurmaFrequenciaSituacao::FALTA_JUSTIFICADA);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da frequência de turma
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da matrícula do aluno associada
     * 
     * @return int
     */
    public function obterAlunoMatriculaId(): int
    {
        return $this->aluno_matricula_id;
    }

    /**
     * Assessor (getter) para obter o ID do dia letivo da turma associado
     * 
     * @return int
     */
    public function obterTurmaDiaLetivoId(): int
    {
        return $this->turma_dia_letivo_id;
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

    /**
     * Assessor (getter) para obter a situação da frequência
     * 
     * @return TurmaFrequenciaSituacao
     */
    public function obterSituacao(): TurmaFrequenciaSituacao
    {
        return $this->situacao;
    }

    /**
     * Verifica se o aluno está presente
     * 
     * @return bool
     */
    public function estaPresente(): bool
    {
        return $this->situacao === TurmaFrequenciaSituacao::PRESENTE;
    }

    /**
     * Verifica se o aluno tem falta
     * 
     * @return bool
     */
    public function temFalta(): bool
    {
        return $this->situacao === TurmaFrequenciaSituacao::FALTA;
    }

    /**
     * Verifica se o aluno tem falta justificada
     * 
     * @return bool
     */
    public function temFaltaJustificada(): bool
    {
        return $this->situacao === TurmaFrequenciaSituacao::FALTA_JUSTIFICADA;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para definir o ID da matrícula do aluno associada
     * 
     * @param int $alunoMatriculaId
     * @return void
     */
    public function atribuirAlunoMatriculaId(int $alunoMatriculaId): void
    {
        $this->aluno_matricula_id = $alunoMatriculaId;
    }

    /**
     * Mutador (setter) para definir o ID do dia letivo da turma associado
     * 
     * @param int $turmaDiaLetivoId
     * @return void
     */
    public function atribuirTurmaDiaLetivoId(int $turmaDiaLetivoId): void
    {
        $this->turma_dia_letivo_id = $turmaDiaLetivoId;
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

    /**
     * Mutador (setter) para definir a situação da frequência
     * 
     * @param TurmaFrequenciaSituacao $situacao
     * @return void
     */
    public function atribuirSituacao(TurmaFrequenciaSituacao $situacao): void
    {
        $this->situacao = $situacao;
    }

    /**
     * Marca o aluno como presente
     * 
     * @return void
     */
    public function marcarPresente(): void
    {
        $this->situacao = TurmaFrequenciaSituacao::PRESENTE;
    }

    /**
     * Marca o aluno com falta
     * 
     * @return void
     */
    public function marcarFalta(): void
    {
        $this->situacao = TurmaFrequenciaSituacao::FALTA;
    }

    /**
     * Marca o aluno com falta justificada
     * 
     * @return void
     */
    public function marcarFaltaJustificada(): void
    {
        $this->situacao = TurmaFrequenciaSituacao::FALTA_JUSTIFICADA;
    }

}