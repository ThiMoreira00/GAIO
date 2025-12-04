<?php

/**
 * @file EventoTipo.php
 * @description Modelo responsável pelos tipos de eventos registrados no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe EventoTipo
 * 
 * Modelo responsável pelos tipos de eventos do sistema
 * 
 * @property int $id
 * @property string $nome
 * @property string $descricao
 * @property boolean $padrao
 * 
 * @package App\Models
 * @extends Model
 */
class EventoTipo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'eventos_tipos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome',
        'descricao',
        'padrao'
    ];

    /**
     * Converte atributos para tipos nativos do PHP
     * @var array
     */
    protected $casts = [
        'padrao' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Relacionamento: Um tipo de evento pode ter muitos eventos
     * TODO: Relacionamento?
     * 
     * @return HasMany
     */
    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'tipo_id');
    }



    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do tipo de evento
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do tipo de evento
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter a descrição do tipo de evento
     * 
     * @return string
     */
    public function obterDescricao(): string
    {
        return $this->descricao;
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Assessor (getter) para saber se o tipo de evento é padrão
     * 
     * @return bool
     */
    public function verificarPadrao(): bool
    {
        return $this->padrao;
    }
    
}
