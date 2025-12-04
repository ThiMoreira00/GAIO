<?php

/**
 * @file NotificacaoModelo.php
 * @description Modelo responsável pelos modelos das notificações disparadas no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use archived\NotificacaoCategoria;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe NotificacaoModelo
 *
 * Modelo responsável pelos modelos das notificações disparadas no sistema
 *
 * @property int $id
 * @property string $codigo
 * @property string $titulo
 * @property string $mensagem
 * @property string $icone
 * @property string $cor
 *
 * @package App\Models
 * @extends Model
 */
class NotificacaoModelo extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'notificacoes_modelos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'codigo',
        'titulo',
        'mensagem',
        'icone',
        'cor'
    ];


    // --- SCOPES ---

    /**
     * Filtro de modelos de notificações com base no código
     *
     * @param $query
     * @param string $codigo
     * @return void
     */
    public function scopeCodigo($query, string $codigo): void {
        $query->where('codigo', $codigo);
    }


    // --- RELACIONAMENTOS ---

    /**
     * Um modelo pode ser usado para gerar muitas notificações
     * TODO: Relacionamento?
     *
     * @return HasMany
    */
    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class, 'modelo_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do modelo da notificação
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o código do modelo da notificação
     *
     * @return string
     */
    public function obterCodigo(): string {
        return $this->codigo;
    }

    /**
     * Assessor (getter) para obter o título do modelo da notificação
     *
     * @return string
     */
    public function obterTitulo(): string {
        return $this->titulo;
    }

    /**
     * Assessor (getter) para obter a mensagem do modelo da notificação
     *
     * @return string
     */
    public function obterMensagem(): string {
        return $this->mensagem;
    }

    /**
     * Assessor (getter) para obter o ícone do modelo da notificação
     *
     * @return string
     */
    public function obterIcone(): string {
        return $this->icone;
    }

    /**
     * Assessor (getter) para obter a cor (classe do TailwindCSS) do modelo da notificação
     *
     * @return string
     */
    public function obterCor(): string {
        return $this->cor;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do modelo da notificação
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o código do modelo da notificação
     *
     * @param string $codigo
     * @return void
     */
    public function atribuirCodigo(string $codigo): void {
        $this->codigo = $codigo;
    }

    /**
     * Mutador (setter) para atribuir o título do modelo da notificação
     *
     * @param string $titulo
     * @return void
     */
    public function atribuirTitulo(string $titulo): void {
        $this->titulo = $titulo;
    }

    /**
     * Mutador (setter) para atribuir a mensagem do modelo da notificação
     *
     * @param string $mensagem
     * @return void
     */
    public function atribuirMensagem(string $mensagem): void {
        $this->mensagem = $mensagem;
    }

    /**
     * Mutador (setter) para atribuir o ícone do modelo da notificação
     *
     * @param string $icone
     * @return void
     */
    public function atribuirIcone(string $icone): void {
        $this->icone = $icone;
    }

    /**
     * Mutador (setter) para atribuir a cor (classe do TailwindCSS) do modelo da notificação
     *
     * @param string $cor
     * @return void
     */
    public function atribuirCor(string $cor): void {
        $this->cor = $cor;
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Função para buscar um modelo de notificação pelo código
     *
     * @param string $codigo
     * @return ?NotificacaoModelo
     */
    public static function buscarPorCodigo(string $codigo): ?NotificacaoModelo {
        return self::codigo($codigo)->first() ?? null;
    }
}