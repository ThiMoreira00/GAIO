<?php

/**
 * @file TurmaStatus.php
 * @description Enumeração para os status de turma
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

namespace App\Models\Enumerations;

/**
 * Enum TurmaStatus
 * 
 * Define os possíveis status de turma
 * 
 * @package App\Models\Enumerations
 */
enum TurmaStatus: string
{
    case PLANEJADA = 'PLANEJADA';
    case OFERTADA = 'OFERTADA';
    case CONFIRMADA = 'CONFIRMADA';
    case ATIVA = 'ATIVA';
    case CONCLUIDA = 'CONCLUIDA';
    case CANCELADA = 'CANCELADA';
    case ARQUIVADA = 'ARQUIVADA';

    /**
     * Obter enum a partir do nome
     * 
     * @param string $name
     * @return self|null
     */
    public static function fromName(string $name): ?self
    {
        if (defined("self::{$name}")) {
            return constant("self::{$name}");
        }
        return null;
    }
}
