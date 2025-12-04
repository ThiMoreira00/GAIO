<?php

/**
 * @file AlunoIngressoTipo.php
 * @description Modelo responsável pelos tipos de ingressos dos alunos
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
 * Classe AlunoIngressoTipo
 *
 * Modelo responsável pelos tipos de ingressos dos alunos
 *
 * @property int $id
 * @property string $nome
 * @property boolean $status
 *
 * @package App\Models
 * @extends Model
 */
class AlunoIngressoTipo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos_ingressos_tipos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'nome' => 'string',
        'status' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um tipo de ingresso pode estar associado a vários ingressos de alunos
     *
     * @return HasMany
     */
    public function alunoMatriculas(): HasMany
    {
        return $this->hasMany(AlunoMatricula::class, 'ingresso_tipo_id');
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
     * Assessor (getter) para obter o ID do tipo de ingresso
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do tipo de ingresso
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter o status do tipo de ingresso
     * 
     * @return bool
     */
    public function obterStatus(): bool
    {
        return $this->status;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do tipo de ingresso
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome do tipo de ingresso
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir o status do tipo de ingresso
     * 
     * @param bool $status
     * @return void
     */
    public function atribuirStatus(bool $status): void
    {
        $this->status = $status;
    }

}