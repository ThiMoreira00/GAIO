<?php

/**
 * @file Professor.php
 * @description Modelo responsável pelos professores do sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\ProfessorTitulo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classe Professor
 *
 * Modelo responsável pelos professores do sistema
 *
 * @property int $id
 * @property int $usuario_id
 * @property ProfessorTitulo|null $titulo
 * @property string|null $lattes_codigo
 *
 * @package App\Models
 * @extends Model
 */
class Professor extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'professores';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'titulo',
        'lattes_codigo'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'usuario_id' => 'integer',
        'titulo' => ProfessorTitulo::class,
        'lattes_codigo' => 'string'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um professor pertence a um usuário
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Um professor pode ter várias matrículas
     *
     * @return HasMany
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(ProfessorMatricula::class, 'professor_id');
    }

    /**
     * Um professor pode ter várias turmas
     *
     * @return HasMany
     */
    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class, 'professor_id');
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
     * Filtro por ID do usuário
     *
     * @param $query
     * @param int $usuarioId
     * @return Builder
     */
    public function scopeUsuarioId($query, int $usuarioId): Builder
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Filtro por título
     *
     * @param $query
     * @param ProfessorTitulo $titulo
     * @return Builder
     */
    public function scopeTitulo($query, ProfessorTitulo $titulo): Builder
    {
        return $query->where('titulo', $titulo);
    }

    /**
     * Filtro por código Lattes (parcial)
     *
     * @param $query
     * @param string $codigo
     * @return Builder
     */
    public function scopeLatteCodigo($query, string $codigo): Builder
    {
        return $query->where('lattes_codigo', 'like', '%' . $codigo . '%');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro de professor
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário
     * 
     * @return int
     */
    public function obterUsuarioId(): int
    {
        return $this->usuario_id;
    }

    /**
     * Assessor (getter) para obter o título
     * 
     * @return ProfessorTitulo|null
     */
    public function obterTitulo(): ?ProfessorTitulo
    {
        return $this->titulo;
    }

    /**
     * Assessor (getter) para obter o código Lattes
     * 
     * @return string|null
     */
    public function obterLattesCodigo(): ?string
    {
        return $this->lattes_codigo;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do registro de professor
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário
     *
     * @param int $usuarioId
     * @return void
     */
    public function atribuirUsuarioId(int $usuarioId): void
    {
        $this->usuario_id = $usuarioId;
    }

    /**
     * Mutador (setter) para atribuir o título
     *
     * @param ProfessorTitulo|null $titulo
     * @return void
     */
    public function atribuirTitulo(?ProfessorTitulo $titulo): void
    {
        $this->titulo = $titulo;
    }

    /**
     * Mutador (setter) para atribuir o código Lattes
     *
     * @param string|null $lattesCodigo
     * @return void
     */
    public function atribuirLattesCodigo(?string $lattesCodigo): void
    {
        $this->lattes_codigo = $lattesCodigo;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Função para verificar se o professor é mestre
     * 
     * @return bool
     */
    public function verificarMestre(): bool
    {
        return $this->titulo === ProfessorTitulo::MESTRE;
    }

    /**
     * Função para verificar se o professor é doutor
     * 
     * @return bool
     */
    public function verificarDoutor(): bool
    {
        return $this->titulo === ProfessorTitulo::DOUTOR;
    }

    /**
     * Função para verificar se o professor é especialista
     * 
     * @return bool
     */
    public function verificarEspecialista(): bool
    {
        return $this->titulo === ProfessorTitulo::ESPECIALISTA;
    }

    /**
     * Função para verificar se o professor possui Lattes cadastrado
     * 
     * @return bool
     */
    public function verificarPossuiLattes(): bool
    {
        return !empty($this->lattes_codigo);
    }

    /**
     * Função estática para buscar professores por título
     * 
     * @param ProfessorTitulo $titulo
     * @return Collection
     */
    public static function buscarPorTitulo(ProfessorTitulo $titulo): Collection
    {
        return self::where('titulo', $titulo)->get();
    }

    /**
     * Função estática para buscar professor por usuário
     * 
     * @param int $usuarioId
     * @return self|null
     */
    public static function buscarPorUsuario(int $usuarioId): ?self
    {
        return self::where('usuario_id', $usuarioId)->first();
    }

    /**
     * Função estática para obter todos os professores alocados (com turmas atribuídas e ativas)
     * 
     * @return Collection
     */
    public static function obterAlocados(): Collection
    {
        return self::whereHas('turmas', function (Builder $query) {
            $query->where('status', 'ATIVA');
        })->get();
    }

    /**
     * Função estática para contar professores alocados
     * 
     * @return int
     */
    public static function contarAlocados(): int
    {
        return self::whereHas('turmas', function (Builder $query) {
            $query->where('status', 'ATIVA');
        })->count();
    }

}