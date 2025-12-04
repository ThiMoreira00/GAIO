<?php

/**
 * @file CursoStatus
 * @description Enumeração responsável por identificar o status do curso
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum CursoStatus
 *
 * @package App\Models\Enumerations
 */
enum CursoStatus: string
{
    case ATIVO = 'Ativo';
    case INATIVO = 'Inativo';
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