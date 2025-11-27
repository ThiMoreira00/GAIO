<?php

/**
 * @file DataFormatador.php
 * @description Classe-auxiliar para o gerenciamento de formatação com datas no sistema
 * @author Thiago Moreira
 * @copyright Copyright (c) 2025
 */

// Declaração de namespace
namespace App\Helper;

// Importação de classes
use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use IntlDateFormatter;

/**
 * Classe DataFormatador
 *
 * Responsável por formatar datas no sistema
 *
 * @package App\Helper
 */
class DataFormatador
{

    // --- ATRIBUTOS ---

    /**
     * Fuso horário padrão do sistema
     * @var string
     */
    private static string $timezone = 'America/Sao_Paulo';

    /**
     * Local padrão do sistema
     * @var string
     */
    private static string $locale = 'pt_BR';

    /**
     * Função para formatar uma data ou string de data
     *
     * @param DateTime|string $data
     * @param string $pattern
     * @return string
     * @throws DateMalformedStringException
     * @throws DateInvalidTimeZoneException
     */
    public static function formatar(DateTime|string $data, string $pattern = "d MMMM Y"): string
    {
        if (is_string($data)) {
            $data = new DateTime($data, new DateTimeZone(self::$timezone));
        }

        $fmt = new IntlDateFormatter(
            self::$locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            self::$timezone,
            IntlDateFormatter::GREGORIAN,
            $pattern
        );

        return $fmt->format($data);
    }
}