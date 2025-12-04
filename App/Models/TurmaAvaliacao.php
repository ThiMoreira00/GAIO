<?php

/**
 * @file TurmaAvaliacao.php
 * @description Modelo responsável pelas avaliações das turmas no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe TurmaAvaliacao
 *
 * Modelo responsável pelas avaliações das turmas no sistema
 *
 * @property int $id
 * @property int $turma_id
 * @property int $avaliacao_tipo_id
 * @property string $nome
 * @property string $formula
 * @property float $peso
 * @property string $condicao_aplicacao
 * @property bool $status
 *
 * @package App\Models
 * @extends Model
 */
class TurmaAvaliacao extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'turma_avaliacoes';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'turma_id',
        'avaliacao_tipo_id',
        'nome',
        'formula',
        'peso',
        'condicao_aplicacao',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'turma_id' => 'int',
        'avaliacao_tipo_id' => 'int',
        'nome' => 'string',
        'formula' => 'string',
        'peso' => 'float',
        'condicao_aplicacao' => 'string',
        'status' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma avaliação de turma está associada a uma turma
     *
     * @return BelongsTo
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    /**
     * Uma avaliação de turma está associada a um tipo de avaliação
     *
     * @return BelongsTo
     */
    public function avaliacaoTipo(): BelongsTo
    {
        return $this->belongsTo(AvaliacaoTipo::class, 'avaliacao_tipo_id');
    }

    /**
     * Uma avaliação de turma possui muitas notas de avaliação
     *
     * @return HasMany
     */
    public function avaliacaoNotas(): HasMany
    {
        return $this->hasMany(AvaliacaoNota::class, 'turma_avaliacao_id');
    }

    /**
     * Uma avaliação de turma possui muitas atividades
     *
     * @return HasMany
     */
    public function atividades(): HasMany
    {
        return $this->hasMany(Atividade::class, 'avaliacao_turma_id');
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
     * Filtro por turma_id
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
     * Filtro por avaliacao_tipo_id
     *
     * @param $query
     * @param int $avaliacaoTipoId
     * @return Builder
     */
    public function scopeAvaliacaoTipoId($query, int $avaliacaoTipoId): Builder
    {
        return $query->where('avaliacao_tipo_id', $avaliacaoTipoId);
    }

    /**
     * Filtro por nome
     *
     * @param $query
     * @param string $nome
     * @return Builder
     */
    public function scopeNome($query, string $nome): Builder
    {
        return $query->where('nome', 'LIKE', "%{$nome}%");
    }

    /**
     * Filtro por status
     *
     * @param $query
     * @param bool $status
     * @return Builder
     */
    public function scopeStatus($query, bool $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Filtro para avaliações ativas
     *
     * @param $query
     * @return Builder
     */
    public function scopeAtivas($query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Filtro para avaliações inativas
     *
     * @param $query
     * @return Builder
     */
    public function scopeInativas($query): Builder
    {
        return $query->where('status', false);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da avaliação de turma
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da turma associada
     * 
     * @return int
     */
    public function obterTurmaId(): int
    {
        return $this->turma_id;
    }

    /**
     * Assessor (getter) para obter o ID do tipo de avaliação associado
     * 
     * @return int
     */
    public function obterAvaliacaoTipoId(): int
    {
        return $this->avaliacao_tipo_id;
    }

    /**
     * Assessor (getter) para obter o nome da avaliação
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter a fórmula da avaliação
     * 
     * @return string
     */
    public function obterFormula(): string
    {
        return $this->formula;
    }

    /**
     * Assessor (getter) para obter o peso da avaliação
     * 
     * @return float
     */
    public function obterPeso(): float
    {
        return $this->peso;
    }

    /**
     * Assessor (getter) para obter a condição de aplicação
     * 
     * @return string
     */
    public function obterCondicaoAplicacao(): string
    {
        return $this->condicao_aplicacao;
    }

    /**
     * Assessor (getter) para obter o status da avaliação
     * 
     * @return bool
     */
    public function obterStatus(): bool
    {
        return $this->status;
    }

    /**
     * Verifica se a avaliação está ativa
     * 
     * @return bool
     */
    public function estaAtiva(): bool
    {
        return $this->status === true;
    }

    /**
     * Verifica se a avaliação está inativa
     * 
     * @return bool
     */
    public function estaInativa(): bool
    {
        return $this->status === false;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para definir o ID da turma associada
     * 
     * @param int $turmaId
     * @return void
     */
    public function atribuirTurmaId(int $turmaId): void
    {
        $this->turma_id = $turmaId;
    }

    /**
     * Mutador (setter) para definir o ID do tipo de avaliação associado
     * 
     * @param int $avaliacaoTipoId
     * @return void
     */
    public function atribuirAvaliacaoTipoId(int $avaliacaoTipoId): void
    {
        $this->avaliacao_tipo_id = $avaliacaoTipoId;
    }

    /**
     * Mutador (setter) para definir o nome da avaliação
     * 
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para definir a fórmula da avaliação
     * 
     * @param string $formula
     * @return void
     */
    public function atribuirFormula(string $formula): void
    {
        $this->formula = $formula;
    }

    /**
     * Mutador (setter) para definir o peso da avaliação
     * 
     * @param float $peso
     * @return void
     */
    public function atribuirPeso(float $peso): void
    {
        $this->peso = $peso;
    }

    /**
     * Mutador (setter) para definir a condição de aplicação
     * 
     * @param string $condicaoAplicacao
     * @return void
     */
    public function atribuirCondicaoAplicacao(string $condicaoAplicacao): void
    {
        $this->condicao_aplicacao = $condicaoAplicacao;
    }

    /**
     * Mutador (setter) para definir o status da avaliação
     * 
     * @param bool $status
     * @return void
     */
    public function atribuirStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * Ativa a avaliação
     * 
     * @return void
     */
    public function ativar(): void
    {
        $this->status = true;
    }

    /**
     * Desativa a avaliação
     * 
     * @return void
     */
    public function desativar(): void
    {
        $this->status = false;
    }

}