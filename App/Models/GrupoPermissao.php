<?php

/**
 * @file GrupoPermissao.php
 * @description Pivot responsável pelas permissões dos grupos registrados no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Pivot;

/**
 * Classe GrupoPermissao
 *
 * Pivot responsável pelas permissões dos grupos registrados no sistema
 *
 * @property int $id
 * @property int $grupo_id
 * @property int $permissao_id
 *
 * @package App\Models
 * @extends Pivot
 */

class GrupoPermissao extends Pivot
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao pivot
     * @var string
     */
    protected $table = 'grupos_permissoes';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'grupo_id',
        'permissao_id'
    ];


    // --- RELACIONAMENTOS ---

    /**
     *
     * TODO: Relacionamento?
     *
     * @return BelongsTo
     */
    public function permissoes(): BelongsTo
    {
        return $this->belongsTo(Permissao::class);
    }

    /**
     *
     * TODO: Relacionamento?
     *
     * @return BelongsTo
     */
    public function grupos(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do grupo
     *
     * @return int
     */
    public function obterGrupoId(): int {
        return $this->grupo_id;
    }

    /**
     * Assessor (getter) para obter o ID da permissão
     *
     * @return int
     */
    public function obterPermissaoId(): int {
        return $this->permissao_id;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do grupo
     *
     * @param int $grupoId
     * @return void
     */
    public function atribuirGrupoId(int $grupoId): void {
        $this->grupo_id = $grupoId;
    }

    /**
     * Mutador (setter) para atribuir o ID da permissão
     *
     * @param int $permissaoId
     * @return void
     */
    public function atribuirPermissaoId(int $permissaoId): void {
        $this->permissao_id = $permissaoId;
    }
}