<?php

/**
 * @file Atividade.php
 * @description Modelo responsável pelas atividades avaliativas no sistema
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
 * Classe Atividade
 *
 * Modelo responsável pelas atividades avaliativas no sistema
 *
 * @property int $id
 * @property int $avaliacao_turma_id
 * @property string $codigo
 * @property string $titulo
 * @property string|null $descricao
 * @property float $peso
 * @property float $nota_maxima
 *
 * @package App\Models
 * @extends Model
 */
class Atividade extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'atividades';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'avaliacao_turma_id',
        'codigo',
        'titulo',
        'descricao',
        'peso',
        'nota_maxima'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'avaliacao_turma_id' => 'int',
        'codigo' => 'string',
        'titulo' => 'string',
        'descricao' => 'string',
        'peso' => 'float',
        'nota_maxima' => 'float'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma atividade está associada a uma avaliação de turma
     *
     * @return BelongsTo
     */
    public function turmaAvaliacao(): BelongsTo
    {
        return $this->belongsTo(TurmaAvaliacao::class, 'turma_avaliacao_id');
    }

    /**
     * Uma atividade possui muitas notas de atividade
     *
     * @return HasMany
     */
    public function atividadeNotas(): HasMany
    {
        return $this->hasMany(AtividadeNota::class, 'atividade_id');
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
     * Filtro por avaliacao_turma_id
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
     * Filtro por codigo
     *
     * @param $query
     * @param string $codigo
     * @return Builder
     */
    public function scopeCodigo($query, string $codigo): Builder
    {
        return $query->where('codigo', $codigo);
    }

    /**
     * Filtro por titulo
     *
     * @param $query
     * @param string $titulo
     * @return Builder
     */
    public function scopeTitulo($query, string $titulo): Builder
    {
        return $query->where('titulo', 'LIKE', "%{$titulo}%");
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da atividade
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da avaliação de turma associada
     * 
     * @return int
     */
    public function obterAvaliacaoTurmaId(): int
    {
        return $this->avaliacao_turma_id;
    }

    /**
     * Assessor (getter) para obter o código da atividade
     * 
     * @return string
     */
    public function obterCodigo(): string
    {
        return $this->codigo;
    }

    /**
     * Assessor (getter) para obter o título da atividade
     * 
     * @return string
     */
    public function obterTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * Assessor (getter) para obter a descrição da atividade
     * 
     * @return string|null
     */
    public function obterDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Assessor (getter) para obter o peso da atividade
     * 
     * @return float
     */
    public function obterPeso(): float
    {
        return $this->peso;
    }

    /**
     * Assessor (getter) para obter a nota máxima da atividade
     * 
     * @return float
     */
    public function obterNotaMaxima(): float
    {
        return $this->nota_maxima;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para definir o ID da avaliação de turma associada
     * 
     * @param int $avaliacaoTurmaId
     * @return void
     */
    public function atribuirAvaliacaoTurmaId(int $avaliacaoTurmaId): void
    {
        $this->avaliacao_turma_id = $avaliacaoTurmaId;
    }

    /**
     * Mutador (setter) para definir o código da atividade
     * 
     * @param string $codigo
     * @return void
     */
    public function atribuirCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * Mutador (setter) para definir o título da atividade
     * 
     * @param string $titulo
     * @return void
     */
    public function atribuirTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    /**
     * Mutador (setter) para definir a descrição da atividade
     * 
     * @param string|null $descricao
     * @return void
     */
    public function atribuirDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * Mutador (setter) para definir o peso da atividade
     * 
     * @param float $peso
     * @return void
     */
    public function atribuirPeso(float $peso): void
    {
        $this->peso = $peso;
    }

    /**
     * Mutador (setter) para definir a nota máxima da atividade
     * 
     * @param float $notaMaxima
     * @return void
     */
    public function atribuirNotaMaxima(float $notaMaxima): void
    {
        $this->nota_maxima = $notaMaxima;
    }

}