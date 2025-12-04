<?php

/**
 * @file Turma.php
 * @description Modelo responsável pelas turmas do sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\Turno;
use App\Models\Enumerations\EnsinoModalidade;
use App\Models\Enumerations\TurmaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe Turma
 *
 * Modelo responsável pelas turmas do sistema
 *
 * @property int $id
 * @property int $disciplina_id
 * @property int $periodo_id
 * @property int $professor_id
 * @property string $codigo
 * @property int $grade_id
 * @property Turno $turno
 * @property int $capacidade_maxima
 * @property EnsinoModalidade $modalidade
 * @property TurmaStatus $status
 *
 * @package App\Models
 * @extends Model
 */
class Turma extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'turmas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'disciplina_id',
        'periodo_id',
        'professor_id',
        'codigo',
        'grade_id',
        'turno',
        'capacidade_maxima',
        'modalidade',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'disciplina_id' => 'integer',
        'periodo_id' => 'integer',
        'professor_id' => 'integer',
        'codigo' => 'string',
        'grade_id' => 'integer',
        'turno' => 'string',
        'capacidade_maxima' => 'integer',
        'modalidade' => 'string',
        'status' => 'string'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma turma pertence a uma disciplina
     *
     * @return BelongsTo
     */
    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class, 'disciplina_id');
    }

    /**
     * Uma turma pertence a um período letivo
     *
     * @return BelongsTo
     */
    public function periodo(): BelongsTo
    {
        return $this->belongsTo(PeriodoLetivo::class, 'periodo_letivo_id');
    }

    /**
     * Uma turma pertence a um professor
     *
     * @return BelongsTo
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }

    /**
     * Uma turma pertence a uma grade horária
     *
     * @return BelongsTo
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(GradeHoraria::class, 'grade_id');
    }

    /**
     * Uma turma pode ter várias avaliações
     *
     * @return HasMany
     */
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(TurmaAvaliacao::class, 'turma_id');
    }

    /**
     * Uma turma pode ter vários dias letivos
     *
     * @return HasMany
     */
    public function diasLetivos(): HasMany
    {
        return $this->hasMany(TurmaDiaLetivo::class, 'turma_id');
    }

    /**
     * Uma turma pode ter vários horários
     *
     * @return HasMany
     */
    public function horarios(): HasMany
    {
        return $this->hasMany(TurmaHorario::class, 'turma_id');
    }

    /**
     * Uma turma pode ter várias inscrições
     *
     * @return HasMany
     */
    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class, 'turma_id');
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
     * Filtro por ID do período
     *
     * @param $query
     * @param int $periodoId
     * @return Builder
     */
    public function scopePeriodoId($query, int $periodoId): Builder
    {
        return $query->where('periodo_id', $periodoId);
    }

    /**
     * Filtro por ID do professor
     *
     * @param $query
     * @param int $professorId
     * @return Builder
     */
    public function scopeProfessorId($query, int $professorId): Builder
    {
        return $query->where('professor_id', $professorId);
    }

    /**
     * Filtro por código (parcial)
     *
     * @param $query
     * @param string $codigo
     * @return Builder
     */
    public function scopeCodigo($query, string $codigo): Builder
    {
        return $query->where('codigo', 'like', '%' . $codigo . '%');
    }

    /**
     * Filtro por turno
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
     * Filtro por modalidade
     *
     * @param $query
     * @param EnsinoModalidade $modalidade
     * @return Builder
     */
    public function scopeModalidade($query, EnsinoModalidade $modalidade): Builder
    {
        return $query->where('modalidade', $modalidade);
    }

    /**
     * Filtro por status
     *
     * @param $query
     * @param TurmaStatus $status
     * @return Builder
     */
    public function scopeStatus($query, TurmaStatus $status): Builder
    {
        return $query->where('status', $status);
    }





    public function scopeOfertasAtivas($query)
    {
        return $query->whereIn('status', [TurmaStatus::OFERTADA->name, TurmaStatus::CONFIRMADA->name]);
    }

    public function scopeCurso($query, $cursoId)
    {
        return $query->whereHas('disciplina.componenteCurricular.matrizCurricular', function ($q) use ($cursoId) {
            $q->where('curso_id', $cursoId);
        });
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de turma
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da disciplina
     * 
     * @return int
     */
    public function obterDisciplinaId(): int
    {
        return $this->disciplina_id;
    }

    /**
     * Assessor (getter) para obter o ID do período
     * 
     * @return int
     */
    public function obterPeriodoId(): int
    {
        return $this->periodo_id;
    }

    /**
     * Assessor (getter) para obter o período
     * 
     * @return PeriodoLetivo
     */
    public function obterPeriodo(): PeriodoLetivo
    {
        return $this->periodo()->first();
    }

    /**
     * Assessor (getter) para obter o ID do professor
     * 
     * @return int
     */
    public function obterProfessorId(): int
    {
        return $this->professor_id;
    }

    /**
     * Assessor (getter) para obter o código
     * 
     * @return string
     */
    public function obterCodigo(): string
    {
        return $this->codigo;
    }

    /**
     * Assessor (getter) para obter o ID da grade
     * 
     * @return int
     */
    public function obterGradeId(): int
    {
        return $this->grade_id;
    }

    /**
     * Assessor (getter) para obter o turno
     * 
     * @return Turno
     */
    public function obterTurno(): Turno
    {
        $turno = $this->attributes['turno'] ?? $this->turno;
        return $turno instanceof Turno
            ? $turno
            : Turno::fromName(trim($turno));
    }

    /**
     * Assessor (getter) para obter a capacidade máxima
     * 
     * @return int
     */
    public function obterCapacidadeMaxima(): int
    {
        return $this->capacidade_maxima;
    }

    /**
     * Assessor (getter) para obter a modalidade
     * 
     * @return EnsinoModalidade
     */
    public function obterModalidade(): EnsinoModalidade
    {
        $modalidade = $this->attributes['modalidade'] ?? $this->modalidade;
        return $modalidade instanceof EnsinoModalidade
            ? $modalidade
            : EnsinoModalidade::fromName(trim($modalidade));
    }

    /**
     * Assessor (getter) para obter o status
     * 
     * @return TurmaStatus
     */
    public function obterStatus(): TurmaStatus
    {
        $status = $this->attributes['status'] ?? $this->status;
        return $status instanceof TurmaStatus
            ? $status
            : TurmaStatus::fromName(trim($status));
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de turma
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID da disciplina
     *
     * @param int $disciplinaId
     * @return void
     */
    public function atribuirDisciplinaId(int $disciplinaId): void
    {
        $this->disciplina_id = $disciplinaId;
    }

    /**
     * Mutador (setter) para atribuir o ID do período
     *
     * @param int $periodoId
     * @return void
     */
    public function atribuirPeriodoId(int $periodoId): void
    {
        $this->periodo_id = $periodoId;
    }

    /**
     * Mutador (setter) para atribuir o ID do professor
     *
     * @param int $professorId
     * @return void
     */
    public function atribuirProfessorId(int $professorId): void
    {
        $this->professor_id = $professorId;
    }

    /**
     * Mutador (setter) para atribuir o código
     *
     * @param string $codigo
     * @return void
     */
    public function atribuirCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * Mutador (setter) para atribuir o ID da grade
     *
     * @param int $gradeId
     * @return void
     */
    public function atribuirGradeId(int $gradeId): void
    {
        $this->grade_id = $gradeId;
    }

    /**
     * Mutador (setter) para atribuir o turno
     *
     * @param Turno $turno
     * @return void
     */
    public function atribuirTurno(Turno $turno): void
    {
        $this->attributes['turno'] = $turno->name;
    }

    /**
     * Mutador (setter) para atribuir a capacidade máxima
     *
     * @param int $capacidadeMaxima
     * @return void
     */
    public function atribuirCapacidadeMaxima(int $capacidadeMaxima): void
    {
        $this->capacidade_maxima = $capacidadeMaxima;
    }

    /**
     * Mutador (setter) para atribuir a modalidade
     *
     * @param EnsinoModalidade $modalidade
     * @return void
     */
    public function atribuirModalidade(EnsinoModalidade $modalidade): void
    {
        $this->attributes['modalidade'] = $modalidade->name;
    }

    /**
     * Mutador (setter) para atribuir o status
     *
     * @param TurmaStatus $status
     * @return void
     */
    public function atribuirStatus(TurmaStatus $status): void
    {
        $this->attributes['status'] = $status->name;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se a turma está ativa
     * 
     * @return bool
     */
    public function verificarAtiva(): bool
    {
        return $this->status === TurmaStatus::ATIVA;
    }

    /**
     * Função para verificar se a turma está concluída
     * 
     * @return bool
     */
    public function verificarConcluida(): bool
    {
        return $this->status === TurmaStatus::CONCLUIDA;
    }

    /**
     * Função para verificar se a turma está cancelada
     * 
     * @return bool
     */
    public function verificarCancelada(): bool
    {
        return $this->status === TurmaStatus::CANCELADA;
    }

    /**
     * Função para verificar se a turma é presencial
     * 
     * @return bool
     */
    public function verificarPresencial(): bool
    {
        return $this->modalidade === EnsinoModalidade::PRESENCIAL;
    }

    /**
     * Função para verificar se a turma é remota
     * 
     * @return bool
     */
    public function verificarRemota(): bool
    {
        return $this->modalidade === EnsinoModalidade::REMOTA;
    }

    /**
     * Função para verificar se a turma é híbrida
     * 
     * @return bool
     */
    public function verificarHibrida(): bool
    {
        return $this->modalidade === EnsinoModalidade::HIBRIDA;
    }

    /**
     * Função para obter quantidade de inscrições
     * 
     * @return int
     */
    public function obterQuantidadeInscricoes(): int
    {
        return $this->inscricoes()->count();
    }

    /**
     * Função para verificar se há vagas disponíveis
     * 
     * @return bool
     */
    public function verificarVagasDisponiveis(): bool
    {
        return $this->obterQuantidadeInscricoes() < $this->capacidade_maxima;
    }

    /**
     * Função para obter quantidade de vagas disponíveis
     * 
     * @return int
     */
    public function obterVagasDisponiveis(): int
    {
        $ocupadas = $this->obterQuantidadeInscricoes();
        $disponiveis = $this->capacidade_maxima - $ocupadas;
        return $disponiveis > 0 ? $disponiveis : 0;
    }

    /**
     * Função para calcular percentual de ocupação
     * 
     * @return float
     */
    public function calcularPercentualOcupacao(): float
    {
        if ($this->capacidade_maxima === 0) {
            return 0.0;
        }

        return round(($this->obterQuantidadeInscricoes() / $this->capacidade_maxima) * 100, 2);
    }

    /**
     * Função estática para buscar turmas ativas
     * 
     * @return Collection
     */
    public static function buscarAtivas()
    {
        return self::where('status', TurmaStatus::ATIVA)->get();
    }

    /**
     * Função estática para buscar por professor
     * 
     * @param int $professorId
     * @return Collection
     */
    public static function buscarPorProfessor(int $professorId)
    {
        return self::where('professor_id', $professorId)->get();
    }

    /**
     * Função estática para buscar por período e disciplina
     * 
     * @param int $periodoId
     * @param int $disciplinaId
     * @return Collection
     */
    public static function buscarPorPeriodoEDisciplina(int $periodoId, int $disciplinaId)
    {
        return self::where('periodo_id', $periodoId)
            ->where('disciplina_id', $disciplinaId)
            ->get();
    }

    /**
     * Função estática para buscar por código
     * 
     * @param string $codigo
     * @return self|null
     */
    public static function buscarPorCodigo(string $codigo): ?self
    {
        return self::where('codigo', $codigo)->first();
    }

    /**
     * Função estática para obter todas as turmas ativas (com status 'ATIVA')
     *
     * @return Collection
     */
    public static function obterAtivas(): Collection
    {
        return self::where('status', TurmaStatus::ATIVA)->get();
    }

    /**
     * Função estática para contar turmas ativas
     *
     * @return int
     */
    public static function contarAtivas(): int
    {
        return self::where('status', TurmaStatus::ATIVA)->count();
    }

    /**
     * Função estática para formatar as informações da turma
     * 
     * @param Turma $turma
     * @return array
     */
    public static function formatarInformacoes(Turma $turma): array
    {
        return [
            'id' => $turma->obterId(),
            'disciplina_id' => $turma->obterDisciplinaId(),
            'periodo_id' => $turma->obterPeriodoId(),
            'professor_id' => $turma->obterProfessorId(),
            'codigo' => $turma->obterCodigo(),
            'grade_id' => $turma->obterGradeId(),
            'turno' => $turma->obterTurno()->value,
            'capacidade_maxima' => $turma->obterCapacidadeMaxima(),
            'modalidade' => $turma->obterModalidade()->value,
            'status' => $turma->obterStatus()->value
        ];
    }
}
