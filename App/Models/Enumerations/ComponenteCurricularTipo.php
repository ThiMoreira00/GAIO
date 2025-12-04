<?php

/**
 * @file ComponenteCurricularTipo.php
 * @description Enumeração responsável por identificar o tipo do componente curricular
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum ComponenteCurricularTipo
 *
 * @package App\Models\Enumerations
 */
enum ComponenteCurricularTipo: string
{
    case OBRIGATORIA = 'Obrigatória';
    case OPTATIVA = 'Optativa';
    case ELETIVA = 'Eletiva';

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
