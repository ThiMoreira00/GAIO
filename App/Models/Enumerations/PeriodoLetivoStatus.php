<?php

/**
 * @file PeriodoLetivoStatus.php
 * @description Enumeration responsável por identificar o status do período letivo
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum PeriodoLetivoStatus
 * 
 * @package App\Models\Enumerations
 */
enum PeriodoLetivoStatus: string
{
    case ATIVO = 'Ativo';
    case INATIVO = 'Inativo';
    case PROGRAMADO = 'Programado';
    case CONCLUIDO = 'Concluído';

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