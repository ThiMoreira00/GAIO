<?php

/**
 * @file GradeHoraria.php
 * @description Modelo responsável pelas grades horárias do sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe GradeHoraria
 *
 * Modelo responsável pelas grades horárias do sistema
 *
 * @property int $id
 * @property string $nome
 *
 * @package App\Models
 * @extends Model
 */
class GradeHoraria extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'grades_horarias';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'nome' => 'string'
    ];


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
     * Filtro por nome (busca parcial)
     *
     * @param $query
     * @param string $nome
     * @return Builder
     */
    public function scopeNome($query, string $nome): Builder
    {
        return $query->where('nome', 'like', '%' . $nome . '%');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de grade horária
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome da grade horária
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de grade horária
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome da grade horária
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função estática para buscar uma grade horária pelo nome
     * 
     * @param string $nome
     * @return GradeHoraria|null
     */
    public static function buscarPorNome(string $nome): ?GradeHoraria
    {
        return self::where('nome', $nome)->first();
    }

    /**
     * Função estática para buscar todas as grades horárias
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function buscarTodas()
    {
        return self::all();
    }

}