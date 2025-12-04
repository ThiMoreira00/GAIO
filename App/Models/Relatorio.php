<?php

/**
 * @file Relatorio.php
 * @description Modelo responsável pelos relatórios do sistema
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
 * Classe Relatorio
 *
 * Modelo responsável pelos relatórios do sistema
 *
 * Exemplo de uso dos metadados:
 * {
 *   "periodo_inicio": "2025-01-01",
 *   "periodo_fim": "2025-12-31",
 *   "filtros": {
 *     "turma_id": 123,
 *     "status": "ativo"
 *   },
 *   "totalizadores": {
 *     "total_alunos": 150,
 *     "aprovados": 120,
 *     "reprovados": 30
 *   },
 *   "formato_gerado": "PDF"
 * }
 *
 * @property int $id
 * @property int $relatorio_tipo_id
 * @property int $usuario_emissor_id
 * @property string $codigo_validador
 * @property DateTime $data_emissao
 * @property DateTime|null $data_vencimento
 * @property bool $status
 * @property array|null $metadados
 *
 * @package App\Models
 * @extends Model
 */
class Relatorio extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'relatorios';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'relatorio_tipo_id',
        'usuario_emissor_id',
        'codigo_validador',
        'data_emissao',
        'data_vencimento',
        'status',
        'metadados'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'relatorio_tipo_id' => 'integer',
        'usuario_emissor_id' => 'integer',
        'codigo_validador' => 'string',
        'data_emissao' => 'datetime',
        'data_vencimento' => 'datetime',
        'status' => 'boolean',
        'metadados' => 'array'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um relatório pertence a um tipo de relatório
     *
     * @return BelongsTo
     */
    public function relatorioTipo(): BelongsTo
    {
        return $this->belongsTo(RelatorioTipo::class, 'relatorio_tipo_id');
    }

    /**
     * Um relatório pertence a um usuário emissor
     *
     * @return BelongsTo
     */
    public function usuarioEmissor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_emissor_id');
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
     * Filtro por ID do tipo de relatório
     *
     * @param $query
     * @param int $relatorioTipoId
     * @return Builder
     */
    public function scopeRelatorioTipoId($query, int $relatorioTipoId): Builder
    {
        return $query->where('relatorio_tipo_id', $relatorioTipoId);
    }

    /**
     * Filtro por ID do usuário emissor
     *
     * @param $query
     * @param int $usuarioEmissorId
     * @return Builder
     */
    public function scopeUsuarioEmissorId($query, int $usuarioEmissorId): Builder
    {
        return $query->where('usuario_emissor_id', $usuarioEmissorId);
    }

    /**
     * Filtro por código validador
     *
     * @param $query
     * @param string $codigo
     * @return Builder
     */
    public function scopeCodigoValidador($query, string $codigo): Builder
    {
        return $query->where('codigo_validador', $codigo);
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

    /**
     * Filtro por formato
     *
     * @param $query
     * @param string $formato
     * @return Builder
     */
    public function scopeFormato($query, string $formato): Builder
    {
        return $query->where('formato', $formato);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de relatório
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do tipo de relatório
     * 
     * @return int
     */
    public function obterRelatorioTipoId(): int
    {
        return $this->relatorio_tipo_id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário emissor
     * 
     * @return int
     */
    public function obterUsuarioEmissorId(): int
    {
        return $this->usuario_emissor_id;
    }

    /**
     * Assessor (getter) para obter o código validador
     * 
     * @return string
     */
    public function obterCodigoValidador(): string
    {
        return $this->codigo_validador;
    }

    /**
     * Assessor (getter) para obter a data de emissão
     * 
     * @return DateTime
     */
    public function obterDataEmissao(): DateTime
    {
        return $this->data_emissao;
    }

    /**
     * Assessor (getter) para obter a data de vencimento
     * 
     * @return DateTime|null
     */
    public function obterDataVencimento(): ?DateTime
    {
        return $this->data_vencimento;
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
     * Assessor (getter) para obter os metadados
     * 
     * @return array|null
     */
    public function obterMetadados(): ?array
    {
        return $this->metadados;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de relatório
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do tipo de relatório
     *
     * @param int $relatorioTipoId
     * @return void
     */
    public function atribuirRelatorioTipoId(int $relatorioTipoId): void
    {
        $this->relatorio_tipo_id = $relatorioTipoId;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário emissor
     *
     * @param int $usuarioEmissorId
     * @return void
     */
    public function atribuirUsuarioEmissorId(int $usuarioEmissorId): void
    {
        $this->usuario_emissor_id = $usuarioEmissorId;
    }

    /**
     * Mutador (setter) para atribuir o código validador
     *
     * @param string $codigoValidador
     * @return void
     */
    public function atribuirCodigoValidador(string $codigoValidador): void
    {
        $this->codigo_validador = $codigoValidador;
    }

    /**
     * Mutador (setter) para atribuir a data de emissão
     *
     * @param DateTime $dataEmissao
     * @return void
     */
    public function atribuirDataEmissao(DateTime $dataEmissao): void
    {
        $this->data_emissao = $dataEmissao;
    }

    /**
     * Mutador (setter) para atribuir a data de vencimento
     *
     * @param DateTime|null $dataVencimento
     * @return void
     */
    public function atribuirDataVencimento(?DateTime $dataVencimento): void
    {
        $this->data_vencimento = $dataVencimento;
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
     * Mutador (setter) para atribuir os metadados
     *
     * @param array|null $metadados
     * @return void
     */
    public function atribuirMetadados(?array $metadados): void
    {
        $this->metadados = $metadados;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se o relatório está ativo
     * 
     * @return bool
     */
    public function verificarAtivo(): bool
    {
        return $this->status === true;
    }

    /**
     * Função para verificar se o relatório está vencido
     * 
     * @return bool
     */
    public function verificarVencido(): bool
    {
        if ($this->data_vencimento === null) {
            return false;
        }
        
        $hoje = new DateTime();
        return $this->data_vencimento < $hoje;
    }

    /**
     * Função para verificar se o relatório é válido
     * 
     * @return bool
     */
    public function verificarValido(): bool
    {
        return $this->status && !$this->verificarVencido();
    }

    /**
     * Função para gerar código validador único
     * 
     * @return string
     */
    public static function gerarCodigoValidador(): string
    {
        return strtoupper(bin2hex(random_bytes(8)));
    }

    /**
     * Função para adicionar metadado
     * 
     * @param string $chave
     * @param mixed $valor
     * @return void
     */
    public function adicionarMetadado(string $chave, $valor): void
    {
        $metadados = $this->metadados ?? [];
        $metadados[$chave] = $valor;
        $this->metadados = $metadados;
        $this->save();
    }

    /**
     * Função para obter metadado específico
     * 
     * @param string $chave
     * @return mixed
     */
    public function obterMetadado(string $chave)
    {
        return $this->metadados[$chave] ?? null;
    }

    /**
     * Função estática para buscar por código validador
     * 
     * @param string $codigo
     * @return self|null
     */
    public static function buscarPorCodigoValidador(string $codigo): ?self
    {
        return self::where('codigo_validador', $codigo)->first();
    }

    /**
     * Função estática para buscar relatórios válidos
     * 
     * @return Collection
     */
    public static function buscarValidos()
    {
        $hoje = new DateTime();
        return self::where('status', true)
            ->where(function($query) use ($hoje) {
                $query->whereNull('data_vencimento')
                    ->orWhere('data_vencimento', '>=', $hoje);
            })
            ->get();
    }

    /**
     * Função estática para buscar por usuário emissor
     * 
     * @param int $usuarioId
     * @return Collection
     */
    public static function buscarPorUsuarioEmissor(int $usuarioId)
    {
        return self::where('usuario_emissor_id', $usuarioId)
            ->orderBy('data_emissao', 'desc')
            ->get();
    }

}