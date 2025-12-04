<?php

/**
 * @file AlunoMatricula.php
 * @description Modelo responsável pelas matrículas dos alunos registrados no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\AlunoMatriculaStatus;
use App\Models\Enumerations\Turno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Date;
use DateTime;

/**
 * Classe AlunoMatricula
 *
 * Modelo responsável pelas matrículas dos alunos registrados no sistema
 *
 * @property int $id
 * @property int $aluno_id
 * @property int $matriz_curricular_id
 * @property int $periodo_ingresso_id
 * @property string $matricula
 * @property Turno $turno
 * @property DateTime $data_matricula
 * @property int $ingresso_tipo_id
 * @property int $ingresso_classificacao
 * @property int $ingresso_pontos
 * @property AlunoMatriculaStatus $status
 *
 * @package App\Models
 * @extends Model
 */
class AlunoMatricula extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos_matriculas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'aluno_id',
        'matriz_curricular_id',
        'periodo_ingresso_id',
        'matricula',
        'turno',
        'data_matricula',
        'ingresso_tipo_id',
        'ingresso_classificacao',
        'ingresso_pontos',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'aluno_id' => 'integer',
        'matriz_curricular_id' => 'integer',
        'periodo_ingresso_id' => 'integer',
        'matricula' => 'string',
        'data_matricula' => 'date',
        'ingresso_tipo_id' => 'integer',
        'ingresso_classificacao' => 'integer',
        'ingresso_pontos' => 'integer',
        'turno' => Turno::class,
        'status' => AlunoMatriculaStatus::class
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma matrícula pertence a um aluno (Aluno)
     *
     * @return BelongsTo
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    /**
     * Uma matrícula pertence a uma matriz curricular
     *
     * @return BelongsTo
     */
    public function matrizCurricular(): BelongsTo
    {
        return $this->belongsTo(MatrizCurricular::class, 'matriz_curricular_id');
    }

    /**
     * Uma matrícula tem acesso ao curso através da matriz curricular
     *
     * @return BelongsTo
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id')
            ->through('matrizCurricular');
    }

    /**
     * Uma matrícula pertence a um período letivo de ingresso
     *
     * @return BelongsTo
     */
    public function periodoIngresso(): BelongsTo
    {
        return $this->belongsTo(PeriodoLetivo::class, 'periodo_ingresso_id');
    }

    /**
     * Uma matrícula pertence a um tipo de ingresso
     *
     * @return BelongsTo
     */
    public function ingressoTipo(): BelongsTo
    {
        return $this->belongsTo(AlunoIngressoTipo::class, 'ingresso_tipo_id');
    }

    /**
     * Uma matrícula pode possuir vários registros de histórico
     *
     * @return HasMany
     */
    public function historicos(): HasMany
    {
        return $this->hasMany(AlunoMatriculaHistorico::class, 'matricula_id');
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
     * Filtro por ID da matriz curricular
     * 
     * @param $query
     * @param int $matrizCurricularId
     * @return Builder
     */
    public function scopeMatrizCurricularId($query, int $matrizCurricularId): Builder
    {
        return $query->where('matriz_curricular_id', $matrizCurricularId);
    }

    /**
     * Filtro por ID do aluno
     * 
     * @param $query
     * @param int $alunoId
     * @return Builder
     */
    public function scopeAlunoId($query, int $alunoId): Builder
    {
        return $query->where('aluno_id', $alunoId);
    }

    /**
     * Filtro por status da matrícula
     * 
     * @param $query
     * @param AlunoMatriculaStatus $status
     * @return Builder
     */
    public function scopeStatus($query, AlunoMatriculaStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Filtro por turno da matrícula
     * 
     * @param $query
     * @param Turno $turno
     * @return Builder
     */
    public function scopeTurno($query, Turno $turno): Builder
    {
        return $query->where('turno', $turno);
    }

    /**
     * Filtro por matrícula do aluno
     * 
     * @param $query
     * @param string $matricula
     * @return Builder
     */
    public function scopeMatricula($query, string $matricula): Builder
    {
        return $query->where('matricula', $matricula);
    }



    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro da matrícula
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do aluno associado à matrícula
     * 
     * @return int
     */
    public function obterAlunoId(): int
    {
        return $this->aluno_id;
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
     * Assessor (getter) para obter o ID do período de ingresso
     * 
     * @return int
     */
    public function obterPeriodoIngressoId(): int
    {
        return $this->periodo_ingresso_id;
    }

    /**
     * Assessor (getter) para obter a matrícula do aluno
     * 
     * @return string
     */
    public function obterMatricula(): string
    {
        return $this->matricula;
    }

    /**
     * Assessor (getter) para obter o turno da matrícula
     * 
     * @return Turno
     */
    public function obterTurno(): Turno
    {
        return $this->turno instanceof Turno ? $this->turno : Turno::tryFrom($this->turno);
    }

    /**
     * Assessor (getter) para obter o ID do tipo do ingresso
     * 
     * @return int
     */
    public function obterIngressoTipoId(): int
    {
        return $this->ingresso_tipo_id;
    }

    /**
     * Assessor (getter) para obter a classificação do ingresso
     * 
     * @return int
     */
    public function obterIngressoClassificacao(): int
    {
        return $this->ingresso_classificacao;
    }

    /**
     * Assessor (getter) para obter os pontos do ingresso
     * 
     * @return int
     */
    public function obterIngressoPontos(): int
    {
        return $this->ingresso_pontos;
    }

    /**
     * Assessor (getter) para obter o status da matrícula
     * 
     * @return AlunoMatriculaStatus
     */
    public function obterStatus(): AlunoMatriculaStatus
    {
        return $this->status;
    }

    /**
     * Assessor (getter) para obter a data da matrícula
     * 
     * @return DateTime
     */
    public function obterDataMatricula(): DateTime
    {
        return $this->data_matricula;
    }

    /**
     * Assessor (getter) para obter a data do registro 
     * 
     * @return DateTime
     */
    public function obterDataRegistro(): DateTime
    {
        return $this->data_registro;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro da matrícula
     * 
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do aluno associado à matrícula
     *
     * @param int $alunoId
     * @return void
     */
    public function atribuirAlunoId(int $alunoId): void
    {
        $this->aluno_id = $alunoId;
    }

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
     * Mutador (setter) para atribuir o ID do período de ingresso
     *
     * @param int $periodoIngressoId
     * @return void
     */
    public function atribuirPeriodoIngressoId(int $periodoIngressoId): void
    {
        $this->periodo_ingresso_id = $periodoIngressoId;
    }

    /**
     * Mutador (setter) para atribuir a matrícula do aluno
     *
     * @param string $matricula
     * @return void
     */
    public function atribuirMatricula(string $matricula): void
    {
        $this->matricula = $matricula;
    }

    /**
     * Mutador (setter) para atribuir o turno da matrícula
     *
     * @param Turno $turno
     * @return void
     */
    public function atribuirTurno(Turno $turno): void
    {
        $this->turno = $turno;
    }

    /**
     * Mutador (setter) para atribuir o ID do tipo do ingresso
     *
     * @param int $ingressoTipoId
     * @return void
     */
    public function atribuirIngressoTipoId(int $ingressoTipoId): void
    {
        $this->ingresso_tipo_id = $ingressoTipoId;
    }

    /**
     * Mutador (setter) para atribuir a classificação do ingresso
     *
     * @param int $ingressoClassificacao
     * @return void
     */
    public function atribuirIngressoClassificacao(int $ingressoClassificacao): void
    {
        $this->ingresso_classificacao = $ingressoClassificacao;
    }

    /**
     * Mutador (setter) para atribuir os pontos do ingresso
     *
     * @param int $ingressoPontos
     * @return void
     */
    public function atribuirIngressoPontos(int $ingressoPontos): void
    {
        $this->ingresso_pontos = $ingressoPontos;
    }

    /**
     * Mutador (setter) para atribuir o status da matrícula
     *
     * @param AlunoMatriculaStatus $status
     * @return void
     */
    public function atribuirStatus(AlunoMatriculaStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Mutador (setter) para atribuir a data da matrícula
     *
     * @param DateTime $dataMatricula
     * @return void
     */
    public function atribuirDataMatricula(DateTime $dataMatricula): void
    {
        $this->data_matricula = $dataMatricula;
    }

    /**
     * Mutador (setter) para atribuir a data do registro da matrícula
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
     * Função para obter a última matrícula do aluno
     * 
     * @return ?AlunoMatricula
     */
    public function obterUltimaMatricula(): ?AlunoMatricula
    {
        return self::where('aluno_id', $this->aluno_id)
            ->orderBy('data_matricula', 'desc')
            ->first();
    }

    /**
     * Função estática para buscar uma matrícula pelo número
     * 
     * @param string $numeroMatricula
     * @return ?AlunoMatricula
     */
    public static function buscarPorMatricula(string $numeroMatricula): ?AlunoMatricula
    {
        return self::matriculas($numeroMatricula)->first();
    }

    /**
     * Função estática para obter todas as matrículas ativas (com status 'CURSANDO' ou 'TRANCADO')
     *
     * @return Builder
     */
    public static function obterAtivos(): Builder
    {
        return self::whereIn('status', [
            AlunoMatriculaStatus::CURSANDO,
            AlunoMatriculaStatus::TRANCADO
        ]);
    }

    /**
     * Função para obter o ID do curso através da matriz curricular
     * 
     * @return ?int
     */
    public function obterCursoId(): ?int
    {
        return $this->matrizCurricular?->curso_id;
    }

    /**
     * Função estática para contar matrículas ativas
     *
     * @return int
     */
    public static function contarAtivos(): int
    {
        return self::whereIn('status', [
            AlunoMatriculaStatus::CURSANDO,
            AlunoMatriculaStatus::TRANCADO
        ])->count();
    }

    /**
     * Função para calcular quantos períodos o aluno já cursou
     * 
     * @return int
     */
    public function calcularPeriodosCursados(): int
    {
        try {
            $periodoAtual = PeriodoLetivo::obterAtual();
            $periodoIngresso = $this->periodoIngresso;
            
            if (!$periodoIngresso) {
                return 0;
            }
            
            return PeriodoLetivo::contarPeriodosEntre(
                $periodoIngresso->obterId(),
                $periodoAtual->obterId()
            );
            
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Função para verificar se o aluno está integralizando
     * 
     * @return bool
     */
    public function verificarIntegralizando(): bool
    {
        $periodosCursados = $this->calcularPeriodosCursados();
        $curso = $this->matrizCurricular->curso;
        $periodosNecessarios = $curso->obterDuracaoMinima();
        
        return $periodosCursados >= $periodosNecessarios;
    }

    /**
     * Função estática para obter alunos que estão integralizando
     * 
     * @return Collection
     */
    public static function obterIntegralizando(): Collection
    {
        $matriculasAtivas = self::obterAtivos()
            ->with(['matrizCurricular.curso', 'periodoIngresso'])
            ->get();
        
        return $matriculasAtivas->filter(function($matricula) {
            return $matricula->verificarIntegralizando();
        });
    }

    /**
     * Função para verificar se a matrícula está ativa
     * 
     * @param int $alunoId
     * @return ?AlunoMatricula
     */
    public static function buscarMatriculaAtivaPorAluno(int $alunoId): ?AlunoMatricula
    {
        return self::where('aluno_id', $alunoId)
            ->whereIn('status', [
                AlunoMatriculaStatus::CURSANDO->name,
                AlunoMatriculaStatus::TRANCADO->name
            ])->with('matrizCurricular')
            ->orderBy('data_matricula', 'desc')
            ->first();

    }

}
