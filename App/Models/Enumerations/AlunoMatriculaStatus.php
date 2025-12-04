<?php

/**
 * @file AlunoMatriculaStatus.php
 * @description Enumeração responsável por identificar o status da matrícula do aluno
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum AlunoMatriculaStatus
 *
 * @package App\Models\Enumerations
 */
enum AlunoMatriculaStatus: string
{
    case CURSANDO = 'Cursando';
    case CONCLUIDO = 'Concluído';
    case EVADIDO = 'Evadido';
    case TRANCADO = 'Trancado';
    case DESISTENTE = 'Desistente';
    case DESLIGADO = 'Desligado';

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