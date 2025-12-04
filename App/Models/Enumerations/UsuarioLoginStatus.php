<?php

/**
 * @file UsuarioLoginStatus
 * @description Enumeration responsável por identificar o status da conta do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioLoginStatus
 *
 * @package App\Models\Enumerations
 */
enum UsuarioLoginStatus: string
{
    case ATIVO = 'Ativo';
    case INATIVO = 'Inativo';

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