<?php

/**
 * @file UsuarioContatoUF
 * @description Enumeration responsável por identificar o UF
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
    case AC = 'Acre';
    case AL = 'Alagoas';
    case AP = 'Amapá';
    case AM = 'Amazonas';
    case BA = 'Bahia';
    case CE = 'Ceará';
    case DF = 'Distrito Federal';
    case ES = 'Espirito Santo';
    case GO = 'Goiás';
    case MA = 'Maranhão';
    case MG = 'Minas Gerais';
    case MS = 'Mato Grosso do Sul';
    case MT = 'Mato Grosso';
    case PA = 'Pará';
    case PB = 'Paraíba';
    case PE = 'Pernambuco';
    case PI = 'Piauí';
    case PR = 'Paraná';
    case RJ = 'Rio de Janeiro';
    case RN = 'Rio Grande do Norte';
    case RO = 'Rondônia';
    case RR = 'Roraima';
    case RS = 'Rio Grande do Sul';
    case SC = 'Santa Catarina';
    case SE = 'Sergipe';
    case SP = 'São Paulo';
    case TO = 'Tocantins';
    

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