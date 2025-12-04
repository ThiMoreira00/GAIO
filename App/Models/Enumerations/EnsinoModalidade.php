<?php

/**
 * @file EnsinoModalidade.php
 * @description Enumeração responsável por identificar a modalidade de ensino
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum EnsinoModalidade
 *
 * @package App\Models\Enumerations
 */
enum EnsinoModalidade: string
{
    case PRESENCIAL = 'Presencial';
    case REMOTA = 'Remota';
    case HIBRIDA = 'Híbrida';

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
