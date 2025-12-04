<?php

/**
 * @file DiaSemana.php
 * @description Enumeração responsável por identificar o dia da semana
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum DiaSemana
 *
 * @package App\Models\Enumerations
 */
enum DiaSemana: string
{
    case SEGUNDA_FEIRA = 'Segunda-feira';
    case TERCA_FEIRA = 'Terça-feira';
    case QUARTA_FEIRA = 'Quarta-feira';
    case QUINTA_FEIRA = 'Quinta-feira';
    case SEXTA_FEIRA = 'Sexta-feira';
    case SABADO = 'Sábado';

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
