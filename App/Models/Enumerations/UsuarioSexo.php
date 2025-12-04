<?php

/**
 * @file UsuarioSexo
 * @description Enumeration responsável por identificar o sexo biológico do usuário
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioSexo
 *
 * @package App\Models\Enumerations
 */
enum UsuarioSexo: string
{
    case MASCULINO = 'Masculino';
    case FEMININO = 'Feminino';
    case OUTRO = 'Outro';

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