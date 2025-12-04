<?php

/**
 * @file ProfessorMatricula.php
 * @description Modelo responsável pelas matrículas de professores
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

/**
 * Classe ProfessorMatricula
 *
 * Modelo responsável pelas matrículas de professores
 *
 * @property int $id
 * @property int $professor_id
 * @property string $matricula
 * @property int $carga_horaria
 * @property DateTime $data_inicio
 * @property DateTime $data_termino
 * @property bool $status
 *
 * @package App\Models
 * @extends Model
 */
class ProfessorMatricula extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'professores_matriculas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'professor_id',
        'matricula',
        'carga_horaria',
        'data_inicio',
        'data_termino',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'professor_id' => 'integer',
        'matricula' => 'string',
        'carga_horaria' => 'integer',
        'data_inicio' => 'datetime',
        'data_termino' => 'datetime',
        'status' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Uma matrícula de professor pertence a um professor
     *
     * @return BelongsTo
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'professor_id');
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
     * Filtro por ID do professor
     *
     * @param $query
     * @param int $professorId
     * @return Builder
     */
    public function scopeProfessorId($query, int $professorId): Builder
    {
        return $query->where('professor_id', $professorId);
    }

    /**
     * Filtro por matrícula (parcial)
     *
     * @param $query
     * @param string $matricula
     * @return Builder
     */
    public function scopeMatricula($query, string $matricula): Builder
    {
        return $query->where('matricula', 'like', '%' . $matricula . '%');
    }

    /**
     * Filtro por status
     *
     * @param $query
     * @param bool $status
     * @return Builder
     */
    public function scopeStatus($query, bool $status): Builder
    {
        return $query->where('status', $status);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de professor matrícula
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do professor
     * 
     * @return int
     */
    public function obterProfessorId(): int
    {
        return $this->professor_id;
    }

    /**
     * Assessor (getter) para obter a matrícula
     * 
     * @return string
     */
    public function obterMatricula(): string
    {
        return $this->matricula;
    }

    /**
     * Assessor (getter) para obter a carga horária
     * 
     * @return int
     */
    public function obterCargaHoraria(): int
    {
        return $this->carga_horaria;
    }

    /**
     * Assessor (getter) para obter a data de início
     * 
     * @return DateTime
     */
    public function obterDataInicio(): DateTime
    {
        return $this->data_inicio;
    }

    /**
     * Assessor (getter) para obter a data de término
     * 
     * @return DateTime
     */
    public function obterDataTermino(): DateTime
    {
        return $this->data_termino;
    }

    /**
     * Assessor (getter) para obter o status
     * 
     * @return bool
     */
    public function obterStatus(): bool
    {
        return $this->status;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de professor matrícula
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do professor
     *
     * @param int $professorId
     * @return void
     */
    public function atribuirProfessorId(int $professorId): void
    {
        $this->professor_id = $professorId;
    }

    /**
     * Mutador (setter) para atribuir a matrícula
     *
     * @param string $matricula
     * @return void
     */
    public function atribuirMatricula(string $matricula): void
    {
        $this->matricula = $matricula;
    }

    /**
     * Mutador (setter) para atribuir a carga horária
     *
     * @param int $cargaHoraria
     * @return void
     */
    public function atribuirCargaHoraria(int $cargaHoraria): void
    {
        $this->carga_horaria = $cargaHoraria;
    }

    /**
     * Mutador (setter) para atribuir a data de início
     *
     * @param DateTime $dataInicio
     * @return void
     */
    public function atribuirDataInicio(DateTime $dataInicio): void
    {
        $this->data_inicio = $dataInicio;
    }

    /**
     * Mutador (setter) para atribuir a data de término
     *
     * @param DateTime $dataTermino
     * @return void
     */
    public function atribuirDataTermino(DateTime $dataTermino): void
    {
        $this->data_termino = $dataTermino;
    }

    /**
     * Mutador (setter) para atribuir o status
     *
     * @param bool $status
     * @return void
     */
    public function atribuirStatus(bool $status): void
    {
        $this->status = $status;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se a matrícula está ativa
     * 
     * @return bool
     */
    public function verificarAtiva(): bool
    {
        return $this->status === true;
    }

    /**
     * Função para ativar a matrícula
     * 
     * @return void
     */
    public function ativar(): void
    {
        $this->status = true;
        $this->save();
    }

    /**
     * Função para desativar a matrícula
     * 
     * @return void
     */
    public function desativar(): void
    {
        $this->status = false;
        $this->save();
    }

    /**
     * Função para calcular o período em dias
     * 
     * @return int
     */
    public function calcularPeriodoDias(): int
    {
        $inicio = $this->data_inicio;
        $termino = $this->data_termino;
        
        return ($termino->getTimestamp() - $inicio->getTimestamp()) / (60 * 60 * 24);
    }

    /**
     * Função para verificar se a matrícula está vigente
     * 
     * @return bool
     */
    public function verificarVigente(): bool
    {
        $hoje = new DateTime();
        return $this->data_inicio <= $hoje && $this->data_termino >= $hoje && $this->status;
    }

    /**
     * Função estática para buscar por professor
     * 
     * @param int $professorId
     * @return Collection
     */
    public static function buscarPorProfessor(int $professorId)
    {
        return self::where('professor_id', $professorId)->get();
    }

    /**
     * Função estática para buscar matrículas ativas
     * 
     * @return Collection
     */
    public static function buscarAtivas()
    {
        return self::where('status', true)->get();
    }

    /**
     * Função estática para buscar por número de matrícula
     * 
     * @param string $matricula
     * @return self|null
     */
    public static function buscarPorNumeroMatricula(string $matricula): ?self
    {
        return self::where('matricula', $matricula)->first();
    }

}
