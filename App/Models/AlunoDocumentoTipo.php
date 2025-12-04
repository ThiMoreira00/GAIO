<?php

/**
 * @file AlunoDocumentoTipo.php
 * @description Modelo responsável pelos tipos de documentos dos alunos
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
 * Classe AlunoDocumentoTipo
 *
 * Modelo responsável pelos tipos de documentos dos alunos
 *
 * @property int $id
 * @property string $nome
 * @property boolean $obrigatorio
 *
 * @package App\Models
 * @extends Model
 */
class AlunoDocumentoTipo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos_documentos_tipos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome',
        'obrigatorio'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'nome' => 'string',
        'obrigatorio' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um tipo de documento possui muitos documentos de alunos (AlunoDocumento)
     *
     * @return BelongsTo
     */
    public function alunoDocumentos(): HasMany
    {
        return $this->hasMany(AlunoDocumento::class, 'documento_tipo_id');
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


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do tipo de documento
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do tipo de documento
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter se o documento é obrigatório
     * 
     * @return bool
     */
    public function obterObrigatorio(): bool
    {
        return $this->obrigatorio;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do tipo de documento
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome do tipo de documento
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir se o documento é obrigatório
     * 
     * @param bool $obrigatorio
     * @return void
     */
    public function atribuirObrigatorio(bool $obrigatorio): void
    {
        $this->obrigatorio = $obrigatorio;
    }

}