<?php

/**
 * @file UsuarioPronome
 * @description Enumeration responsável por identificar o pronome do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioPronome
 *
 * @package App\Models\Enumerations
 */
enum UsuarioPronome: string
{
    case ELE_DELE = 'Ele/dele';
    case ELA_DELA = 'Ela/dela';
    case ELU_DELU = 'Elu/delu';

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