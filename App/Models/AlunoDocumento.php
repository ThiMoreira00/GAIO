<?php

/**
 * @file AlunoDocumento.php
 * @description Modelo responsável pelos documentos dos alunos
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
 * Classe AlunoDocumento
 *
 * Modelo responsável pelos documentos dos alunos
 *
 * @property int $id
 * @property int $aluno_id
 * @property int $documento_tipo_id
 * @property object $metadados
 *
 * @package App\Models
 * @extends Model
 */
class AlunoDocumento extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'alunos_documentos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'aluno_id',
        'documento_tipo_id',
        'metadados'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'aluno_id' => 'integer',
        'documento_tipo_id' => 'integer',
        'metadados' => 'object'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um aluno pertence a um usuário (Usuario)
     *
     * @return BelongsTo
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    /**
     * Um documento pertence a um tipo de documento (DocumentoTipo)
     *
     * @return BelongsTo
     */
    public function documentoTipo(): BelongsTo
    {
        return $this->belongsTo(AlunoDocumentoTipo::class, 'documento_tipo_id');
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
     * Assessor (getter) para obter o ID do documento
     * 
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do aluno
     * 
     * @return int
     */
    public function obterAlunoId(): int
    {
        return $this->aluno_id;
    }

    /**
     * Assessor (getter) para obter o ID do tipo de documento
     * 
     * @return int
     */
    public function obterDocumentoTipoId(): int
    {
        return $this->documento_tipo_id;
    }

    /**
     * Assessor (getter) para obter os metadados do documento
     * 
     * @return object
     */
    public function obterMetadados(): object
    {
        return $this->metadados;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do documento
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do aluno
     *
     * @param int $alunoId
     * @return void
     */
    public function atribuirAlunoId(int $alunoId): void
    {
        $this->aluno_id = $alunoId;
    }

    /**
     * Mutador (setter) para atribuir o ID do tipo de documento
     *
     * @param int $documentoTipoId
     * @return void
     */
    public function atribuirDocumentoTipoId(int $documentoTipoId): void
    {
        $this->documento_tipo_id = $documentoTipoId;
    }

    /**
     * Mutador (setter) para atribuir os metadados do documento
     *
     * @param object $metadados
     * @return void
     */
    public function atribuirMetadados(object $metadados): void
    {
        $this->metadados = $metadados;
    }

    
    // --- MÉTODOS AUXILIARES ---

    /**
     * Função auxiliar para verificar se o documento possui metadados específicos
     *
     * @param string $chave
     * @return bool
     */
    public function possuiMetadado(string $chave): bool
    {
        return isset($this->metadados->{$chave});
    }

    /**
     * Função auxiliar para obter um metadado específico do documento
     *
     * @param string $chave
     * @return mixed|null
     */
    public function obterMetadado(string $chave)
    {
        return $this->possuiMetadado($chave) ? $this->metadados->{$chave} : null;
    }

}