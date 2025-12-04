<?php

/**
 * @file InscricaoStatus.php
 * @description Enumeração responsável por identificar o status da inscrição
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum InscricaoStatus
 *
 * @package App\Models\Enumerations
 */
enum InscricaoStatus: string
{
    case SOLICITADA = 'Solicitada';
    case DEFERIDA = 'Deferida';
    case INDEFERIDA = 'Indeferida';
    case ISENTO = 'Isento';
    case CURSANDO = 'Cursando';
    case APROVADO = 'Aprovado';
    case REPROVADO_FALTA = 'Reprovado por Falta';
    case REPROVADO_MEDIA = 'Reprovado por Média';
    case EXCLUIDO = 'Excluído';

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
