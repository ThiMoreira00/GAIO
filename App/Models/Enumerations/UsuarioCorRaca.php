<?php

/**
 * @file UsuarioCorRaca
 * @description Enumeration responsável por identificar a cor/raça do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioCorRaca
 *
 * @package App\Models\Enumerations
 */
enum UsuarioCorRaca: string
{
    case AMARELA = 'Amarela';
    case BRANCA = 'Branca';
    case PARDA = 'Parda';
    case PRETA = 'Preta';
    case INDIGENA = 'Indígena';
    case NAO_DECLARADA = 'Não declarada';

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