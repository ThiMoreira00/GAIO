<?php

/**
 * @file UsuarioNecessidadeEspecifica.php
 * @description Enumeração responsável por identificar as necessidades específicas dos usuários
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioNecessidadeEspecifica
 *
 * @package App\Models\Enumerations
 */
enum UsuarioNecessidadeEspecifica: string
{
    case AUDITIVA = 'Auditiva';
    case VISUAL = 'Visual';
    case MOTORA = 'Motora';
    case MULTIPLA = 'Múltipla';
    case MENTAL = 'Mental';
    case OUTRA = 'Outra';

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