<?php

/**
 * @file UsuarioContatoUF
 * @description Enumeration responsável por identificar o UF em que o usuário reside
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

/**
 * Enum UsuarioContatoUF
 *
 * @package App\Models\Enumerations
 */
enum UF: string
{
    case AC = 'AC';
    case AL = 'AL';
    case AP = 'AP';
    case AM = 'AM';
    case BA = 'BA';
    case CE = 'CE';
    case DF = 'DF';
    case ES = 'ES';
    case GO = 'GO';
    case MA = 'MA';
    case MG = 'MG';
    case MS = 'MS';
    case MT = 'MT';
    case PA = 'PA';
    case PB = 'PB';
    case PE = 'PE';
    case PI = 'PI';
    case PR = 'PR';
    case RJ = 'RJ';
    case RN = 'RN';
    case RO = 'RO';
    case RR = 'RR';
    case RS = 'RS';
    case SC = 'SC';
    case SE = 'SE';
    case SP = 'SP';
    case TO = 'TO';

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