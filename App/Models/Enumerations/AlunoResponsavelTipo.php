<?php

/**
 * @file AlunoResponsavelTipo.php
 * @description Enumeração responsável por identificar o tipo de responsável pelo aluno
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum AlunoResponsavelTipo
 *
 * @package App\Models\Enumerations
 */
enum AlunoResponsavelTipo: string
{
    case MAE = 'Mãe';
    case PAI = 'Pai';
    case RESPONSAVEL_LEGAL = 'Responsável legal';

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