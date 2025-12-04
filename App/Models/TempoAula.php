<?php

/**
 * @file TempoAula.php
 * @description Modelo responsável pelos tempos de aula no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\DiaSemana;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Enumerations\Turno;

/**
 * Classe TempoAula
 *
 * Modelo responsável pelos tempos de aula no sistema
 *
 * @property int $id
 * @property string $dia_semana
 * @property DateTime $hora_inicio
 * @property DateTime $hora_termino
 * @property string $turno
 *
 * @package App\Models
 * @extends Model
 */
class TempoAula extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'tempos_aulas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'dia_semana',
        'hora_inicio',
        'hora_termino',
        'turno'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'dia_semana' => 'string',
        'hora_inicio' => 'datetime',
        'hora_termino' => 'datetime',
        'turno' => 'string'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um tempo de aula pode ter vários horários de turma associados
     *
     * @return HasMany
     */
    public function turmaHorarios(): HasMany
    {
        return $this->hasMany(TurmaHorario::class, 'tempo_aula_id');
    }

    /**
     * Um tempo de aula pode ter várias frequências de turma associadas
     *
     * @return HasMany
     */
    public function turmaFrequencias(): HasMany
    {
        return $this->hasMany(TurmaFrequencia::class, 'tempo_aula_id');
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

    /**
     * Filtro por dia da semana
     *
     * @param $query
     * @param DiaSemana $diaSemana
     * @return Builder
     */
    public function scopeDiaSemana($query, DiaSemana $diaSemana): Builder
    {
        return $query->where('dia_semana', $diaSemana);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do tempo de aula
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o dia da semana
     * 
     * @return DiaSemana
     */
    public function obterDiaSemana(): DiaSemana
    {
        return DiaSemana::fromName($this->dia_semana);
    }

    /**
     * Assessor (getter) para obter a hora de início
     * 
     * @return DateTime
     */
    public function obterHoraInicio(): DateTime
    {
        return $this->hora_inicio;
    }

    /**
     * Assessor (getter) para obter a hora de término
     * 
     * @return DateTime
     */
    public function obterHoraTermino(): DateTime
    {
        return $this->hora_termino;
    }

    /**
     * Assessor (getter) para obter o turno 
     * 
     * @return Turno
     */
    public function obterTurno(): Turno
    {
        return Turno::fromName($this->turno);
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do tempo de aula
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o dia da semana
     *
     * @param DiaSemana $diaSemana
     * @return void
     */
    public function atribuirDiaSemana(DiaSemana $diaSemana): void
    {
        $this->dia_semana = $diaSemana->name;
    }

    /**
     * Mutador (setter) para atribuir a hora de início
     *
     * @param DateTime $horaInicio
     * @return void
     */
    public function atribuirHoraInicio(DateTime $horaInicio): void
    {
        $this->hora_inicio = $horaInicio;
    }

    /**
     * Mutador (setter) para atribuir a hora de término
     *
     * @param DateTime $horaTermino
     * @return void
     */
    public function atribuirHoraTermino(DateTime $horaTermino): void
    {
        $this->hora_termino = $horaTermino;
    }

    /**
     * Mutador (setter) para atribuir o turno
     *
     * @param Turno $turno
     * @return void
     */
    public function atribuirTurno(Turno $turno): void
    {
        $this->turno = $turno->name;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para calcular a duração da aula em minutos
     * 
     * @return int
     */
    public function calcularDuracaoMinutos(): int
    {
        $inicio = $this->hora_inicio;
        $termino = $this->hora_termino;
        
        return ($termino->getTimestamp() - $inicio->getTimestamp()) / 60;
    }

    /**
     * Função para verificar se o tempo de aula é válido (término após início)
     * 
     * @return bool
     */
    public function verificarTempoValido(): bool
    {
        return $this->hora_termino > $this->hora_inicio;
    }

    /**
     * Função estática para buscar tempos de aula por dia da semana
     * 
     * @param DiaSemana $diaSemana
     * @return Collection
     */
    public static function buscarPorDiaSemana(DiaSemana $diaSemana)
    {
        return self::where('dia_semana', $diaSemana)
            ->orderBy('hora_inicio')
            ->get();
    }

}