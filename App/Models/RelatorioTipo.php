<?php

/**
 * @file RelatorioTipo.php
 * @description Modelo responsável pelos tipos de relatórios do sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * Classe RelatorioTipo
 *
 * Modelo responsável pelos tipos de relatórios do sistema
 *
 * Exemplo de estrutura dos parâmetros:
 * {
 *   "permissoes": {
 *     "visualizar": ["GAIO_RELATORIO_TIPO_VISUALIZAR"],
 *     "editar": ["admin"],
 *     "emitir": ["admin", "professor", "coordenador"]
 *   },
 *  "formato_padrao": "PDF",
 *  "configuracoes": {
 *    "incluir_cabecalho": true,
 *    "incluir_rodape": false
 * }
 * 
 * }
 *
 * @property int $id
 * @property string $nome
 * @property int|null $dias_vencimento
 * @property array|null $parametros
 * @property bool $status
 *
 * @package App\Models
 * @extends Model
 */
class RelatorioTipo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'relatorios_tipos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome',
        'dias_vencimento',
        'parametros',
        'status'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'nome' => 'string',
        'dias_vencimento' => 'integer',
        'parametros' => 'array',
        'status' => 'boolean'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um tipo de relatório pode ter vários relatórios
     *
     * @return HasMany
     */
    public function relatorios(): HasMany
    {
        return $this->hasMany(Relatorio::class, 'relatorio_tipo_id');
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
     * Filtro por nome (parcial)
     *
     * @param $query
     * @param string $nome
     * @return Builder
     */
    public function scopeNome($query, string $nome): Builder
    {
        return $query->where('nome', 'like', '%' . $nome . '%');
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
     * Assessor (getter) para obter o ID do registro de relatório tipo
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter os dias de vencimento
     * 
     * @return int|null
     */
    public function obterDiasVencimento(): ?int
    {
        return $this->dias_vencimento;
    }

    /**
     * Assessor (getter) para obter os parâmetros
     * 
     * @return array|null
     */
    public function obterParametros(): ?array
    {
        return $this->parametros;
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
     * Mutador (setter) para atribuir o ID do registro de relatório tipo
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir os dias de vencimento
     *
     * @param int|null $diasVencimento
     * @return void
     */
    public function atribuirDiasVencimento(?int $diasVencimento): void
    {
        $this->dias_vencimento = $diasVencimento;
    }

    /**
     * Mutador (setter) para atribuir os parâmetros
     *
     * @param array|null $parametros
     * @return void
     */
    public function atribuirParametros(?array $parametros): void
    {
        $this->parametros = $parametros;
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
     * Função para verificar se o tipo está ativo
     * 
     * @return bool
     */
    public function verificarAtivo(): bool
    {
        return $this->status === true;
    }

    /**
     * Função para ativar o tipo de relatório
     * 
     * @return void
     */
    public function ativar(): void
    {
        $this->status = true;
    }

    /**
     * Função para desativar o tipo de relatório
     * 
     * @return void
     */
    public function desativar(): void
    {
        $this->status = false;
    }

    /**
     * Função para verificar se o tipo possui vencimento
     * 
     * @return bool
     */
    public function verificarPossuiVencimento(): bool
    {
        return $this->dias_vencimento !== null && $this->dias_vencimento > 0;
    }

    /**
     * Função para adicionar parâmetro
     * 
     * @param string $chave
     * @param mixed $valor
     * @return void
     */
    public function adicionarParametro(string $chave, $valor): void
    {
        $parametros = $this->parametros ?? [];
        $parametros[$chave] = $valor;
        $this->parametros = $parametros;
    }

    /**
     * Função para obter parâmetro específico
     * 
     * @param string $chave
     * @return mixed
     */
    public function obterParametro(string $chave)
    {
        return $this->parametros[$chave] ?? null;
    }

    /**
     * Função estática para buscar tipos ativos
     * 
     * @return Collection
     */
    public static function buscarAtivos()
    {
        return self::where('status', true)->get();
    }

    /**
     * Função estática para buscar por nome
     * 
     * @param string $nome
     * @return self|null
     */
    public static function buscarPorNome(string $nome): ?self
    {
        return self::where('nome', $nome)->first();
    }

}
