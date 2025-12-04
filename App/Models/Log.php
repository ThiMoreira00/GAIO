<?php

/**
 * @file Log.php
 * @description Modelo responsável pelas logs do sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe Log
 *
 * Modelo responsável pelas logs do sistema
 *
 * @package App\Models
 * @extends Model
 */
class Log extends Model {

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'logs';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'tipo',
        'descricao',
        'data_registro'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos
     * @var array
     */
    protected $casts = [
        'data_registro' => 'datetime'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Várias logs podem estar atribuídas a um usuário
     * TODO: Relacionamento?
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário que fez o registro
     *
     * @return int
     */
    public function obterUsuarioId(): int {
        return $this->usuario_id;
    }

    /**
     * Assessor (getter) para obter o tipo do registro
     *
     * @return string
     */
    public function obterTipo(): string {
        return $this->tipo;
    }

    /**
     * Assessor (getter) para obter a descrição do registro
     *
     * @return string
     */
    public function obterDescricao(): string {
        return $this->descricao;
    }

    /**
     * Assessor (getter) para obter a data de registro
     *
     * @return DateTime
     */
    public function obterDataRegistro(): DateTime {
        return $this->data_registro;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário que fez o registro
     *
     * @param int $usuarioId
     * @return void
     */
    public function atribuirUsuarioId(int $usuarioId): void {
        $this->usuario_id = $usuarioId;
    }

    /**
     * Mutador (setter) para atribuir o tipo do registro
     *
     * @param string $tipo
     * @return void
     */
    public function atribuirTipo(string $tipo): void {
        $this->tipo = $tipo;
    }

    /**
     * Mutador (setter) para atribuir o registro
     *
     * @param string $descricao
     * @return void
     */
    public function atribuirDescricao(string $descricao): void {
        $this->descricao = $descricao;
    }

    /**
     * Mutador (setter) para atribuir a data de registro
     *
     * @param DateTime $data_registro
     * @return void
     */
    public function atribuirDataRegistro(DateTime $data_registro): void {
        $this->attributes['data_registro'] = $data_registro->format('Y-m-d H:i:s');
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Função para registrar uma log
     *
     * @param ?int $usuarioId
     * @param string $tipo
     * @param string $descricao
     * @return void
     */
    public static function registrar(?int $usuarioId, string $tipo, string $descricao): void {
        $log = new Log();
        $log->atribuirUsuarioId($usuarioId ?: null);
        $log->atribuirTipo($tipo);
        $log->atribuirDescricao($descricao);
        $log->atribuirDataRegistro(new DateTime());
        $log->salvar();
    }
}