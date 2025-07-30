<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

abstract class Model extends EloquentModel
{

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    /**
     * O nome da tabela associada ao modelo.
     * @var string
     */
    protected $table = '';

    /**
     * Os atributos que podem ser atribuídos em massa.
     * Essencial para segurança ao usar métodos como `create()` ou `update()`.
     * @var array
     */
    protected $fillable = [];

    /**
     * Os atributos que devem ser ocultados ao serializar o modelo (converter para array ou JSON).
     * Usado para proteger dados sensíveis.
     * @var array
     */
    protected $hidden = [];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     * O Eloquent converte automaticamente os valores ao acessá-los.
     * @var array
     */
    protected $casts = [];

    /**
     * Define se o modelo deve registrar `created_at` e `updated_at`.
     * Por padrão é `true`.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Função-base para criar um registro no modelo do banco de dados
     *
     * @param array $dados
     * @return Model
     */
    public function criar(array $dados): Model
    {
        return $this->create($dados);
    }

    /**
     * Função-base para atualizar um registro no modelo do banco de dados
     *
     * @param array $dados
     * @return bool
     */
    public function atualizar(array $dados): bool
    {
        return $this->update($dados);
    }

    /**
     * Função-base para salvar todas as alterações feitas no registro do modelo no banco de dados
     *
     * @return bool
     */
    public function salvar(): bool
    {
        return $this->save();
    }

    /**
     * Função-base para excluir um registro do modelo no banco de dados
     *
     * @return bool
     */
    public function excluir(): bool
    {
        return $this->delete();
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
     * @return Model|null
     */
    public function obterPrimeiro(): ?Model
    {
        return parent::first();
    }

    /**
     * Função-base para encontrar um registro com base no ID
     *
     * @param int $id
     * @return Model|null
     */
    public function encontrar(int $id): ?Model
    {
        return parent::find($id);
    }

    /**
     * Função-base para retornar o total de registros de um modelo
     *
     * @return int
     */
    public function total(): int {
        return $this->count();
    }


}