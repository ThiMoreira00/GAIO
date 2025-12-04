<?php

/**
 * @file UsuarioContato.php
 * @description Modelo responsável pelas informações de contato do usuário.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\UF;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe UsuarioContato
 *
 * Modelo responsável pelas informações de contato do usuário
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $cep
 * @property string $endereco
 * @property ?string $numero
 * @property ?string $complemento
 * @property string $bairro
 * @property string $cidade
 * @property string $uf
 * @property string $telefone_fixo
 * @property string $telefone_celular
 *
 * @package App\Models
 * @extends Model
 */
class UsuarioContato extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'usuarios_contatos';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'telefone_fixo',
        'telefone_celular'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um contato pertence um e somente a um usuário
     * TODO: Verificar relacionamento = Relacionamento Um-para-muitos
     *
     * @return BelongsTo
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }


    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do contato do usuário
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do usuário
     *
     * @return int
     */
    public function obterUsuarioId(): int {
        return $this->usuario_id;
    }

    /**
     * Assessor (getter) para obter o CEP de contato do usuário
     * 
     * @return string
     */
    public function obterCEP(): string {
        return $this->cep;
    }

    /**
     * Assessor (getter) para formatar o CEP no padrão XXXXX-XXX
     * 
     * @return string
     */
    public function obterCEPFormatado(): string {
        return preg_replace('/(\d{5})(\d{3})/', '\1-\2', $this->cep);
    }

    /**
     * Assessor (getter) para obter o endereço de contato do usuário
     * 
     * @return string
     */
    public function obterEndereco(): string {
        return $this->endereco;
    }

    /**
     * Assessor (getter) para obter o número do endereço de contato do usuário
     * 
     * @return ?string
     */
    public function obterNumero(): ?string {
        return $this->numero;
    }

    /**
     * Assessor (getter) para obter o complemento do endereço de contato do usuário
     * 
     * @return ?string
     */
    public function obterComplemento(): ?string {
        return $this->complemento;
    }

    /**
     * Assessor (getter) para obter o bairro de contato do usuário
     * 
     * @return string
     */
    public function obterBairro(): string {
        return $this->bairro;
    }

    /**
     * Assessor (getter) para obter a cidade de contato do usuário
     * 
     * @return string
     */
    public function obterCidade(): string {
        return $this->cidade;
    }

    /**
     * Assessor (getter) para obter a UF de contato do usuário
     * 
     * @return UF
     */
    public function obterUF(): UF
    {
        return UF::fromName($this->uf);
    }

    /**
     * Assessor (getter) para obter o telefone fixo de contato do usuário
     * 
     * @return ?string
     */
    public function obterTelefoneFixo(): ?string {
        return $this->telefone_fixo;
    }

    /**
     * Assessor (getter) para formatar o telefone fixo no padrão (XX) XXXX-XXXX
     * 
     * @return ?string
     */
    public function obterTelefoneFixoFormatado(): ?string {
        if (empty($this->telefone_fixo)) {
            return null;
        }
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '(\1) \2-\3', $this->telefone_fixo);
    }

    /**
     * Assessor (getter) para obter o telefone celular de contato do usuário
     * 
     * @return ?string
     */
    public function obterTelefoneCelular(): ?string {
        return $this->telefone_celular;
    }

    /**
     * Assessor (getter) para formatar o telefone celular no padrão (XX) XXXXX-XXXX
     * 
     * @return ?string
     */
    public function obterTelefoneCelularFormatado(): ?string {
        if (empty($this->telefone_celular)) {
            return null;
        }
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '(\1) \2-\3', $this->telefone_celular);
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do contato do usuário
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do usuário
     *
     * @param int $usuarioId
     * @return void
     */
    public function atribuirUsuarioId(int $usuarioId): void {
        $this->usuario_id = $usuarioId;
    }

    /** 
     * Mutador (setter) para atribuir o CEP do usuário
     *
     * @param string $cep
     * @return void
     */
    public function atribuirCEP(string $cep): void {
        $this->cep = $cep;
    }

    /**
     * Mutador (setter) para atribuir o endereço de contato do usuário
     *
     * @param string $endereco
     * @return void
     */
    public function atribuirEndereco(string $endereco): void {
        $this->endereco = $endereco;
    }

    /**
     * Mutador (setter) para atribuir o número do endereço de contato do usuário
     *
     * @param ?string $numero
     * @return void
     */
    public function atribuirNumero(?string $numero): void {
        $this->numero = $numero;
    }

    /**
     * Mutador (setter) para atribuir o complemento do endereço de contato do usuário
     *
     * @param ?string $complemento
     * @return void
     */
    public function atribuirComplemento(?string $complemento): void {
        $this->complemento = $complemento;
    }

    /**
     * Mutador (setter) para atribuir o bairro de contato do usuário
     * 
     * @param string $bairro
     */
    public function atribuirBairro(string $bairro): void {
        $this->bairro = $bairro;
    }

    /**
     * Mutador (setter) para atribuir a cidade de contato do usuário
     *
     * @param string $cidade
     * @return void
     */
    public function atribuirCidade(string $cidade): void {
        $this->cidade = $cidade;
    }

    /**
     * Mutador (setter) para atribuir a UF de contato do usuário
     *
     * @param UF $uf
     * @return void
     */
    public function atribuirUF(UF $uf): void {
        $this->uf = $uf->name;
    }

    /**
     * Mutador (setter) para atribuir o telefone fixo de contato do usuário
     *
     * @param ?string $telefone_fixo
     * @return void
     */
    public function atribuirTelefoneFixo(?string $telefone_fixo): void {
        $this->telefone_fixo = $telefone_fixo;
    }

    /**
     * Mutador (setter) para atribuir o telefone celular de contato do usuário
     *
     * @param ?string $telefone_celular
     * @return void
     */
    public function atribuirTelefoneCelular(?string $telefone_celular): void {
        $this->telefone_celular = $telefone_celular;
    }


    // --- MÉTODOS ADICIONAIS ---

    /**
     * Formata o telefone celular no padrão (XX) XXXXX-XXXX
     * 
     * @return string
     */
    public static function formatarTelefoneCelular(string $telefoneCelular): string {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '(\1) \2-\3', $telefoneCelular);
    }

    /**
     * Formata o telefone fixo no padrão (XX) XXXX-XXXX
     * 
     * @return string
     */
    public static function formatarTelefoneFixo(string $telefoneFixo): string {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '(\1) \2-\3', $telefoneFixo);
    }

    /**
     * Valida o formato do telefone celular
     * 
     * @param string $telefoneCelular
     * @return bool
     */
    public static function validarTelefoneCelular(string $telefoneCelular): bool {
        // Remove caracteres não numéricos
        $telefoneNumeros = preg_replace('/\D/', '', $telefoneCelular);

        // Verifica se é um telefone celular (11 dígitos)
        return preg_match('/^\d{11}$/', $telefoneNumeros) === 1;
    }

    /**
     * Valida o formato do telefone fixo
     * 
     * @param string $telefoneFixo
     * @return bool
     */
    public static function validarTelefoneFixo(string $telefoneFixo): bool {
        // Remove caracteres não numéricos
        $telefoneNumeros = preg_replace('/\D/', '', $telefoneFixo);

        // Verifica se é um telefone fixo (10 dígitos)
        return preg_match('/^\d{10}$/', $telefoneNumeros) === 1;
    }

}