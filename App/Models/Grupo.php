<?php

/**
 * @file Grupo.php
 * @description Modelo responsável pelos grupos no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Classe Grupo
 *
 * Modelo responsável pelos grupos no sistema
 *
 * @property int $id
 * @property string $nome
 * @property ?string $descricao
 * @property boolean $padrao
 *
 * @package App\Models
 * @extends Model
 */
class Grupo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'grupos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome',
        'descricao'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Os usuários que possuem este grupo.
     * TODO: Verificar relacionamento = Relacionamento Muitos-para-Muitos (M-M)
     *
     * @return BelongsToMany
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_grupos', 'grupo_id', 'usuario_id');
    }

    /**
     * As permissões que este grupo concede.
     * TODO: Verificar relacionamento = Relacionamento Muitos-para-Muitos
     *
     * @return BelongsToMany
     */
    public function permissoes(): BelongsToMany
    {
        return $this->belongsToMany(Permissao::class, 'grupos_permissoes','grupo_id','permissao_id')
            ->using(GrupoPermissao::class);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do grupo
     *
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do grupo
     *
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter a descrição do grupo
     *
     * @return ?string
     */
    public function obterDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Assessor (getter) para obter se o grupo é padrão
     *
     * @return bool
     */
    public function obterPadrao(): bool
    {
        return $this->padrao;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do grupo
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome do grupo
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir a descrição do grupo
     *
     * @param ?string $descricao
     * @return void
     */
    public function atribuirDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * Mutador (setter) para atribuir se o grupo é padrão
     *
     * @param bool $padrao
     * @return void
     */
    public function atribuirPadrao(bool $padrao): void
    {
        $this->padrao = $padrao;
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Função para buscar um grupo pelo nome
     *
     * @param string $nome
     * @return ?Grupo
     */
    public static function buscarPorNome(string $nome): ?Grupo
    {
        return self::where('nome', $nome)->first();
    }
}