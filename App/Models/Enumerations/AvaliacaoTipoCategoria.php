<?php

/**
 * @file AvaliacaoTipoCategoria.php
 * @description Enumeração responsável por identificar a categoria do tipo de avaliação
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum AvaliacaoTipoCategoria
 *
 * @package App\Models\Enumerations
 */
enum AvaliacaoTipoCategoria: string
{
    case LANCADO = 'Lançado';
    case CALCULADO = 'Calculado';

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
