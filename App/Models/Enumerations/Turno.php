<?php

/**
 * @file Turno.php
 * @description Enumeração responsável por identificar o turno
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum Turno
 *
 * @package App\Models\Enumerations
 */
enum Turno: string
{
    case MANHA = 'Manhã';
    case TARDE = 'Tarde';
    case NOITE = 'Noite';
    case INTEGRAL = 'Integral';

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