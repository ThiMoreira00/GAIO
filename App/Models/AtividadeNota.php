<?php

/**
 * @file AtividadeNota.php
 * @description Modelo responsável pelas notas das atividades dos alunos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe AtividadeNota
 *
 * Modelo responsável pelas notas das atividades dos alunos
 *
 * @property int $id
 * @property int $atividade_id
 * @property int $usuario_responsavel_id
 * @property int $aluno_matricula_id
 * @property float $nota
 * @property DateTime $data_lancamento
 *
 * @package App\Models
 * @extends Model
 */
class AtividadeNota extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'atividades_notas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'atividade_id',
        'usuario_responsavel_id',
        'aluno_matricula_id',
        'nota',
        'data_lancamento'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'atividade_id' => 'integer',
        'usuario_responsavel_id' => 'integer',
        'aluno_matricula_id' => 'integer',
        'nota' => 'float',
        'data_lancamento' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma nota de atividade pertence a uma atividade
     *
     * @return BelongsTo
     */
    public function atividade(): BelongsTo
    {
        return $this->belongsTo(Atividade::class, 'atividade_id');
    }

    /**
     * Uma nota de atividade pertence a um usuário responsável
     *
     * @return BelongsTo
     */
    public function usuarioResponsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
    }

    /**
     * Uma nota de atividade pertence a uma matrícula de aluno
     *
     * @return BelongsTo
     */
    public function alunoMatricula(): BelongsTo
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
     * Filtro por ID da atividade
     *
     * @param $query
     * @param int $atividadeId
     * @return Builder
     */
    public function scopeAtividadeId($query, int $atividadeId): Builder
    {
        return $query->where('atividade_id', $atividadeId);
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


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro da nota de atividade
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da atividade
     * 
     * @return int
     */
    public function obterAtividadeId(): int
    {
        return $this->atividade_id;
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
     * Assessor (getter) para obter o ID da matrícula do aluno
     * 
     * @return int
     */
    public function obterAlunoMatriculaId(): int
    {
        return $this->aluno_matricula_id;
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
     * Mutador (setter) para atribuir o ID do registro da nota de atividade
     * 
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do registro da atividade
     *
     * @param int $atividadeId
     * @return void
     */
    public function atribuirAtividadeId(int $atividadeId): void
    {
        $this->atividade_id = $atividadeId;
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
     * Função estática para buscar notas por atividade e matrícula do aluno
     * 
     * @param int $atividadeId
     * @param int $alunoMatriculaId
     * @return AtividadeNota|null
     */
    public static function buscarPorAtividadeEAluno(int $atividadeId, int $alunoMatriculaId): ?AtividadeNota
    {
        return self::where('atividade_id', $atividadeId)
            ->where('aluno_matricula_id', $alunoMatriculaId)
            ->first();
    }

    /**
     * Função para obter a média das notas de uma atividade
     * 
     * @param int $atividadeId
     * @return float
     */
    public static function obterMediaPorAtividade(int $atividadeId): float
    {
        return self::where('atividade_id', $atividadeId)->avg('nota') ?? 0.0;
    }

}