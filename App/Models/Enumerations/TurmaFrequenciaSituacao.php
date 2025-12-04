<?php

/**
 * @file TurmaFrequenciaSituacao.php
 * @description Enumeração responsável por identificar a situação da frequência na turma
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum TurmaFrequenciaSituacao
 *
 * @package App\Models\Enumerations
 */
enum TurmaFrequenciaSituacao: string
{
    case PRESENTE = 'Presente';
    case FALTA = 'Falta';
    case FALTA_JUSTIFICADA = 'Falta Justificada';

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
