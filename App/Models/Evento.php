<?php

/**
 * @file Evento.php
 * @description Modelo responsável pelos eventos registrados no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe Evento
 *
 * Modelo responsável pelos eventos do sistema
 *
 * @property int $id
 * @property int $tipo_id
 * @property string $nome
 * @property string $descricao
 * @property DateTime $data_inicio
 * @property DateTime $data_termino
 *
 * @package App\Models
 * @extends Model
 */
class Evento extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'eventos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'tipo_id',
        'nome',
        'descricao',
        'data_inicio',
        'data_termino'
    ];

    /**
     * Converte atributos para tipos nativos do PHP
     * @var array
     */
    protected $casts = [
        'data_inicio' => 'datetime',
        'data_termino' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Relacionamento: Um evento pertence a um tipo de evento
     * 
     * @return BelongsTo
     */
    public function tipo(): BelongsTo
    {
        return $this->belongsTo(EventoTipo::class, 'tipo_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do evento
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do evento
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter a descrição do evento
     * 
     * @return string
     */
    public function obterDescricao(): string
    {
        return $this->descricao;
    }

    /**
     * Assessor (getter) para obter a data de início do evento
     * 
     * @return DateTime
     */
    public function obterDataInicio(): DateTime
    {
        return $this->data_inicio;
    }

    /**
     * Assessor (getter) para obter a data de término do evento
     * 
     * @return DateTime
     */
    public function obterDataTermino(): DateTime
    {
        return $this->data_termino;
    }

    /**
     * Assessor (getter) para obter o tipo do evento
     * 
     * @return EventoTipo
     */
    public function obterTipo(): EventoTipo
    {
        return $this->tipo;
    }

    /**
     * Assessor (getter) para obter o ID do tipo do evento
     * 
     * @return int
     */
    public function obterTipoId(): int
    {
        return $this->tipo_id;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do evento
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do tipo do evento
     *
     * @param int $tipoId
     * @return void
     */
    public function atribuirTipoId(int $tipoId): void
    {
        $this->tipo_id = $tipoId;
    }

    /**
     * Mutador (setter) para atribuir o nome do evento
     * 
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir a descrição do evento
     * 
     * @param ?string $descricao
     * @return void
     */
    public function atribuirDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * Mutador (setter) para atribuir a data de início do evento
     * 
     * @param DateTime $data_inicio
     * @return void
     */
    public function atribuirDataInicio(DateTime $data_inicio): void
    {
        $this->data_inicio = $data_inicio;
    }

    /**
     * Mutador (setter) para atribuir a data de término do evento
     * 
     * @param ?DateTime $data_termino
     * @return void
     */
    public function atribuirDataTermino(?DateTime $data_termino): void
    {
        $this->data_termino = $data_termino;
    }
}
