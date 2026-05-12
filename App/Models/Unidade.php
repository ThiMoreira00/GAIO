<?php

declare(strict_types=1);

/**
 * @file unidadephp
 *
 * @description Singleton responsável por gerir os dados da unidade
 *
 * @author Thiago Moreira
 *
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace

namespace App\Models;

/**
 * Classe Unidade
 *
 * Singleton responsável por gerir os dados da unidade
 *
 * @property Unidade $instance
 * @property array $data
 * @property string $filePath
 *
 * @package App\Models
 *
 * @final
 */
final class Unidade
{
    // --- PROPRIEDADES ---

    /**
     * Instância única da classe Unidade (Singleton)
     */
    private static ?Unidade $unidade = null;

    /**
     * Dados da unidade carregados do arquivo JSON
     *
     * @var array<string|int|null>
     */
    private array $dados = [];

    /**
     * Caminho do arquivo JSON onde os dados da unidade são armazenados
     *
     * @var string
     */
    private readonly string $caminho_arquivo;

    // --- FUNÇÕES AUXILIARES ---

    /**
     * Construtor privado para impedir a criação de múltiplas instâncias da classe
     *
     * @return void
     */
    private function __construct()
    {
        $this->caminho_arquivo = __DIR__ . '/../../config/unidade.json';
        $this->carregar();
    }

    /**
     * Função para obter a instância única da classe Unidade
     *
     * @return Unidade
     */
    public static function obter(): self
    {
        if (self::$unidade === null) {
            self::$unidade = new self();
        }
        return self::$unidade;
    }

    /**
     * Verifica se a unidade as informações foram configuradas.
     */
    public function estaConfigurada(): bool
    {
        return file_exists($this->caminho_arquivo) && !empty($this->dados);
    }

    // --- ASSESSORES (GETTERS) ---

    /**
     * Assessor (getter) para obter o ID da unidade (e-MEC)
     */
    public function obterEmecId(): int
    {
        return (int) ($this->dados['UNIDADE_EMEC_ID'] ?? 0);
    }

    /**
     * Assessor (getter) para obter o nome completo da unidade
     */
    public function obterNomeCompleto(): string
    {
        return $this->dados['UNIDADE_NOME_COMPLETO'] ?? '';
    }

    /**
     * Assessor (getter) para obter o codinome da unidade
     */
    public function obterCodinome(): string
    {
        return $this->dados['UNIDADE_CODINOME'] ?? '';
    }

    /**
     * Assessor (getter) para obter o e-mail de contato da unidade
     */
    public function obterEmailContato(): string
    {
        return $this->dados['UNIDADE_EMAIL_CONTATO'] ?? '';
    }

    /**
     * Assessor (getter) para obter o telefone de contato da unidade
     */
    public function obterTelefoneContato(): string
    {
        return $this->dados['UNIDADE_TELEFONE_CONTATO'] ?? '';
    }

    /**
     * Assessor (getter) para obter o CEP do endereço da unidade
     */
    public function obterCep(): string
    {
        return $this->dados['UNIDADE_ENDERECO_CEP'] ?? '';
    }

    /**
     * Assessor (getter) para obter o logradouro do endereço da unidade
     */
    public function obterLogradouro(): string
    {
        return $this->dados['UNIDADE_ENDERECO_LOGRADOURO'] ?? '';
    }

    
    /**
     * Assessor (getter) para obter o número do endereço da unidade
    */
    public function obterNumero(): string
    {
        return $this->dados['UNIDADE_ENDERECO_NUMERO'] ?? '';
    }

    /**
     * Assessor (getter) para obter o complemento do endereço da unidade
     */
    public function obterComplemento(): ?string
    {
        return $this->dados['UNIDADE_ENDERECO_COMPLEMENTO'] ?? null;
    }

    /**
     * Assessor (getter) para obter o bairro do endereço da unidade
     */
    public function obterBairro(): string
    {
        return $this->dados['UNIDADE_ENDERECO_BAIRRO'] ?? '';
    }

    /**
     * Assessor (getter) para obter a cidade do endereço da unidade
     */
    public function obterCidade(): string
    {
        return $this->dados['UNIDADE_ENDERECO_CIDADE'] ?? '';
    }

    /**
     * Assessor (getter) para obter o estado do endereço da unidade
     */
    public function obterEstado(): string
    {
        return $this->dados['UNIDADE_ENDERECO_ESTADO'] ?? '';
    }

    /**
     * Assessor (getter) para obter o ID do diretor da unidade
     */
    public function obterDiretorId(): int
    {
        return intval($this->dados['UNIDADE_CORPO_ADMINISTRATIVO_DIRETOR_ID'] ?? 0);
    }

    /**
     * Assessor (getter) para obter o ID do vice-diretor da unidade
     */
    public function obterViceDiretorId(): int
    {
        return intval($this->dados['UNIDADE_CORPO_ADMINISTRATIVO_VICE_DIRETOR_ID'] ?? 0);
    }

    /**
     * Assessor (getter) para obter o ID do administrador da unidade
     */
    public function obterAdministradorId(): int
    {
        return intval($this->dados['UNIDADE_CORPO_ADMINISTRATIVO_ADMINISTRADOR_ID'] ?? 0);
    }

    /**
     * Assessor (getter) para obter o ato autorizativo da unidade
     *
     * @return array{parecer: string, data_publicacao: string|null, anos_vigencia: int|null}
     */
    public function obterAtoAutorizativo(): array
    {
        return array_merge([
            'parecer'         => '',
            'data_publicacao' => null,
            'anos_vigencia'   => null,
        ], $this->dados['UNIDADE_ATO_AUTORIZATIVO'] ?? []);
    }

    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------

    /**
     * Mutador (setter) para atribuir o ID da unidade (e-MEC).
     */
    public function atribuirEmecId(int $id): void
    {
        $this->dados['UNIDADE_EMEC_ID'] = $id;
    }

    /**
     * Mutador (setter) para atribuir o nome completo da unidade
     */
    public function atribuirNomeCompleto(string $nome_completo): void
    {
        $this->dados['UNIDADE_NOME_COMPLETO'] = $nome_completo;
    }

    /**
     * Mutador (setter) para atribuir o codinome da unidade
     */
    public function atribuirCodinome(string $codinome): void
    {
        $this->dados['UNIDADE_CODINOME'] = $codinome;
    }

    /**
     * Mutador (setter) para atribuir o e-mail de contato da unidade
     */
    public function atribuirEmailContato(string $email): void
    {
        $this->dados['UNIDADE_EMAIL_CONTATO'] = $email;
    }

    /**
     * Mutador (setter) para atribuir o telefone de contato da unidade
     */
    public function atribuirTelefoneContato(?string $telefone): void
    {
        $this->dados['UNIDADE_TELEFONE_CONTATO'] = $telefone;
    }

    /**
     * Mutador (setter) para atribuir o CEP do endereço da unidade
     */
    public function atribuirCep(string $cep): void
    {
        $this->dados['UNIDADE_ENDERECO_CEP'] = $cep;
    }

    /**
     * Mutador (setter) para atribuir o número do endereço da unidade
     */
    public function atribuirNumero(string $numero): void
    {
        $this->dados['UNIDADE_ENDERECO_NUMERO'] = $numero;
    }

    /**
     * Mutador (setter) para atribuir o complemento do endereço da unidade
     */
    public function atribuirComplemento(?string $complemento): void
    {
        $this->dados['UNIDADE_ENDERECO_COMPLEMENTO'] = $complemento;
    }

    /**
     * Mutador (setter) para atribuir o ato autorizativo da unidade
     *
     * @param array $ato
     */
    public function atribuirAtoAutorizativo(array $ato): void
    {
        $this->dados['UNIDADE_ATO_AUTORIZATIVO'] = $ato;
    }

    /**
     * Mutador (setter) para atribuir o ID do diretor da unidade
     */
    public function atribuirDiretorId(int $id): void
    {
        $this->dados['UNIDADE_DIRETOR_ID'] = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do vice-diretor da unidade
     */
    public function atribuirViceDiretorId(int $id): void
    {
        $this->dados['UNIDADE_VICE_DIRETOR_ID'] = $id;
    }

    /**
     * Mutador (setter) para atribuir o ID do administrador da unidade
     */
    public function atribuirAdministradorId(int $id): void
    {
        $this->dados['UNIDADE_ADMINISTRADOR_ID'] = $id;
    }

    /**
     * Persiste os dados da unidade no arquivo JSON (método público)
     */
    public function persistir(): void
    {
        $this->salvar();
    }

    /**
     * Função para carregar os dados da unidade do arquivo JSON
     */
    private function carregar(): void
    {
        if (file_exists($this->caminho_arquivo)) {
            $json = file_get_contents($this->caminho_arquivo);
            $this->dados = json_decode($json, true) ?? [];
        }
    }

    /**
     * Função para salvar os dados da unidade no arquivo JSON
     */
    private function salvar(): void
    {
        file_put_contents($this->caminho_arquivo, json_encode($this->dados, JSON_PRETTY_PRINT));
    }
}
