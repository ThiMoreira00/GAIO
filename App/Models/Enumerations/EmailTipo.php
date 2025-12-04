<?php

/**
 * @file EmailTipo.php
 * @description Enumeração responsável pelos tipos de email no sistema.
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Models\Enumerations;

// Importação de classes


enum EmailTipo: string
{
    case REDEFINICAO_SENHA = 'login_senha_redefinicao';
    case BLOQUEIO_CONTA = 'login_conta_bloqueio';
}