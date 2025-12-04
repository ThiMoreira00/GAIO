<?php

/**
 * @file Permissao.php
 * @description Modelo responsável pelas permissões registradas no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Classe Permissao
 *
 * Modelo responsável pelas permissões registradas no sistema
 *
 * @property int $id
 * @property string $codigo
 * @property string $categoria
 * @property string $nome
 * @property ?string $descricao
 * @property boolean $padrao
 */
class Permissao extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'permissoes';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'codigo',
        'categoria',
        'nome',
        'descricao',
        'padrao'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'padrao' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Os grupos que possuem esta permissão
     * TODO: Relacionamento?
     *
     * @return BelongsToMany
     */
    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class)->using(GrupoPermissao::class);
    }

    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da permissão
     *
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o código da permissão
     *
     * @return string
     */
    public function obterCodigo(): string
    {
        return $this->codigo;
    }

    /**
     * Assessor (getter) para obter a categoria da permissão
     * 
     * @return string
     */
    public function obterCategoria(): string
    {
        return $this->categoria;
    }

    /**
     * Assessor (getter) para obter o nome da permissão
     *
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter a descrição da permissão
     *
     * @return string
     */
    public function obterDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * Assessor (getter) para obter o valor padrão da permissão
     *
     * @return bool
     */
    public function obterPadrao(): bool
    {
        return $this->padrao;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID da permissão
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o código da permissão
     *
     * @param string $codigo
     * @return void
     */
    public function atribuirCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * Mutador (setter) para atribuir a categoria da permissão
     *
     * @param string $categoria
     * @return void
     */
    public function atribuirCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    /**
     * Mutador (setter) para atribuir o nome da permissão
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir a descrição da permissão
     *
     * @param string $descricao
     * @return void
     */
    public function atribuirDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * Mutador (setter) para atribuir o valor padrão da permissão
     *
     * @param bool $padrao
     * @return void
     */
    public function atribuirPadrao(bool $padrao): void
    {
        $this->padrao = $padrao;
    }
}
