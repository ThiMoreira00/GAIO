<?php

/**
 * @file Curso.php
 * @description Modelo responsável pelos cursos registrados no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\CursoStatus;
use App\Models\Enumerations\MatrizCurricularStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe Curso
 *
 * Modelo responsável pelos cursos do sistema
 *
 * @property int $id
 * @property string $emec_codigo
 * @property int $grau_id
 * @property string $nome
 * @property string $sigla
 * @property int $duracao_minima
 * @property int $duracao_maxima
 * @property string $parecer_reconhecimento
 * @property string $status
 *
 * @package App\Models
 * @extends Model
 */
class Curso extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'cursos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'emec_codigo',
        'grau_id',
        'nome',
        'sigla',
        'duracao_minima',
        'duracao_maxima',
        'parecer_reconhecimento',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'duracao_minima' => 'integer',
        'duracao_maxima' => 'integer'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um curso pertence a um grau (CursoGrau)
     *
     * @return BelongsTo
     */
    public function grau(): BelongsTo
    {
        return $this->belongsTo(CursoGrau::class, 'grau_id');
    }

    /**
     * Um curso possui uma ou mais matrizes curriculares (MatrizCurricular)
     *
     * @return HasMany
     */
    public function matrizes()
    {
        return $this->hasMany(MatrizCurricular::class, 'curso_id');
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
     * Filtro por código E-MEC
     *
     * @param $query
     * @param string $codigo
     * @return Builder
     */
    public function scopeEmecCodigo($query, string $codigo): Builder
    {
        return $query->where('emec_codigo', $codigo);
    }

    /**
     * Filtro por status (ativo, inativo, arquivado)
     *
     * @param $query
     * @param string $status
     * @return Builder
     */
    public function scopeStatus($query, string $status): Builder
    {
        return $query->where('status', $status);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do curso
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o código E-MEC do curso
     * 
     * @return ?string
     */
    public function obterEmecCodigo(): ?string
    {
        return $this->emec_codigo;
    }

    /**
     * Assessor (getter) para obter o ID do grau do curso
     *
     * @return int
     */
    public function obterGrauId(): int
    {
        return $this->grau_id;
    }

    /**
     * Assessor (getter) para obter o grau do curso
     * 
     * @return string
     */
    public function obterGrau(): string
    {
        return $this->grau;
    }

    /**
     * Assessor (getter) para obter o nome do curso
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter o nome simplificado do curso (sem caracteres especiais, espaços - substituído por hífen -)
     * 
     * @return string
     */
    public function obterNomeSimplificado(): string
    {
        return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9]+/', '-', strtolower(transliterator_transliterate('Any-Latin; Latin-ASCII', $this->nome)))), '-');
    }

    /**
     * Assessor (getter) para obter a sigla do curso
     * 
     * @return ?string
     */
    public function obterSigla(): ?string
    {
        return $this->sigla;
    }

    /**
     * Assessor (getter) para obter a duração mínima de semestres do curso
     *
     * @return int
     */
    public function obterDuracaoMinima(): int
    {
        return (int) $this->duracao_minima;
    }

    /**
     * Assessor (getter) para obter a duração máxima de semestres do curso
     *
     * @return int
     */
    public function obterDuracaoMaxima(): int
    {
        return (int) $this->duracao_maxima;
    }

    /**
     * Assessor (getter) para obter o parecer de reconhecimento do curso
     *
     * @return string
     */
    public function obterParecerReconhecimento(): string
    {
        return $this->parecer_reconhecimento;
    }

    /**
     * Assessor (getter) para obter o status atual do curso
     *
     * @return CursoStatus
     */
    public function obterStatus(): CursoStatus
    {
        return CursoStatus::fromName($this->status);
    }

    /**
     * Assessor (getter) para obter a matriz vigente do curso
     *
     * @return ?MatrizCurricular
     */
    public function obterMatrizVigente(): ?MatrizCurricular {
        return $this->matrizes()?->where('status', MatrizCurricularStatus::VIGENTE)?->first();
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do curso
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o código E-MEC do curso
     *
     * @param string $codigo
     * @return void
     */
    public function atribuirEmecCodigo(string $codigo): void
    {
        $this->emec_codigo = $codigo;
    }

    /**
     * Mutador (setter) para atribuir o grau do curso
     * 
     * @param CursoGrau $grau
     * @return void
     */
    public function atribuirGrau(CursoGrau $grau): void
    {
        $this->grau()->associate($grau);
    }

    /**
     * Mutador (setter) para atribuir o ID do grau do curso
     *
     * @param int $id
     * @return void
     */
    public function atribuirGrauId(int $grauId): void
    {
        $this->grau_id = $grauId;
    }

    /**
     * Mutador (setter) para atribuir o nome do curso
     * 
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir a sigla do curso
     * 
     * @param ?string $sigla
     * @return void
     */
    public function atribuirSigla(?string $sigla): void
    {
        $this->sigla = $sigla;
    }

    /**
     * Mutador (setter) para atribuir a duração mínima de semestres do curso
     * 
     * @param int $duracao_minima
     * @return void
     */
    public function atribuirDuracaoMinima(int $duracao_minima): void
    {
        $this->duracao_minima = $duracao_minima;
    }

    /**
     * Mutador (setter) para atribuir a duração máxima de semestres do curso
     * 
     * @param int $duracao_maxima
     * @return void
     */
    public function atribuirDuracaoMaxima(int $duracao_maxima): void
    {
        $this->duracao_maxima = $duracao_maxima;
    }

    /**
     * Mutador (setter) para atribuir o parecer de reconhecimento do curso
     *
     * @param string $parecer_reconhecimento
     * @return void
     */
    public function atribuirParecerReconhecimento(string $parecer_reconhecimento): void
    {
        $this->parecer_reconhecimento = $parecer_reconhecimento;
    }

    /**
     * Mutador (setter) para atribuir o status do curso
     *
     * @param CursoStatus $status
     * @return void
     */
    public function atribuirStatus(CursoStatus $status): void
    {
        $this->status = $status->value;
    }


    // --- MÉTODOS DE BUSCA ---

    /**
     * Busca um curso pelo código E-MEC
     *
     * @param string $codigo
     * @return ?Curso
     */
    public static function buscarPorEmecCodigo(string $codigo): ?Curso
    {
        return self::emecCodigo($codigo)->first() ?? null;
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Arquiva o curso (altera o status para "arquivado")
     *
     * @return void
     */
    public function arquivar(): void
    {
        $this->status = CursoStatus::ARQUIVADO->value;
    }
}   