<?php

declare(strict_types=1);

/**
 * @file Pivot.php
 * @description Classe-base para todas as tabelas "pivôs" do sistema, responsável pela comunicação com o banco de dados.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Core;

// Importação de classes
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\Pivot as EloquentPivot;

/**
 * Classe Pivot
 *
 * @package App\Core
 * @abstract
 */
abstract class Pivot extends EloquentPivot
{
    // --- ATRIBUTOS ---

    /**
     * Campo de data e hora da criação do registro
     * @var string
     */
    public const CREATED_AT = 'data_criado'; // (padrão do Eloquent ORM)

    /**
     * Campo de data e hora da última atualização do registro
     * @var string
     */
    public const UPDATED_AT = 'data_atualizado'; // (padrão do Eloquent ORM)

    /**
     * Define se o modelo deve registrar `data_criado` e `data_atualizado`
     * @var bool
     */
    public $timestamps = false; // (padrão do Eloquent ORM)

    /**
     * Nome da tabela associada ao modelo
     * @var string
     */
    protected $table = ''; // (padrão do Eloquent ORM)

    /**
     * Os atributos que podem ser atribuídos em massa
     * @var array
     */
    protected $fillable = []; // (padrão do Eloquent ORM)

    /**
     * Os atributos que devem ser ocultados ao serializar o pivô (converter para array ou JSON)
     * @var array
     */
    protected $hidden = []; // (padrão do Eloquent ORM)

    /**
     * Os atributos que devem ser convertidos para tipos nativos (O Eloquent converte automaticamente os valores)
     * @var array
     */
    protected $casts = []; // (padrão do Eloquent ORM)


    // --- MÉTODOS ---

    /**
     * Função-base para criar um registro no pivô do banco de dados
     *
     * @param array $dados
     * @return Pivot
     */
    public function criar(array $dados): Pivot
    {
        return $this->create($dados);
    }

    /**
     * Função-base para atualizar um registro no pivô do banco de dados
     *
     * @param array $dados
     * @return bool
     */
    public function atualizar(array $dados): bool
    {
        return $this->update($dados);
    }

    /**
     * Função-base para salvar todas as alterações feitas no registro do pivô no banco de dados
     *
     * @return bool
     */
    public function salvar(): bool
    {
        return $this->save();
    }

    /**
     * Função-base para excluir um registro do pivô no banco de dados
     *
     * @return bool
     */
    public function excluir(): bool
    {
        return (bool) $this->delete();
    }

    /**
     * Função-base para obter todos os registros no banco de dados
     *
     * @return EloquentCollection
     */
    public static function obterTodos(): EloquentCollection
    {
        return parent::all();
    }

    /**
     * Função-base para obter todos os dados do registro no banco de dados
     *
     * @return array
     */
    public function obterDados(): array
    {
        return $this->toArray();
    }

    /**
     * Função-base para obter o primeiro registro (ou sem registros) encontrado
     *
     * @return ?Pivot
     */
    public function obterPrimeiro(): ?Pivot
    {
        return parent::first();
    }

    /**
     * Função-base para encontrar um registro com base no ID
     *
     * @param int $id
     * @return ?Pivot
     */
    public function encontrar(int $id): ?Pivot
    {
        return parent::find($id);
    }

    /**
     * Função-base para retornar o total de registros de um pivô
     *
     * @return int
     */
    public function total(): int {
        return $this->count();
    }
}