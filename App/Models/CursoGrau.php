<?php

/**
 * @file CursoGrau.php
 * @description Modelo responsável pelos graus (tipos) de cursos registráveis no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Classe CursoGrau
 *
 * Responsável pelos graus de cursos registráveis no sistema
 *
 * @package App\Models
 * @extends Model
 */
class CursoGrau extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'cursos_graus';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome',
        'titulo',
        'nivel'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um grau pertence a zero ou muitos cursos
     * TODO: Relacionamento?
     *
     * @return BelongsToMany
     */
    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'cursos', 'curso_id', 'grau_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do grau
     *
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do grau
     *
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter o título do grau
     *
     * @return string
     */
    public function obterTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * Assessor (getter) para obter o nível do grau
     * 
     * @return string
     */
    public function obterNivel(): string
    {
        return $this->nivel;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do grau
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome do grau
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir o título do grau
     *
     * @param string $titulo
     * @return void
     */
    public function atribuirTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    /**
     * Mutador (setter) para atribuir o nível do grau
     * 
     * @param string $nivel
     * @return void
     */
    public function atribuirNivel(string $nivel): void
    {
        $this->nivel = $nivel;
    }


    // --- MÉTODOS DE BUSCA ---

    /**
     * Busca um grau de curso pelo nome
     *
     * @param string $nome
     * @return CursoGrau|null
     */
    public static function buscarPorNome(string $nome): ?CursoGrau
    {
        return self::where('nome', $nome)->first();
    }

}