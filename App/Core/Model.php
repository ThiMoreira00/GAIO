<?php

/**
 * @file Model.php
 * @description Classe-base para todos os "models" do sistema, responsável pela comunicação com o banco de dados.
 * @author Thiago Moreira
 * @copyright Copright (c) 2025
 */

// Declaração de namespace
namespace App\Core;

// Importação de classes
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use UnitEnum;

/**
 * Classe Model (abstrata)
 *
 * Gerencia a comunicação com o banco de dados
 *
 * @package App\Core
 * @abstract
 */
abstract class Model extends EloquentModel
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
     * Os atributos que devem ser ocultados ao serializar o modelo (converter para array ou JSON)
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
        $dados = [];
        
        // Percorre todos os atributos do modelo
        foreach ($this->getAttributes() as $key => $value) {
            // Verifica se existe um cast para este atributo
            if (isset($this->casts[$key])) {
                $castType = $this->casts[$key];
                
                // Verifica se é um enum (classe que existe e é enum)
                if (class_exists($castType) && enum_exists($castType)) {
                    // Remove espaços extras do valor antes de tentar converter
                    $cleanValue = is_string($value) ? trim($value) : $value;
                    
                    // Tenta obter o enum através do valor limpo
                    try {
                        $enumValue = null;
                        
                        // Primeiro tenta pelo value (tryFrom)
                        if (method_exists($castType, 'tryFrom')) {
                            $enumValue = $castType::tryFrom($cleanValue);
                        }
                        
                        // Se não encontrou, tenta pelo name
                        if ($enumValue === null && method_exists($castType, 'fromName')) {
                            $enumValue = $castType::fromName($cleanValue);
                        }
                        
                        // Se ainda não encontrou, tenta buscar manualmente
                        if ($enumValue === null) {
                            foreach ($castType::cases() as $case) {
                                // Verifica se o valor do banco corresponde ao name do enum
                                if ($case->name === $cleanValue) {
                                    $enumValue = $case;
                                    break;
                                }
                                // Verifica se o valor do banco corresponde ao value do enum
                                if ($case instanceof \BackedEnum && $case->value === $cleanValue) {
                                    $enumValue = $case;
                                    break;
                                }
                            }
                        }
                        
                        if ($enumValue instanceof \UnitEnum) {
                            $dados[$key] = [
                                'name' => $enumValue->name,
                                'value' => $enumValue instanceof \BackedEnum ? $enumValue->value : $enumValue->name
                            ];
                            continue;
                        }
                    } catch (\ValueError $e) {
                        // Se falhar, usa o valor original
                        $dados[$key] = $cleanValue;
                        continue;
                    }
                }
            }
            
            // Para não-enums, usa o valor original
            $dados[$key] = $value;
        }
        
        // Adiciona os relacionamentos carregados
        foreach ($this->getRelations() as $key => $value) {
            $dados[$key] = $value;
        }
        
        return $dados;
    }


    /**
     * Função-base para obter o primeiro registro (ou sem registros) encontrado
     *
     * @return Model|null
     */
    public static function obterPrimeiro(): ?Model
    {
        return parent::first();
    }

    /**
     * Função-base para encontrar um registro com base no ID
     *
     * @param int $id
     * @return Model|null
     */
    public static function buscarPorId(int $id): ?Model
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
