<?php

declare(strict_types=1);

/**
 * @file Model.php
 * @description Classe-base para todos os modelos do sistema, responsável pela gestão dos dados.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2026
 */

// Declaração de namespace
namespace App\Core;

// Importação de classes
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Classe Model (abstrata)
 * 
 * Gerencia a comunicação com o banco de dados usando Eloquent ORM
 * 
 * @package App\Core
 * @abstract
 */
abstract class Model extends EloquentModel
{

    /**
     * Campo de data e hora da criação do registro
     * @var string
     */
    public const CREATED_AT = 'data_criado';

    /**
     * Campo de data e hora da última atualização do registro
     * @var string
     */
    public const UPDATED_AT = 'data_atualizado';

    /**
     * Desativa timestamps automáticos (data_criado e data_atualizado não são criados automaticamente)
     */
    public $timestamps = false;

    /**
     * Nome da tabela (definido nas classes filhas)
     */
    protected $table = '';

    /**
     * Campos permitidos para preenchimento em massa
     */
    protected $fillable = [];

    /**
     * Campos ocultos em serialização
     */
    protected $hidden = [];

    /**
     * Casts automáticos do Eloquent
     */
    protected $casts = [];

    /**
     * Cria um novo registro com proteção contra mass assignment
     */
    public static function criar(array $dados): static
    {
        $model = new static();
        $model->fill(self::sanitizar($dados));
        $model->save();

        return $model;
    }

    /**
     * Atualiza o registro atual
     */
    public function atualizar(array $dados): bool
    {
        return $this->fill(self::sanitizar($dados))->save();
    }

    /**
     * Persiste alterações no banco
     */
    public function salvar(): bool
    {
        return $this->save();
    }

    /**
     * Remove o registro do banco
     */
    public function excluir(): ?bool
    {
        return $this->delete();
    }

    /**
     * Retorna todos os registros
     */
    public static function obterTodos(): Collection
    {
        return static::all();
    }

    /**
     * Retorna o primeiro registro
     */
    public static function obterPrimeiro(): ?static
    {
        return static::first();
    }

    /**
     * Busca por ID
     */
    public static function buscarPorId(int $id): ?static
    {
        return static::find($id);
    }

    /**
     * Total de registros
     */
    public static function total(): int
    {
        return static::count();
    }

    /**
     * Sanitização básica de dados
     */
    protected static function sanitizar(array $dados): array
    {
        foreach ($dados as $key => $value) {
            if (is_string($value)) {
                $dados[$key] = trim($value);
            }
        }

        return $dados;
    }
}