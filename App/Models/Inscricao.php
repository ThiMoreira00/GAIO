<?php

/**
 * @file Inscricao.php
 * @description Modelo responsável pelas inscrições dos alunos nas turmas
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\InscricaoStatus;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

/**
 * Classe Inscricao
 *
 * Modelo responsável pelas inscrições dos alunos nas turmas
 *
 * @property int $id
 * @property int $aluno_matricula_id
 * @property int $turma_id
 * @property string|null $observacao
 * @property string $status
 * @property DateTime $data_registro
 *
 * @package App\Models
 * @extends Model
 */
class Inscricao extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'inscricoes';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'aluno_matricula_id',
        'turma_id',
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
        'turma_id' => 'integer',
        'observacao' => 'string',
        'status' => 'string',
        'data_registro' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma inscrição pertence a uma matrícula de aluno
     *
     * @return BelongsTo
     */
    public function alunoMatricula(): BelongsTo
    {
        return $this->belongsTo(AlunoMatricula::class, 'aluno_matricula_id');
    }

    /**
     * Uma inscrição pertence a uma turma
     *
     * @return BelongsTo
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'turma_id');
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
     * Filtro por ID da matrícula do aluno
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
     * Filtro por ID da turma
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
     * Filtro por status
     *
     * @param $query
     * @param InscricaoStatus $status
     * @return Builder
     */
    public function scopeStatus($query, InscricaoStatus $status): Builder
    {
        return $query->where('status', $status);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de inscrição
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da matrícula do aluno
     * 
     * @return int
     */
    public function obterAlunoMatriculaId(): int
    {
        return $this->aluno_matricula_id;
    }

    /**
     * Assessor (getter) para obter o ID da turma
     * 
     * @return int
     */
    public function obterTurmaId(): int
    {
        return $this->turma_id;
    }

    /**
     * Assessor (getter) para obter a observação
     * 
     * @return string|null
     */
    public function obterObservacao(): ?string
    {
        return $this->observacao;
    }

    /**
     * Assessor (getter) para obter o status
     * 
     * @return InscricaoStatus
     */
    public function obterStatus(): InscricaoStatus
    {
        return InscricaoStatus::fromName($this->status);
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
     * Mutador (setter) para atribuir o ID do registro de inscrição
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID da matrícula do aluno
     *
     * @param int $alunoMatriculaId
     * @return void
     */
    public function atribuirAlunoMatriculaId(int $alunoMatriculaId): void
    {
        $this->aluno_matricula_id = $alunoMatriculaId;
    }

    /**
     * Mutador (setter) para atribuir o ID da turma
     *
     * @param int $turmaId
     * @return void
     */
    public function atribuirTurmaId(int $turmaId): void
    {
        $this->turma_id = $turmaId;
    }

    /**
     * Mutador (setter) para atribuir a observação
     *
     * @param string|null $observacao
     * @return void
     */
    public function atribuirObservacao(?string $observacao): void
    {
        $this->observacao = $observacao;
    }

    /**
     * Mutador (setter) para atribuir o status
     *
     * @param InscricaoStatus $status
     * @return void
     */
    public function atribuirStatus(InscricaoStatus $status): void
    {
        $this->status = $status->name;
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


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se a inscrição está aprovada
     * 
     * @return bool
     */
    public function verificarAprovado(): bool
    {
        return $this->status === InscricaoStatus::APROVADO;
    }

    /**
     * Função para verificar se a inscrição foi reprovada
     * 
     * @return bool
     */
    public function verificarReprovado(): bool
    {
        return $this->status === InscricaoStatus::REPROVADO_FALTA 
            || $this->status === InscricaoStatus::REPROVADO_MEDIA;
    }

    /**
     * Função para verificar se a inscrição está cursando
     * 
     * @return bool
     */
    public function verificarCursando(): bool
    {
        return $this->status === InscricaoStatus::CURSANDO;
    }

    /**
     * Função para verificar se a inscrição foi deferida
     * 
     * @return bool
     */
    public function verificarDeferida(): bool
    {
        return $this->status === InscricaoStatus::DEFERIDA;
    }

    /**
     * Função para verificar se a inscrição foi indeferida
     * 
     * @return bool
     */
    public function verificarIndeferida(): bool
    {
        return $this->status === InscricaoStatus::INDEFERIDA;
    }

    /**
     * Função estática para buscar inscrições por aluno e turma
     * 
     * @param int $alunoMatriculaId
     * @param int $turmaId
     * @return Inscricao|null
     */
    public static function buscarPorAlunoETurma(int $alunoMatriculaId, int $turmaId): ?Inscricao
    {
        return self::where('aluno_matricula_id', $alunoMatriculaId)
            ->where('turma_id', $turmaId)
            ->first();
    }

    /**
     * Função estática para buscar inscrições por turma e status
     * 
     * @param int $turmaId
     * @param InscricaoStatus $status
     * @return Collection
     */
    public static function buscarPorTurma(int $turmaId, ?InscricaoStatus $status = null): Collection
    {
        return self::where('turma_id', $turmaId)
            ->when($status !== null, function ($query) use ($status) {
                return $query->where('status', $status->value);
            })
            ->get();
    }

    /**
     * Buscar disciplinas já cursadas (aprovadas) pelo aluno
     * 
     * @param int $alunoMatriculaId
     * @return array Array de IDs de disciplinas
     */
    public static function buscarDisciplinasAprovadas(int $alunoMatriculaId): array
    {
        return self::where('inscricoes.aluno_matricula_id', $alunoMatriculaId)
            ->whereIn('inscricoes.status', [
                InscricaoStatus::APROVADO->name
            ])
            ->join('turmas', 'inscricoes.turma_id', '=', 'turmas.id')
            ->pluck('turmas.disciplina_id')
            ->unique()
            ->toArray();
    }

    /**
     * Buscar inscrições do aluno em turmas específicas
     * 
     * @param int $alunoMatriculaId
     * @param array $turmaIds
     * @return Collection Coleção indexada por turma_id
     */
    public static function buscarPorAlunoETurmas(int $alunoMatriculaId, array $turmaIds): Collection
    {
        return self::where('aluno_matricula_id', $alunoMatriculaId)
            ->whereIn('turma_id', $turmaIds)
            ->get()
            ->keyBy('turma_id');
    }


}