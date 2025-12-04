<?php

/**
 * @file AlunoResponsavel.php
 * @description Modelo responsável pelos responsáveis/filiação dos alunos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\AlunoResponsavelTipo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe AlunoResponsavel
 *
 * Modelo responsável pelos responsáveis/filiação dos alunos
 *
 * @property int $id
 * @property int $aluno_id
 * @property string $nome
 * @property AlunoResponsavelTipo $tipo
 *
 * @package App\Models
 * @extends Model
 */
class AlunoResponsavel extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos_responsaveis';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'aluno_id',
        'nome',
        'tipo'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'aluno_id' => 'integer',
        'tipo' => AlunoResponsavelTipo::class
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um responsável pertence a um aluno (Aluno)
     *
     * @return BelongsTo
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
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
     * Assessor (getter) para obter o ID do responsável
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do aluno
     * 
     * @return int
     */
    public function obterAlunoId(): int
    {
        return $this->aluno_id;
    }

    /**
     * Assessor (getter) para obter o nome do responsável
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter o tipo do responsável
     * 
     * @return AlunoResponsavelTipo
     */
    public function obterTipo(): AlunoResponsavelTipo
    {
        return $this->tipo;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do responsável
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do aluno
     *
     * @param int $alunoId
     * @return void
     */
    public function atribuirAlunoId(int $alunoId): void
    {
        $this->aluno_id = $alunoId;
    }

    /**
     * Mutador (setter) para atribuir o nome do responsável
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir o tipo do responsável
     *
     * @param AlunoResponsavelTipo $tipo
     * @return void
     */
    public function atribuirTipo(AlunoResponsavelTipo $tipo): void
    {
        $this->tipo = $tipo;
    }

}
