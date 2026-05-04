<?php

/**
 * @file UsuarioEstadoCivil
 * @description Enumeration responsável por identificar o estado civil do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioEstadoCivil
 *
 * @package App\Models\Enumerations
 */
enum UsuarioEstadoCivil: string
{
    case SOLTEIRO = 'Solteiro';
    case CASADO = 'Casado';
    case SEPARADO = 'Separado';
    case DIVORCIADO = 'Divorciado';
    case VIUVO = 'Viúvo';

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