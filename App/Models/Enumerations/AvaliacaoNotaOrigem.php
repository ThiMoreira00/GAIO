<?php

/**
 * @file AvaliacaoNotaOrigem.php
 * @description Enumeração responsável por identificar a origem da nota de avaliação
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum AvaliacaoNotaOrigem
 *
 * @package App\Models\Enumerations
 */
enum AvaliacaoNotaOrigem: string
{
    case ATIVIDADE = 'Atividade';
    case MANUAL = 'Manual';

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
