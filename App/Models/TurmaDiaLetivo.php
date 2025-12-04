<?php

/**
 * @file TurmaDiaLetivo.php
 * @description Modelo responsável pelos dias letivos das turmas
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe TurmaDiaLetivo
 *
 * Modelo responsável pelos dias letivos das turmas
 *
 * @property int $id
 * @property int $turma_id
 * @property int $usuario_responsavel_id
 * @property string $conteudo
 * @property string|null $anotacao_particular
 * @property bool $status
 * @property DateTime $data_letivo
 * @property DateTime $data_registro
 * @property DateTime $data_liberacao
 *
 * @package App\Models
 * @extends Model
 */
class TurmaDiaLetivo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'turmas_dias_letivos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'turma_id',
        'usuario_responsavel_id',
        'conteudo',
        'anotacao_particular',
        'status',
        'data_letivo',
        'data_registro',
        'data_liberacao'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'turma_id' => 'int',
        'usuario_responsavel_id' => 'int',
        'conteudo' => 'string',
        'anotacao_particular' => 'string',
        'status' => 'boolean',
        'data_letivo' => 'date',
        'data_registro' => 'datetime',
        'data_liberacao' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um dia letivo pertence a uma turma
     *
     * @return BelongsTo
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'turma_id');
    }

    /**
     * Um dia letivo pertence a um usuário responsável
     *
     * @return BelongsTo
     */
    public function usuarioResponsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsavel_id');
    }

    /**
     * Um dia letivo pode ter várias frequências
     *
     * @return HasMany
     */
    public function turmaFrequencias(): HasMany
    {
        return $this->hasMany(TurmaFrequencia::class, 'turma_dia_letivo_id');
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
     * Filtro por ID da turma
     *
     * @param $query
     * @param int $turmaId
     * @return Builder
     */
    public function scopeTurmaId($query, int $turmaId): Builder
    {
        return $query->where('turma_id', $turmaId);
    }

    /**
     * Filtro por dia
     *
     * @param $query
     * @param DateTime $dataLetivo
     * @return Builder
     */
    public function scopeDataLetivo($query, DateTime $dataLetivo): Builder
    {
        return $query->whereDate('data_letivo', $dataLetivo->format('Y-m-d'));
    }

    /**
     * Filtro por usuario_responsavel_id
     *
     * @param $query
     * @param int $usuarioResponsavelId
     * @return Builder
     */
    public function scopeUsuarioResponsavelId($query, int $usuarioResponsavelId): Builder
    {
        return $query->where('usuario_responsavel_id', $usuarioResponsavelId);
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
     * Assessor (getter) para obter o ID do registro de turma dia letivo
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID da turma
     * 
     * @return int
     */
    public function obterTurmaId(): int
    {
        return $this->turma_id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário responsável
     * 
     * @return int
     */
    public function obterUsuarioResponsavelId(): int
    {
        return $this->usuario_responsavel_id;
    }

    /**
     * Assessor (getter) para obter o conteúdo
     * 
     * @return string
     */
    public function obterConteudo(): string
    {
        return $this->conteudo;
    }

    /**
     * Assessor (getter) para obter a anotação particular
     * 
     * @return string|null
     */
    public function obterAnotacaoParticular(): ?string
    {
        return $this->anotacao_particular;
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

    /**
     * Assessor (getter) para obter a data letivo
     * 
     * @return DateTime
     */
    public function obterDataLetivo(): DateTime
    {
        return $this->data_letivo;
    }

    /**
     * Assessor (getter) para obter a data de registro
     * 
     * @return DateTime
     */
    public function obterDataRegistro(): DateTime
    {
        return $this->data_registro;
    }

    /**
     * Assessor (getter) para obter a data de liberação
     * 
     * @return DateTime
     */
    public function obterDataLiberacao(): DateTime
    {
        return $this->data_liberacao;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de turma dia letivo
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID da turma
     *
     * @param int $turmaId
     * @return void
     */
    public function atribuirTurmaId(int $turmaId): void
    {
        $this->turma_id = $turmaId;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário responsável
     *
     * @param int $usuarioResponsavelId
     * @return void
     */
    public function atribuirUsuarioResponsavelId(int $usuarioResponsavelId): void
    {
        $this->usuario_responsavel_id = $usuarioResponsavelId;
    }

    /**
     * Mutador (setter) para atribuir o conteúdo
     *
     * @param string $conteudo
     * @return void
     */
    public function atribuirConteudo(string $conteudo): void
    {
        $this->conteudo = $conteudo;
    }

    /**
     * Mutador (setter) para atribuir a anotação particular
     *
     * @param string|null $anotacaoParticular
     * @return void
     */
    public function atribuirAnotacaoParticular(?string $anotacaoParticular): void
    {
        $this->anotacao_particular = $anotacaoParticular;
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

    /**
     * Mutador (setter) para atribuir a data letivo
     *
     * @param DateTime $dataLetivo
     * @return void
     */
    public function atribuirDataLetivo(DateTime $dataLetivo): void
    {
        $this->data_letivo = $dataLetivo;
    }

    /**
     * Mutador (setter) para atribuir a data de registro
     *
     * @param DateTime $dataRegistro
     * @return void
     */
    public function atribuirDataRegistro(DateTime $dataRegistro): void
    {
        $this->data_registro = $dataRegistro;
    }

    /**
     * Mutador (setter) para atribuir a data de liberação
     *
     * @param DateTime $dataLiberacao
     * @return void
     */
    public function atribuirDataLiberacao(DateTime $dataLiberacao): void
    {
        $this->data_liberacao = $dataLiberacao;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se o dia letivo está ativo
     * 
     * @return bool
     */
    public function verificarAtivo(): bool
    {
        return $this->status === true;
    }

    /**
     * Função para ativar o dia letivo
     * 
     * @return void
     */
    public function ativar(): void
    {
        $this->status = true;
    }

    /**
     * Função para desativar o dia letivo
     * 
     * @return void
     */
    public function desativar(): void
    {
        $this->status = false;
    }

    /**
     * Função para verificar se o dia é hoje
     * 
     * @return bool
     */
    public function verificarHoje(): bool
    {
        $hoje = new DateTime();
        return $this->data_letivo->format('Y-m-d') === $hoje->format('Y-m-d');
    }

    /**
     * Função para verificar se o dia já passou
     * 
     * @return bool
     */
    public function verificarPassado(): bool
    {
        $hoje = new DateTime();
        return $this->data_letivo < $hoje;
    }

    /**
     * Função para verificar se o dia é futuro
     * 
     * @return bool
     */
    public function verificarFuturo(): bool
    {
        $hoje = new DateTime();
        return $this->data_letivo > $hoje;
    }

    /**
     * Função para verificar se foi liberado
     * 
     * @return bool
     */
    public function verificarLiberado(): bool
    {
        $agora = new DateTime();
        return $this->data_liberacao <= $agora;
    }

    /**
     * Função para obter total de frequências
     * 
     * @return int
     */
    public function obterTotalFrequencias(): int
    {
        return $this->turmaFrequencias()->count();
    }

    /**
     * Função estática para buscar por turma
     * 
     * @param int $turmaId
     * @return Collection
     */
    public static function buscarPorTurma(int $turmaId)
    {
        return self::where('turma_id', $turmaId)
            ->orderBy('data_letivo', 'asc')
            ->get();
    }

    /**
     * Função estática para buscar dias letivos ativos
     * 
     * @return Collection
     */
    public static function buscarAtivos()
    {
        return self::where('status', true)->get();
    }

    /**
     * Função estática para buscar por turma e período
     * 
     * @param int $turmaId
     * @param DateTime $dataInicio
     * @param DateTime $dataFim
     * @return Collection
     */
    public static function buscarPorTurmaEPeriodo(int $turmaId, DateTime $dataInicio, DateTime $dataFim)
    {
        return self::where('turma_id', $turmaId)
            ->whereDate('data_letivo', '>=', $dataInicio->format('Y-m-d'))
            ->whereDate('data_letivo', '<=', $dataFim->format('Y-m-d'))
            ->orderBy('data_letivo', 'asc')
            ->get();
    }

}
