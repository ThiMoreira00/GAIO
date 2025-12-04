<?php

/**
 * @file ProfessorTitulo.php
 * @description Enumeração para os títulos de professores
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

namespace App\Models\Enumerations;

/**
 * Enum ProfessorTitulo
 * 
 * Define os possíveis títulos de professores
 * 
 * @package App\Models\Enumerations
 */
enum ProfessorTitulo: string
{
    case MESTRE = 'MESTRE';
    case DOUTOR = 'DOUTOR';
    case ESPECIALISTA = 'ESPECIALISTA';

    /**
     * Obter enum a partir do nome
     * 
     * @param string $name
     * @return self|null
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
