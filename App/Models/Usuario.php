<?php

/**
 * @file Usuario.php
 * @description Modelo responsável pelos usuários do sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use App\Models\Enumerations\UsuarioCorRaca;
use App\Models\Enumerations\UsuarioEstadoCivil;
use App\Models\Enumerations\UsuarioPronome;
use App\Models\Enumerations\UsuarioSexo;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * Classe Usuario
 *
 * Modelo responsável pelos usuários do sistema
 *
 * @property int $id
 * @property string $nome_civil
 * @property ?string $nome_social
 * @property ?string $caminho_foto
 * @property DateTime $data_nascimento
 * @property string $sexo
 * @property string $pronome
 * @property string $cor_raca
 * @property string $estado_civil
 * @property string $cpf
 * @property string $rg
 * @property string $nacionalidade
 * @property string $naturalidade
 * @property string $email_pessoal
 * @property ?string $email_institucional
 *
 * @package App\Models
 * @extends Model
 */

class Usuario extends Model
{

    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'nome_civil',
        'nome_social',
        'caminho_foto',
        'sexo',
        'cor_raca',
        'estado_civil',
        'cpf',
        'rg',
        'nacionalidade',
        'naturalidade',
        'email_pessoal',
        'email_institucional'
    ];

    /**
     * Atributos que devem ser ocultos para array ou JSON
     * @var array
     */
    protected $hidden = [
        'sexo',
        'cor_raca',
        'estado_civil',
        'cpf',
        'rg',
        'nacionalidade',
        'naturalidade'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos específicos
     * @var array
     */
    protected $casts = [
        'nome_civil' => 'string',
        'nome_social' => 'string',
        'caminho_foto' => 'string',
        'data_nascimento' => 'datetime',
        'sexo' => 'string',
        'cor_raca' => 'string',
        'estado_civil' => 'string',
        'cpf' => 'string',
        'rg' => 'string',
        'nacionalidade' => 'string',
        'naturalidade' => 'string',
        'email_pessoal' => 'string',
        'email_institucional' => 'string'
    ];


    // --- RELACIONAMENTOS ---

    /**
     * Um usuário tem um e apenas um login
     * TODO: Verificar relacionamento = Relacionamento Um-para-um
     *
     * @return HasOne
     */
    public function login(): HasOne {
        return $this->hasOne(UsuarioLogin::class, 'usuario_id');
    }

    /**
     * Um usuário tem um e apenas um conjunto de dados pessoais
     * TODO: Relacionamento?
     *
     * @return HasOne
     */
    public function contato(): HasOne {
        return $this->hasOne(UsuarioContato::class, 'usuario_id');
    }

    /**
     * Um usuário pode ter um ou muitos tokens
     * TODO: Relacionamento?
     *
     * @return HasMany
     */
    public function tokens(): HasMany {
        return $this->hasMany(UsuarioToken::class, 'usuario_id');
    }

    /**
     * Um usuário pode ter zero ou muitas notificações
     * TODO: Relacionamento?
     *
     * @return HasMany
     */
    public function notificacoes(): HasMany
    {
        return $this->hasMany(Notificacao::class, 'usuario_id');
    }

    /**
     * Um usuário pode ter zero ou muitas tentativas de login.
     * Relacionamento Um-para-muitos.
     * 
     * @return HasMany
     */
    public function loginTentativas(): HasMany
    {
        return $this->hasMany(LoginTentativa::class, 'usuario_id');
    }

    /**
     * Um usuário pode ter um ou muitos grupos.
     * Relacionamento Muitos-para-muitos.
     *
     * @return BelongsToMany
     */
    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'usuarios_grupos', 'usuario_id', 'grupo_id');
    }

    /**
     * Um usuário é associado a um aluno.
     * Relacionamento Um-para-um.
     * 
     * @return HasOne
     */
    public function aluno(): HasOne
    {
        return $this->hasOne(Aluno::class, 'usuario_id');
    }

    // --- SCOPES (FILTROS) ---

    /**
     * Filtro de usuários com base no ID
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
     * Filtro de usuários com base no nome (civil ou social)
     * 
     * @param $query
     * @param string $nome
     * @return Builder
     */
    public function scopeNome($query, string $nome): Builder
    {
        return $query->where('nome_civil', 'LIKE', "%$nome%")
            ->orWhere('nome_social', 'LIKE', "%$nome%");
    }

    /**
     * Filtro de usuários com base no CPF
     *
     * @param $query
     * @param string $cpf
     * @return Builder
     */
    public function scopeCpf($query, string $cpf): Builder
    {
        return $query->where('cpf', $cpf);
    }

    /**
     * Filtro de usuários com base no RG
     * 
     * @param $query
     * @param string $rg
     * @return Builder
     */
    public function scopeRG($query, string $rg): Builder
    {
        return $query->where('rg', $rg);
    }

    /**
     * Filtro de usuários com base no e-mail (institucional e pessoal)
     *
     * @param $query
     * @param string $email
     * @return Builder
     */
    public function scopeEmail($query, string $email): Builder
    {
        return $query->where('email_pessoal', $email)->orWhere('email_institucional', $email);
    }
    

    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID do usuário
     *
     * @return int
     */
    public function obterId(): int {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o nome civil do usuário
     *
     * @return string
     */
    public function obterNomeCivil(): string {
        return $this->nome_civil;
    }

    /**
     * Assessor (getter) para obter o nome social do usuário
     *
     * @return ?string
     */
    public function obterNomeSocial(): ?string {
        return $this->nome_social;
    }

    /**
     * Assessor (getter) para obter o nome reduzido do usuário
     * Retorna o primeiro e último nome, ou o nome social se disponível
     *
     * @return string
     */
    public function obterNomeReduzido(): string {

        // Se houver nome social, utiliza ele; caso contrário, usa o nome completo
        $nome = $this->nome_social ?: $this->nome_civil;

        $nomes = explode(' ', trim($nome));

        // Se tiver apenas um nome, retorna ele mesmo
        if (count($nomes) <= 1) {
            return $nome;
        }

        // Retorna o primeiro e último nome
        return $nomes[0] . ' ' . end($nomes);
    }

    /**
     * Assessor (getter) para obter o caminho da foto do usuário
     *
     * @return ?string
     */
    public function obterCaminhoFoto(): ?string {
        return $this->caminho_foto;
    }

    /**
     * Assessor (getter) para obter a data de nascimento do usuário
     *
     * @return DateTime
     */
    public function obterDataNascimento(): DateTime {
        return $this->data_nascimento;
    }

    /**
     * Assessor (getter) para obter a data de nascimento do usuário no formato "dd/mm/aaaa"
     *
     * @return string
     */
    public function obterDataNascimentoFormatada(): string {
        return date('d/m/Y', strtotime(strval($this->data_nascimento)));
    }

    /**
     * Assessor (getter) para obter o sexo do usuário
     *
     * @return UsuarioSexo|null
     */
    public function obterSexo(): ?UsuarioSexo {
        return $this->sexo ? UsuarioSexo::fromName($this->sexo) : null;
    }

    /**
     * Assessor (getter) para obter a cor da raça do usuário
     *
     * @return UsuarioCorRaca|null
     */
    public function obterCorRaca(): ?UsuarioCorRaca {
        return $this->cor_raca ? UsuarioCorRaca::fromName($this->cor_raca) : null;
    }

    /**
     * Assessor (getter) para obter o estado civil do usuário
     *
     * @return UsuarioEstadoCivil|null
     */
    public function obterEstadoCivil(): ?UsuarioEstadoCivil {
        return $this->estado_civil ? UsuarioEstadoCivil::fromName($this->estado_civil) : null;
    }

    /**
     * Assessor (getter) para obter o CPF do usuário
     *
     * @return string
     */
    public function obterCPF(): string {
        return $this->cpf;
    }

    /**
     * Assessor (getter) para obter o CPF do usuário
     *
     * @return string
     */
    public function obterCPFFormatado(): string {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    /**
     * Assessor (getter) para obter o RG do usuário
     *
     * @return string
     */
    public function obterRG(): string {
        return $this->rg;
    }

    /**
     * Assessor (getter) para obter o RG do usuário
     *
     * @return string
     */
    public function obterRGFormatado(): string {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{1})/', '$1.$2.$3-$4', $this->rg);
    }

    /**
     * Assessor (getter) para obter a nacionalidade do usuário
     *
     * @return string
     */
    public function obterNacionalidade(): string {
        return $this->nacionalidade;
    }

    /**
     * Assessor (getter) para obter a naturalidade do usuário
     *
     * @return string
     */
    public function obterNaturalidade(): string {
        return $this->naturalidade;
    }

    /**
     * Assessor (getter) para obter o email pessoal do usuário
     *
     * @return string
     */
    public function obterEmailPessoal(): string {
        return $this->email_pessoal;
    }

    /**
     * Assessor (getter) para obter o email institucional do usuário
     *
     * @return ?string
     */
    public function obterEmailInstitucional(): ?string {
        return $this->email_institucional;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID do usuário
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome civil do usuário
     *
     * @param string $nomeCivil
     * @return void
     */
    public function atribuirNomeCivil(string $nomeCivil): void {
        $this->nome_civil = $nomeCivil;
    }

    /**
     * Mutador (setter) para atribuir o nome social do usuário
     *
     * @param ?string $nomeSocial
     * @return void
     */
    public function atribuirNomeSocial(?string $nomeSocial): void {
        $this->nome_social = $nomeSocial;
    }

    /**
     * Mutador (setter) para atribuir o caminho da foto do usuário
     *
     * @param ?string $caminhoFoto
     * @return void
     */
    public function atribuirCaminhoFoto(?string $caminhoFoto): void {
        $this->caminho_foto = $caminhoFoto;
    }

    /**
     * Mutador (setter) para atribuir a data de nascimento do usuário
     *
     * @param DateTime $dataNascimento
     * @return void
     */
    public function atribuirDataNascimento(DateTime $dataNascimento): void {
        $this->data_nascimento = $dataNascimento;
    }

    /**
     * Mutador (setter) para atribuir o sexo do usuário
     *
     * @param UsuarioSexo $sexo
     * @return void
     */
    public function atribuirSexo(UsuarioSexo $sexo): void {
        $this->sexo = $sexo->value;
    }

    /**
     * Mutador (setter) para atribuir a cor da raça do usuário
     *
     * @param UsuarioCorRaca $corRaca
     * @return void
     */
    public function atribuirCorRaca(UsuarioCorRaca $corRaca): void {
        $this->cor_raca = $corRaca->value;
    }

    /**
     * Mutador (setter) para atribuir o estado civil do usuário
     *
     * @param UsuarioEstadoCivil $estadoCivil
     * @return void
     */
    public function atribuirEstadoCivil(UsuarioEstadoCivil $estadoCivil): void {
        $this->estado_civil = $estadoCivil->value;
    }

    /**
     * Mutador (setter) para atribuir o CPF do usuário
     *
     * @param string $cpf
     * @return void
     */
    public function atribuirCPF(string $cpf): void {
        $this->cpf = $cpf;
    }

    /**
     * Mutador (setter) para atribuir o RG do usuário
     *
     * @param string $rg
     * @return void
     */
    public function atribuirRG(string $rg): void {
        $this->rg = $rg;
    }

    /**
     * Mutador (setter) para atribuir a nacionalidade do usuário
     *
     * @param string $nacionalidade
     * @return void
     */
    public function atribuirNacionalidade(string $nacionalidade): void {
        $this->nacionalidade = $nacionalidade;
    }

    /**
     * Mutador (setter) para atribuir a naturalidade do usuário
     *
     * @param string $naturalidade
     * @return void
     */
    public function atribuirNaturalidade(string $naturalidade): void {
        $this->naturalidade = $naturalidade;
    }

    /**
     * Mutador (setter) para atribuir o email pessoal do usuário
     *
     * @param string $email_pessoal
     * @return void
     */
    public function atribuirEmailPessoal(string $email_pessoal): void {
        $this->email_pessoal = $email_pessoal;
    }

    /**
     * Mutador (setter) para atribuir o email institucional do usuário
     *
     * @param ?string $emailInstitucional
     * @return void
     */
    public function atribuirEmailInstitucional(?string $emailInstitucional): void {
        $this->email_institucional = $emailInstitucional;
    }


    // --- MÉTODOS DE BUSCA ---

    /**
     * Função para buscar um usuário pelo CPF
     *
     * @param string $cpf
     * @return ?Usuario
     */
    public static function buscarPorCPF(string $cpf): ?Usuario
    {
        return self::cpf($cpf)->first() ?? null;
    }

    /**
     * Função para buscar um usuário pelo RG
     *
     * @param string $rg
     * @return ?Usuario
     */
    public static function buscarPorRG(string $rg): ?Usuario
    {
        return self::rg($rg)->first() ?? null;
    }

    /**
     * Função para buscar um usuário pelo e-mail (institucional e pessoal)
     *
     * @param string $email
     * @return ?Usuario
     */
    public static function buscarPorEmail(string $email): ?Usuario
    {
        return self::email($email)->first() ?? null;
    }

    /**
     * Função para buscar usuários com base no nome (parcial)
     * 
     * @param string $nome
     * @return Collection<Usuario>
     */
    public static function buscarPorNome(string $nome): Collection
    {
        return self::nome($nome)->get();
    }

    /**
     * Função para buscar um usuário pelo e-mail pessoal
     *
     * @param string $emailPessoal
     * @return ?Usuario
     */
    public static function buscarPorEmailPessoal(string $emailPessoal): ?Usuario
    {
        return self::where('email_pessoal', $emailPessoal)->first();
    }

    /**
     * Função para buscar um usuário pelo e-mail institucional
     *
     * @param string $emailInstitucional
     * @return ?Usuario
     */
    public static function buscarPorEmailInstitucional(string $emailInstitucional): ?Usuario
    {
        return self::where('email_institucional', $emailInstitucional)->first();
    }


    // --- MÉTODOS AUXILIARES ---

    public function obterFotoPerfil(): string {
        return empty($this->obterCaminhoFoto()) ? sprintf('https://ui-avatars.com/api/?name=%s&color=7F9CF5&background=EBF4FF', urlencode(str_replace(' ', '+', $this->obterNomeReduzido()))) : sprintf(obterURL('/' . $_ENV['SISTEMA_IMAGENS_PERFIL'] . $this->obterCaminhoFoto()), $this->obterCaminhoFoto());
    }

    public static function validarCPF(string $cpf): bool {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\\d)\\1{10}$/', $cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Função para validar um RG
     * 
     * @param string $rg
     * @return bool
     */
    public static function validarRG(string $rg): bool {
        // Remove caracteres não numéricos
        $rg = preg_replace('/[^0-9]/', '', $rg);

        // Verifica se o RG tem entre 7 e 9 dígitos
        $length = strlen($rg);
        if ($length < 7 || $length > 9) {
            return false;
        }

        return true;
    }

    /**
     * Função para verificar se o usuário é um aluno
     * 
     * @return bool
     */
    public function verificarAluno(): bool {
        return $this->grupos->contains(function($grupo) {
            return strtoupper($grupo->obterNome()) === 'ALUNO';
        });
    }

    /**
     * Função para verificar se o usuário é um professor
     * 
     * @return bool
     */
    public function verificarProfessor(): bool {
        return $this->grupos->contains(function($grupo) {
            return strtoupper($grupo->obterNome()) === 'PROFESSOR';
        });
    }

    /**
     * Função para verificar se o usuário é um administrador
     * 
     * @return bool
     */
    public function verificarAdministrador(): bool {
        return $this->grupos->contains(function($grupo) {
            return strtoupper($grupo->obterNome()) === 'ADMINISTRADOR';
        });
    }
}