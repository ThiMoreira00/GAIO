<?php

/**
 * @file UsuarioTokenTipo.php
 * @description Enumeration responsável por identificar o tipo do token do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioTokenTipo
 *
 * @package App\Models\Enumerations
 */
enum UsuarioTokenTipo: string
{
    case VERIFICACAO_EMAIL = 'Verificação de Email';
    case REDEFINICAO_SENHA = 'Redefinição de Senha';

    /**
     * Função para obter por meio da chave (nome)
     *
     * @param string $name
     * @return ?self
     */
    public static function fromName(string $name): ?self
    {
        // Usa a função constant para recuperar o case
        if (defined("self::{$name}")) {
            return constant("self::{$name}");
        }
        return null;
    }
}