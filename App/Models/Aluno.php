<?php

/**
 * @file Aluno.php
 * @description Modelo responsável pelos alunos registrados no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe Aluno
 *
 * Modelo responsável pelos alunos registrados no sistema
 *
 * @property int $id
 * @property int $usuario_id
 *
 * @package App\Models
 * @extends Model
 */
class Aluno extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'usuario_id'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'usuario_id' => 'integer'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um aluno pertence a um usuário (Usuario)
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Um aluno pode possuir mais de uma matrícula
     * 
     * @return HasMany
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(AlunoMatricula::class, 'aluno_id');
    }

    /**
     * Um aluno possui várias escolas
     *
     * @return HasMany
     */
    public function escolas(): HasMany
    {
        return $this->hasMany(AlunoEscola::class, 'aluno_id');
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


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do aluno
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário associado ao aluno
     * 
     * @return int
     */
    public function obterUsuarioId(): int
    {
        return $this->usuario_id;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do aluno
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário associado ao aluno
     *
     * @param int $usuarioId
     * @return void
     */
    public function atribuirUsuarioId(int $usuarioId): void
    {
        $this->usuario_id = $usuarioId;
    }

    
    // --- MÉTODOS AUXILIARES ---

    /**
     * Delegar métodos de usuário
     *
     * @param mixed $metodo
     * @param mixed $parametros
     */
    public function __call($metodo, $parametros)
    {
        // Verifica se o relacionamento usuario está carregado e se o método existe
        if ($this->relationLoaded('usuario') && $this->usuario && method_exists($this->usuario, $metodo)) {
            return $this->usuario->$metodo(...$parametros);
        }

        return parent::__call($metodo, $parametros);
    }


    /**
     * Função para obter a última matrícula do aluno
     * 
     * @return ?AlunoMatricula
     */
    public function obterUltimaMatricula(): ?AlunoMatricula
    {
        return $this->matriculas()
            ->orderBy('data_matricula', 'desc')
            ->first();
    }

}