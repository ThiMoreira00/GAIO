<?php

/**
 * @file AlunoEscolaNivel.php
 * @description Enumeração responsável por identificar o nível de ensino da última escola do aluno
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum AlunoEscolaNivel
 *
 * @package App\Models\Enumerations
 */
enum AlunoEscolaNivel: string
{
    case ENSINO_MEDIO = 'Ensino Médio';
    case GRADUACAO = 'Graduação';
    case SUPLETIVO = 'Supletivo';
    case OUTRO = 'Outro';

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