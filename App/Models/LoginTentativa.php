<?php

/**
 * @file LoginTentativa.php
 * @description Modelo responsável pelas tentativas de login de um usuário no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models;

// Importação de classes
use App\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DateTime;

/**
 * Classe LoginTentativa
 *
 * Modelo responsável pelas tentativas de login de um usuário no sistema
 *
 * @property int $id
 * @property ?int $login_id
 * @property string $identificador
 * @property int $tentativas
 * @property ?DateTime $data_bloqueio
 * @property DateTime $data_criado
 * @property DateTime $data_atualizado
 *
 * @package App\Models
 * @extends Model
 */
class LoginTentativa extends Model
{

    // --- ATRIBUTOS ---

    /**
     * Quantidade máxima de tentativas de login (por usuário, por IP)
     * @var int
     */
    const int LIMITE_TENTATIVAS = 5;

    /**
     * Quantidade de minutos em que a conta fica bloqueada
     * @var int
     */
    const int BLOQUEIO_MINUTOS = 15;


    // --- CONFIGURAÇÕES (ELOQUENT ORM) ---

    /**
     * A tabela associada ao model
     * @var string
     */
    protected $table = 'logins_tentativas';

    /**
     * Os atributos que podem ser preenchidos em massa
     * @var array
     */
    protected $fillable = [
        'login_id',
        'identificador',
        'tentativas',
        'data_bloqueio'
    ];

    /**
     * Converte atributos para tipos nativos do PHP
     * @var array
     */
    protected $casts = [
        'data_bloqueio' => 'datetime'
    ];

    /**
     * Atributos que devem ser ocultos para array ou JSON
     * @var array
     */
    protected $hidden = [
        'login_id',
        'identificador'
    ];

    /**
     * Indica se o modelo deve ser atualizado automaticamente
     * @var bool
     */
    public $timestamps = true;


    // --- RELACIONAMENTOS ---

    /**
     * Uma tentativa de login pertencem a um usuário
     * TODO: Relacionamento?
     *
     * @return BelongsTo
     */
    public function usuarioLogin(): BelongsTo
    {
        return $this->belongsTo(UsuarioLogin::class, 'usuario_id');
    }
    

    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da tentativa de login
     *
     * @return int
     */
    public function obterId(): int
    {
        return $this->id;
    }

    /**
     * Assessor (getter) para obter o ID do login da tentativa de login
     *
     * @return ?int
     */
    public function obterLoginId(): ?int
    {
        return $this->login_id;
    }

    /**
     * Assessor (getter) para obter o identificador da tentativa de login
     *
     * @return string
     */
    public function obterIdentificador(): string
    {
        return $this->identificador;
    }

    /**
     * Assessor (getter) para obter o número de tentativas de login
     *
     * @return int
     */
    public function obterTentativas(): int
    {
        return $this->tentativas ?? 0;
    }

    /**
     * Assessor (getter) para obter a data de bloqueio da tentativa de login
     *
     * @return ?DateTime
     */
    public function obterDataBloqueio(): ?DateTime
    {
        return $this->data_bloqueio;
    }


    // --- MUTADORES (SETTERS) ---

    /**
     * Mutador (setter) para atribuir o ID da tentativa de login
     *
     * @param int $id
     * @return void
     */
    public function atribuirId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do login da tentativa de login
     *
     * @param int $loginId
     * @return void
     */
    public function atribuirLoginId(int $loginId): void
    {
        $this->login_id = $loginId;
    }

    /**
     * Mutador (setter) para atribuir o identificador da tentativa de login
     *
     * @param string $identificador
     * @return void
     */
    public function atribuirIdentificador(string $identificador): void
    {
        $this->identificador = $identificador;
    }

    /**
     * Mutador (setter) para atribuir o número de tentativas de login
     *
     * @param int $tentativas
     * @return void
     */
    public function atribuirTentativas(int $tentativas): void
    {
        $this->tentativas = $tentativas;
    }

    /**
     * Mutador (setter) para atribuir a data de bloqueio da tentativa de login
     *
     * @param ?DateTime $data_bloqueio
     * @return void
     */
    public function atribuirDataBloqueio(?DateTime $data_bloqueio): void
    {
        $this->data_bloqueio = $data_bloqueio;
    }


    // --- MÉTODOS AUXILIARES ---

    /**
     * Verifica se a tentativa de login está bloqueada
     *
     * @return bool
     */
    public function estaBloqueada(): bool
    {
        
        // Bloqueio quando:
        // 1. A data de bloqueio é maior que a data atual, independente do número de tentativas
        // 2. O número de tentativas é maior ou igual ao limite definido (módulo LIMITE_TENTATIVAS) + a data de bloqueio é maior que a atual

        return (!empty($this->data_bloqueio) && $this->data_bloqueio > new DateTime()) ||
               ($this->tentativas % self::LIMITE_TENTATIVAS === 0 && $this->data_bloqueio > new DateTime());
    }

    /**
     * Reseta o bloqueio da tentativa de login
     *
     * @return void
     */
    public function resetarBloqueio(): void
    {
        $this->atribuirDataBloqueio(null);
        $this->atribuirTentativas(0);
    }

    /**
     * Incrementa o número de tentativas de login
     *
     * @return void
     */
    public function incrementarTentativas(): void
    {
        $this->atribuirTentativas($this->obterTentativas() + 1);
    }

    /**
     * Bloqueia o acesso na conta por X minutos
     *
     * @param int $quantidadeMinutos
     * @throws DateMalformedStringException
     */
    public function bloquearPorMinutos(int $quantidadeMinutos): void
    {
        $this->atribuirDataBloqueio(new DateTime('+ ' . $quantidadeMinutos . ' minutes'));
    }

    /**
     * Verifica se o usuário está bloqueado com base no identificador
     *
     * @param string $identificador
     * @return bool
     */
    public static function verificarBloqueio(string $identificador): bool
    {
        $tentativa = self::where('identificador', $identificador)
            ->where('data_bloqueio', '>', new DateTime())
            ->first();

        return $tentativa !== null;
    }

    /**
     * Registra uma tentativa de login
     *
     * @param string $identificador
     * @return void
     */
    public static function registrarTentativa(string $identificador): void
    {
        $tentativa = self::firstOrCreate(
            ['identificador' => $identificador],
            ['tentativas' => 0]
        );

        $tentativa->incrementarTentativas();

        if ($tentativa->obterTentativas() >= self::LIMITE_TENTATIVAS) {
            $tentativa->bloquearPorMinutos(self::BLOQUEIO_MINUTOS);
            $tentativa->salvar();
        }

        $tentativa->salvar();
    }
}