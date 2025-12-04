<?php

/**
 * @file AvaliacaoNota.php
 * @description Modelo responsável pelas notas das avaliações dos alunos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\AvaliacaoNotaOrigem;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe AvaliacaoNota
 *
 * Modelo responsável pelas notas das avaliações dos alunos
 *
 * @property int $id
 * @property int $avaliacao_turma_id
 * @property int $aluno_matricula_id
 * @property int $usuario_responsavel_id
 * @property float $nota
 * @property AvaliacaoNotaOrigem $origem
 * @property string|null $observacao
 * @property DateTime $data_lancamento
 *
 * @package App\Models
 * @extends Model
 */
class AvaliacaoNota extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'avaliacoes_notas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'avaliacao_turma_id',
        'aluno_matricula_id',
        'usuario_responsavel_id',
        'nota',
        'origem',
        'observacao',
        'data_lancamento'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'avaliacao_turma_id' => 'integer',
        'aluno_matricula_id' => 'integer',
        'usuario_responsavel_id' => 'integer',
        'nota' => 'float',
        'origem' => AvaliacaoNotaOrigem::class,
        'observacao' => 'string',
        'data_lancamento' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma nota de avaliação pertence a uma avaliação de turma
     *
     * @return BelongsTo
     */
    public function turmaAvaliacao(): BelongsTo
    {
        return $this->belongsTo(TurmaAvaliacao::class, 'turma_avaliacao_id');
    }

    /**
     * Uma nota de avaliação pertence a uma matrícula de aluno
     *
     * @return BelongsTo
     */
    public function alunoMatricula(): BelongsTo
    {
        return $this->belongsTo(AlunoMatricula::class, 'aluno_matricula_id');
    }

    /**
     * Uma nota de avaliação pertence a um usuário responsável
     *
     * @return BelongsTo
     */
    public function usuarioResponsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
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
     * Filtro por ID da avaliação de turma
     *
     * @param $query
     * @param int $avaliacaoTurmaId
     * @return Builder
     */
    public function scopeAvaliacaoTurmaId($query, int $avaliacaoTurmaId): Builder
    {
        return $query->where('avaliacao_turma_id', $avaliacaoTurmaId);
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
     * Filtro por ID do usuário responsável
     *
     * @param $query
     * @param int $usuarioResponsavelId
     * @return Builder
     */
    public function scopeUsuarioResponsavelId($query, int $usuarioResponsavelId): Builder
    {
        return $query->where('usuario_responsavel_id', $usuarioResponsavelId);
    }

    /**
     * Filtro por origem da nota
     *
     * @param $query
     * @param AvaliacaoNotaOrigem $origem
     * @return Builder
     */
    public function scopeOrigem($query, AvaliacaoNotaOrigem $origem): Builder
    {
        return $query->where('origem', $origem);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da nota de avaliação
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da avaliação de turma
     * 
     * @return int
     */
    public function obterAvaliacaoTurmaId(): int
    {
        return $this->avaliacao_turma_id;
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
     * Assessor (getter) para obter o ID do usuário responsável
     * 
     * @return int
     */
    public function obterUsuarioResponsavelId(): int
    {
        return $this->usuario_responsavel_id;
    }

    /**
     * Assessor (getter) para obter a nota
     * 
     * @return float
     */
    public function obterNota(): float
    {
        return $this->nota;
    }

    /**
     * Assessor (getter) para obter a origem da nota
     * 
     * @return AvaliacaoNotaOrigem
     */
    public function obterOrigem(): AvaliacaoNotaOrigem
    {
        return $this->origem;
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
     * Assessor (getter) para obter a data de lançamento
     * 
     * @return DateTime
     */
    public function obterDataLancamento(): DateTime
    {
        return $this->data_lancamento;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID da avaliação de turma
     *
     * @param int $avaliacaoTurmaId
     * @return void
     */
    public function atribuirAvaliacaoTurmaId(int $avaliacaoTurmaId): void
    {
        $this->avaliacao_turma_id = $avaliacaoTurmaId;
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
     * Mutador (setter) para atribuir o ID do usuário responsável
     *
     * @param int $usuarioResponsavelId
     * @return void
     */
    public function atribuirUsuarioResponsavelId(int $usuarioResponsavelId): void
    {
        $this->usuario_responsavel_id = $usuarioResponsavelId;
    }

    /**
     * Mutador (setter) para atribuir a nota
     *
     * @param float $nota
     * @return void
     */
    public function atribuirNota(float $nota): void
    {
        $this->nota = $nota;
    }

    /**
     * Mutador (setter) para atribuir a origem da nota
     *
     * @param AvaliacaoNotaOrigem $origem
     * @return void
     */
    public function atribuirOrigem(AvaliacaoNotaOrigem $origem): void
    {
        $this->origem = $origem;
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
     * Mutador (setter) para atribuir a data de lançamento
     *
     * @param DateTime $dataLancamento
     * @return void
     */
    public function atribuirDataLancamento(DateTime $dataLancamento): void
    {
        $this->data_lancamento = $dataLancamento;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se a nota é aprovada (>= 6.0)
     * 
     * @return bool
     */
    public function verificarAprovado(): bool
    {
        return $this->nota >= 6.0;
    }

    /**
     * Função para verificar se a nota é de origem manual
     * 
     * @return bool
     */
    public function verificarOrigemManual(): bool
    {
        return $this->origem === AvaliacaoNotaOrigem::MANUAL;
    }

    /**
     * Função para verificar se a nota é de origem de atividade
     * 
     * @return bool
     */
    public function verificarOrigemAtividade(): bool
    {
        return $this->origem === AvaliacaoNotaOrigem::ATIVIDADE;
    }

    /**
     * Função estática para buscar notas por avaliação e matrícula do aluno
     * 
     * @param int $avaliacaoTurmaId
     * @param int $alunoMatriculaId
     * @return AvaliacaoNota|null
     */
    public static function buscarPorAvaliacaoEAluno(int $avaliacaoTurmaId, int $alunoMatriculaId): ?AvaliacaoNota
    {
        return self::where('avaliacao_turma_id', $avaliacaoTurmaId)
            ->where('aluno_matricula_id', $alunoMatriculaId)
            ->first();
    }

    /**
     * Função para obter a média das notas de uma avaliação de turma
     * 
     * @param int $avaliacaoTurmaId
     * @return float
     */
    public static function obterMediaPorAvaliacao(int $avaliacaoTurmaId): float
    {
        return self::where('avaliacao_turma_id', $avaliacaoTurmaId)->avg('nota') ?? 0.0;
    }

}