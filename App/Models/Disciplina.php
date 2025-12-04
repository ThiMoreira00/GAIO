<?php

/**
 * @file Disciplina.php
 * @description Modelo responsável pelas disciplinas dos componentes curriculares
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\EnsinoModalidade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * Classe Disciplina
 *
 * Modelo responsável pelas disciplinas dos componentes curriculares
 *
 * @property int $id
 * @property int $componente_curricular_id
 * @property string $sigla
 * @property string|null $ementa
 * @property string|null $bibliografia
 * @property EnsinoModalidade $modalidade_padrao
 *
 * @package App\Models
 * @extends Model
 */
class Disciplina extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'disciplinas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'componente_curricular_id',
        'sigla',
        'ementa',
        'bibliografia',
        'modalidade_padrao'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'componente_curricular_id' => 'integer',
        'sigla' => 'string',
        'ementa' => 'string',
        'bibliografia' => 'string',
        'modalidade_padrao' => EnsinoModalidade::class
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma disciplina pertence a um componente curricular
     *
     * @return BelongsTo
     */
    public function componenteCurricular(): BelongsTo
    {
        return $this->belongsTo(ComponenteCurricular::class, 'componente_curricular_id');
    }

    /**
     * Uma disciplina pode ter várias turmas
     *
     * @return HasMany
     */
    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class, 'disciplina_id');
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
     * Filtro por ID do componente curricular
     *
     * @param $query
     * @param int $componenteCurricularId
     * @return Builder
     */
    public function scopeComponenteCurricularId($query, int $componenteCurricularId): Builder
    {
        return $query->where('componente_curricular_id', $componenteCurricularId);
    }

    /**
     * Filtro por sigla
     *
     * @param $query
     * @param string $sigla
     * @return Builder
     */
    public function scopeSigla($query, string $sigla): Builder
    {
        return $query->where('sigla', $sigla);
    }

    /**
     * Filtro por modalidade padrão
     *
     * @param $query
     * @param EnsinoModalidade $modalidade
     * @return Builder
     */
    public function scopeModalidadePadrao($query, EnsinoModalidade $modalidade): Builder
    {
        return $query->where('modalidade_padrao', $modalidade);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de disciplina
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do componente curricular
     * 
     * @return int
     */
    public function obterComponenteCurricularId(): int
    {
        return $this->componente_curricular_id;
    }

    /**
     * Assessor (getter) para obter a sigla da disciplina
     * 
     * @return string
     */
    public function obterSigla(): string
    {
        return $this->sigla;
    }

    /**
     * Assessor (getter) para obter a ementa da disciplina
     * 
     * @return string|null
     */
    public function obterEmenta(): ?string
    {
        return $this->ementa;
    }

    /**
     * Assessor (getter) para obter a bibliografia da disciplina
     * 
     * @return string|null
     */
    public function obterBibliografia(): ?string
    {
        return $this->bibliografia;
    }

    /**
     * Assessor (getter) para obter a modalidade padrão da disciplina
     * 
     * @return EnsinoModalidade
     */
    public function obterModalidadePadrao(): EnsinoModalidade
    {
        return $this->modalidade_padrao;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de disciplina
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do componente curricular
     *
     * @param int $componenteCurricularId
     * @return void
     */
    public function atribuirComponenteCurricularId(int $componenteCurricularId): void
    {
        $this->componente_curricular_id = $componenteCurricularId;
    }

    /**
     * Mutador (setter) para atribuir a sigla da disciplina
     *
     * @param string $sigla
     * @return void
     */
    public function atribuirSigla(string $sigla): void
    {
        $this->sigla = $sigla;
    }

    /**
     * Mutador (setter) para atribuir a ementa da disciplina
     *
     * @param string|null $ementa
     * @return void
     */
    public function atribuirEmenta(?string $ementa): void
    {
        $this->ementa = $ementa;
    }

    /**
     * Mutador (setter) para atribuir a bibliografia da disciplina
     *
     * @param string|null $bibliografia
     * @return void
     */
    public function atribuirBibliografia(?string $bibliografia): void
    {
        $this->bibliografia = $bibliografia;
    }

    /**
     * Mutador (setter) para atribuir a modalidade padrão da disciplina
     *
     * @param EnsinoModalidade $modalidadePadrao
     * @return void
     */
    public function atribuirModalidadePadrao(EnsinoModalidade $modalidadePadrao): void
    {
        $this->modalidade_padrao = $modalidadePadrao;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se a disciplina é presencial
     * 
     * @return bool
     */
    public function verificarPresencial(): bool
    {
        return $this->modalidade_padrao === EnsinoModalidade::PRESENCIAL;
    }

    /**
     * Função para verificar se a disciplina é remota
     * 
     * @return bool
     */
    public function verificarRemota(): bool
    {
        return $this->modalidade_padrao === EnsinoModalidade::REMOTA;
    }

    /**
     * Função para verificar se a disciplina é híbrida
     * 
     * @return bool
     */
    public function verificarHibrida(): bool
    {
        return $this->modalidade_padrao === EnsinoModalidade::HIBRIDA;
    }

    /**
     * Função estática para buscar uma disciplina pela sigla
     * 
     * @param string $sigla
     * @return Disciplina|null
     */
    public static function buscarPorSigla(string $sigla): ?Disciplina
    {
        return self::where('sigla', $sigla)->first();
    }

    /**
     * Função estática para buscar disciplinas por modalidade
     * 
     * @param EnsinoModalidade $modalidade
     * @return Collection
     */
    public static function buscarPorModalidade(EnsinoModalidade $modalidade)
    {
        return self::where('modalidade_padrao', $modalidade)->get();
    }

}