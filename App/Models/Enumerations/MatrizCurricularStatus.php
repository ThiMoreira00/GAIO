<?php

/**
 * @file MatrizCurricularStatus.php
 * @description Enumeração responsável por identificar o status da matriz curricular
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum MatrizCurricularStatus
 *
 * @package App\Models\Enumerations
 */
enum MatrizCurricularStatus: string
{

    case VIGENTE = 'Vigente';
    case ARQUIVADO = 'Arquivado';

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