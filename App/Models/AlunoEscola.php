<?php

/**
 * @file AlunoEscola.php
 * @description Modelo responsável pelas escolas anteriores dos alunos
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\AlunoEscolaModalidade;
use App\Models\Enumerations\UF;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe AlunoEscola
 *
 * Modelo responsável pelas escolas anteriores dos alunos
 *
 * @property int $id
 * @property int $aluno_id
 * @property string $nome
 * @property string $cidade
 * @property UF $uf
 * @property int $ano_conclusao
 * @property AlunoEscolaModalidade $modalidade
 *
 * @package App\Models
 * @extends Model
 */
class AlunoEscola extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos_escolas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'aluno_id',
        'nome',
        'cidade',
        'uf',
        'ano_conclusao',
        'modalidade'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'aluno_id' => 'integer',
        'nome' => 'string',
        'cidade' => 'string',
        'uf' => UF::class,
        'ano_conclusao' => 'integer',
        'modalidade' => AlunoEscolaModalidade::class
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um registro de escola pertence a um aluno
     *
     * @return BelongsTo
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
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
     * Filtro por ID do aluno
     *
     * @param $query
     * @param int $alunoId
     * @return Builder
     */
    public function scopeAlunoId($query, int $alunoId): Builder
    {
        return $query->where('aluno_id', $alunoId);
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do registro da escola
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome da escola
     * 
     * @return string
     */
    public function obterNome(): string
    {
        return $this->nome;
    }

    /**
     * Assessor (getter) para obter a cidade da escola
     * 
     * @return string
     */
    public function obterCidade(): string
    {
        return $this->cidade;
    }

    /**
     * Assessor (getter) para obter a UF da escola
     * 
     * @return UF
     */
    public function obterUF(): UF
    {
        return $this->uf;
    }

    /**
     * Assessor (getter) para obter o ano de conclusão na escola
     * 
     * @return int
     */
    public function obterAnoConclusao(): int
    {
        return $this->ano_conclusao;
    }

    /**
     * Assessor (getter) para obter a modalidade da escola
     * 
     * @return AlunoEscolaModalidade
     */
    public function obterModalidade(): AlunoEscolaModalidade
    {
        return $this->modalidade;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o nome da escola
     *
     * @param string $nome
     * @return void
     */
    public function atribuirNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Mutador (setter) para atribuir a cidade da escola
     *
     * @param string $cidade
     * @return void
     */
    public function atribuirCidade(string $cidade): void
    {
        $this->cidade = $cidade;
    }

    /**
     * Mutador (setter) para atribuir a UF da escola
     *
     * @param UF $uf
     * @return void
     */
    public function atribuirUF(UF $uf): void
    {
        $this->uf = $uf;
    }

    /**
     * Mutador (setter) para atribuir o ano de conclusão na escola
     *
     * @param int $anoConclusao
     * @return void
     */
    public function atribuirAnoConclusao(int $anoConclusao): void
    {
        $this->ano_conclusao = $anoConclusao;
    }

    /**
     * Mutador (setter) para atribuir a modalidade da escola
     *
     * @param AlunoEscolaModalidade $modalidade
     * @return void
     */
    public function atribuirModalidade(AlunoEscolaModalidade $modalidade): void
    {
        $this->modalidade = $modalidade;
    }

}