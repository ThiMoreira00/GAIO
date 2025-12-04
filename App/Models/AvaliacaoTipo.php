<?php

/**
 * @file AvaliacaoTipo.php
 * @description Modelo responsável pelos tipos de avaliações registradas no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\AvaliacaoTipoCategoria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe AvaliacaoTipo
 *
 * Modelo responsável pelos tipos de avaliações registradas no sistema
 *
 * @property int $id
 * @property string $nome
 * @property string|null $descricao
 * @property AvaliacaoTipoCategoria $categoria
 * @property bool $status
 *
 * @package App\Models
 * @extends Model
 */
class AvaliacaoTipo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'avaliacoes_tipos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome',
        'descricao',
        'categoria',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'nome' => 'string',
        'descricao' => 'string',
        'categoria' => AvaliacaoTipoCategoria::class,
        'status' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um tipo de avaliação pode estar associado a várias avaliações de turma
     *
     * @return HasMany
     */
    public function turmaAvaliacoes(): HasMany
    {
        return $this->hasMany(TurmaAvaliacao::class, 'avaliacao_tipo_id');
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
     * Filtro por status (ativo/inativo)
     *
     * @param $query
     * @param bool $status
     * @return Builder
     */
    public function scopeStatus($query, bool $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Filtro por categoria
     *
     * @param $query
     * @param AvaliacaoTipoCategoria $categoria
     * @return Builder
     */
    public function scopeCategoria($query, AvaliacaoTipoCategoria $categoria): Builder
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Filtro por tipos ativos
     *
     * @param $query
     * @return Builder
     */
    public function scopeAtivos($query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Filtro por tipos inativos
     *
     * @param $query
     * @return Builder
     */
    public function scopeInativos($query): Builder
    {
        return $query->where('status', false);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de tipo de avaliação
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do tipo de avaliação
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter a descrição do tipo de avaliação
     * 
     * @return string|null
     */
    public function obterDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Assessor (getter) para obter a categoria do tipo de avaliação
     * 
     * @return AvaliacaoTipoCategoria
     */
    public function obterCategoria(): AvaliacaoTipoCategoria
    {
        return $this->categoria;
    }

    /**
     * Assessor (getter) para obter o status do tipo de avaliação
     * 
     * @return bool
     */
    public function obterStatus(): bool
    {
        return $this->status;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de tipo de avaliação
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome do tipo de avaliação
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir a descrição do tipo de avaliação
     *
     * @param string|null $descricao
     * @return void
     */
    public function atribuirDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * Mutador (setter) para atribuir a categoria do tipo de avaliação
     *
     * @param AvaliacaoTipoCategoria $categoria
     * @return void
     */
    public function atribuirCategoria(AvaliacaoTipoCategoria $categoria): void
    {
        $this->categoria = $categoria;
    }

    /**
     * Mutador (setter) para atribuir o status do tipo de avaliação
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
     * Função para verificar se o tipo de avaliação está ativo
     * 
     * @return bool
     */
    public function verificarAtivo(): bool
    {
        return $this->status === true;
    }

    /**
     * Função para verificar se o tipo de avaliação é lançado
     * 
     * @return bool
     */
    public function verificarLancado(): bool
    {
        return $this->categoria === AvaliacaoTipoCategoria::LANCADO;
    }

    /**
     * Função para verificar se o tipo de avaliação é calculado
     * 
     * @return bool
     */
    public function verificarCalculado(): bool
    {
        return $this->categoria === AvaliacaoTipoCategoria::CALCULADO;
    }

    /**
     * Função para ativar o tipo de avaliação
     * 
     * @return void
     */
    public function ativar(): void
    {
        $this->status = true;
    }

    /**
     * Função para desativar o tipo de avaliação
     * 
     * @return void
     */
    public function desativar(): void
    {
        $this->status = false;
    }

    /**
     * Função estática para buscar tipos de avaliação ativos
     * 
     * @return Collection
     */
    public static function buscarAtivos()
    {
        return self::where('status', true)->get();
    }

    /**
     * Função estática para buscar um tipo de avaliação pelo nome
     * 
     * @param string $nome
     * @return AvaliacaoTipo|null
     */
    public static function buscarPorNome(string $nome): ?AvaliacaoTipo
    {
        return self::where('nome', $nome)->first();
    }

}