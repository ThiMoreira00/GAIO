<?php

/**
 * @file PeriodoLetivo.php
 * @description Modelo responsável pelos períodos letivos registrados no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\PeriodoLetivoStatus;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Classe PeriodoLetivo
 *
 * Modelo responsável pelos períodos letivos do sistema
 *
 * @property int $id
 * @property string $sigla
 * @property DateTime $data_inicio
 * @property DateTime $data_termino
 * @property PeriodoLetivoStatus $status
 *
 * @package App\Models
 * @extends Model
 */
class PeriodoLetivo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'periodos_letivos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'sigla',
        'data_inicio',
        'data_termino',
        'status'
    ];

    /**
     * Converte atributos para tipos nativos do PHP
     * @var array
     */
    protected $casts = [
        'data_inicio' => 'date:d/m/Y',
        'data_termino' => 'date:d/m/Y'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um período letivo pode estar associado a vários cursos
     * 
     * @return BelongsToMany
     */
    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'cursos_periodos_letivos', 'periodo_letivo_id', 'curso_id');
    }

    /**
     * Um período letivo pode estar associado a várias turmas
     * 
     * @return HasMany
     */
    public function turmas(): HasMany {
        return $this->hasMany(Turma::class, 'periodo_id', 'id');
    }

    // --- ESCOPOS ---

    /**
     * Escopo para filtrar períodos letivos por intervalo de datas
     *
     * @param DateTime $data_inicio
     * @param DateTime $data_termino
     * @return Builder
     */
    public static function intervalo(DateTime $data_inicio, DateTime $data_termino): Builder
    {
        return self::query()
            ->where(function ($query) use ($data_inicio, $data_termino) {
                $query->whereBetween('data_inicio', [$data_inicio, $data_termino])
                      ->orWhereBetween('data_termino', [$data_inicio, $data_termino]);
            });
    }

    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do período letivo
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter a sigla do período letivo
     * 
     * @return string
     */
    public function obterSigla(): string
    {
        return $this->sigla;
    }

    /**
     * Assessor (getter) para obter a data de início do período letivo
     * 
     * @return DateTime
     */
    public function obterDataInicio(): DateTime
    {
        return $this->data_inicio;
    }

    /**
     * Assessor (getter) para obter a data de término do período letivo
     * 
     * @return DateTime
     */
    public function obterDataTermino(): DateTime
    {
        return $this->data_termino;
    }

    /**
     * Assessor (getter) para obter o status do período letivo
     * 
     * @return PeriodoLetivoStatus
     */
    public function obterStatus(): PeriodoLetivoStatus
    {
        return $this->status instanceof PeriodoLetivoStatus ? $this->status : PeriodoLetivoStatus::from($this->status);
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para definir o ID do período letivo
     * 
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para definir a sigla do período letivo
     * 
     * @param string $sigla
     * @return void
    */
    public function atribuirSigla(string $sigla): void
    {
        $this->sigla = $sigla;
    }

    /**
     * Mutador (setter) para definir a data de início do período letivo
     * 
     * @param DateTime $data_inicio
     * @return void
     */
    public function atribuirDataInicio(DateTime $data_inicio): void
    {
        $this->data_inicio = $data_inicio;
    }

    /**
     * Mutador (setter) para definir a data de término do período letivo
     * 
     * @param DateTime $data_termino
     * @return void
     */
    public function atribuirDataTermino(DateTime $data_termino): void
    {
        $this->data_termino = $data_termino;
    }

    /**
     * Mutador (setter) para definir o status do período letivo
     * 
     * @param string $status
     * @return void
     */
    public function atribuirStatus(string $status): void
    {
        $this->status = PeriodoLetivoStatus::from($status);
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Função estática para obter o período letivo atual
     *
     * @return ?PeriodoLetivo
     */
    public static function obterAtual(): ?PeriodoLetivo
    {
        return self::query()->where('status', PeriodoLetivoStatus::ATIVO)?->first();
    }

    /**
     * Função estática para obter o último período letivo
     * 
     * @return ?PeriodoLetivo
     */
    public static function obterUltimo(): ?PeriodoLetivo
    {
        return self::query()
            ->orderBy('data_termino', 'desc')
            ->first();
    }

    /**
     * Função estática para contar quantos períodos letivos existem entre dois períodos
     *
     * @param int $periodoIngressoId ID do período de ingresso
     * @param int $periodoAtualId ID do período atual
     * @return int Quantidade de períodos entre os dois (inclusive)
     */
    public static function contarPeriodosEntre(int $periodoIngressoId, int $periodoAtualId): int
    {
        $periodoIngresso = self::find($periodoIngressoId);
        $periodoAtual = self::find($periodoAtualId);
        
        if (!$periodoIngresso || !$periodoAtual) {
            return 0;
        }
        
        return self::query()
            ->whereBetween('data_inicio', [
                $periodoIngresso->obterDataInicio(),
                $periodoAtual->obterDataInicio()
            ])
            ->count();
    }
}
