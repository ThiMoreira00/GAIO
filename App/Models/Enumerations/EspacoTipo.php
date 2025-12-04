<?php

/**
 * @file EspacoTipo.php
 * @description Enumeração responsável por identificar o tipo do espaço
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum EspacoTipo
 *
 * @package App\Models\Enumerations
 */
enum EspacoTipo: string
{
    case SALA_AULA = 'Sala de Aula';
    case LABORATORIO = 'Laboratório';
    case AUDITORIO = 'Auditório';
    case BIBLIOTECA = 'Biblioteca';
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