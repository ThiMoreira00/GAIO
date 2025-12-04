<?php

/**
 * @file Espaco.php
 * @description Modelo responsável pelos espaços (salas de aula) do sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\EspacoTipo;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe Espaco
 *
 * Modelo responsável pelos espaços do sistema (salas de aula, laboratórios, etc.)
 *
 * @property int $id
 * @property string $nome
 * @property string $codigo
 * @property int $capacidade_maxima
 * @property EspacoTipo $tipo
 * @property bool $status
 *
 * @package App\Models
 * @extends Model
 */
class Espaco extends Model
{

    // --- PROPRIEDADES ---

    /**
     * Tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'espacos';

    /**
     * Campos que podem ser preenchidos em massa
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'codigo',
        'capacidade_maxima',
        'tipo',
        'status'
    ];

    /**
     * Campos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'nome' => 'string',
        'codigo' => 'string',
        'capacidade_maxima' => 'integer',
        'tipo' => 'string',
        'status' => 'boolean'
    ];


    // --- MÉTODOS ESTÁTICOS ---

    /**
     * Escopo para filtrar espaços por status
     *
     * @param Builder $query
     * @param bool $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, bool $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Escopo para filtrar espaços por tipo
     *
     * @param Builder $query
     * @param EspacoTipo $tipo
     * @return Builder
     */
    public function scopeTipo(Builder $query, EspacoTipo $tipo): Builder
    {
        return $query->where('tipo', $tipo->name);
    }

    /**
     * Escopo para buscar espaços por nome
     *
     * @param Builder $query
     * @param string $termo
     * @return Builder
     */
    public function scopeNome(Builder $query, string $termo): Builder
    {
        return $query->where('nome', 'LIKE', "%$termo%");
    }

    /**
     * Escopo para buscar espaços por código
     *
     * @param Builder $query
     * @param string $termo
     * @return Builder
     */
    public function scopeCodigo(Builder $query, string $termo): Builder
    {
        return $query->where('codigo', 'LIKE', "%$termo%");
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do espaço
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome do espaço
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter o código do espaço
     * 
     * @return string
     */
    public function obterCodigo(): string
    {
        return $this->codigo;
    }

    /**
     * Assessor (getter) para obter a capacidade máxima do espaço
     * 
     * @return int
     */
    public function obterCapacidadeMaxima(): int
    {
        return $this->capacidade_maxima;
    }

    /**
     * Assessor (getter) para obter o tipo do espaço
     * 
     * @return EspacoTipo
     */
    public function obterTipo(): EspacoTipo
    {
        $tipo = $this->attributes['tipo'] ?? $this->tipo;
        // Se já for um enum, retorna direto, senão converte da chave (nome)
        return $tipo instanceof EspacoTipo 
            ? $tipo 
            : EspacoTipo::fromName($tipo);
    }

    /**
     * Assessor (getter) para obter o status do espaço
     * 
     * @return bool
     */
    public function obterStatus(): bool
    {
        return (bool) $this->status;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do espaço
     * 
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome do espaço
     * 
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir o código do espaço
     * 
     * @param string $codigo
     * @return void
     */
    public function atribuirCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * Mutador (setter) para atribuir a capacidade máxima do espaço
     * 
     * @param int $capacidade
     * @return void
     */
    public function atribuirCapacidadeMaxima(int $capacidade): void
    {
        $this->capacidade_maxima = $capacidade;
    }

    /**
     * Mutador (setter) para atribuir o tipo do espaço
     * 
     * @param EspacoTipo $tipo
     * @return void
     */
    public function atribuirTipo(EspacoTipo $tipo): void
    {
        $this->attributes['tipo'] = $tipo->name;
    }

    /**
     * Mutador (setter) para atribuir o status do espaço
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
     * Sobrescreve o método obterDados para formatar o tipo corretamente
     * 
     * @return array
     */
    public function obterDados(): array
    {
        $dados = parent::obterDados();
        
        // Formata o tipo como array com nome e valor
        if (isset($dados['tipo'])) {
            $tipoEnum = $this->obterTipo();
            $dados['tipo'] = [
                'nome' => $tipoEnum->name,
                'valor' => $tipoEnum->value
            ];
        }
        
        return $dados;
    }

    /**
     * Função para gerar um código único para o espaço
     * 
     * @return string
     */
    public function gerarCodigo(): string
    {

        // Os 3 primeiros caracteres do tipo do espaço + 5 números sequenciais
        // Ex.: SAL00001 para uma sala de aula, LAB00002 para um laboratório, etc.
        $prefixo = strtoupper(substr($this->obterTipo()->name, 0, 3));
        $sufixo = str_pad($this->id, 5, '0', STR_PAD_LEFT);

        return $prefixo . $sufixo;
    }

    /**
     * Função para arquivar o espaço
     * 
     * @return void
     */
    public function arquivar(): void
    {
        $this->atribuirStatus(false);
        $this->salvar();
    }

}