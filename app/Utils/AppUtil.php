<?php

namespace OGame\Utils;

class AppUtil
{
    /**
     * Format a number with dot as thousands separator and no decimal places (default).)
     *
     * @param $number
     * @return string
     */
    public static function formatNumber($number)
    {
        return number_format($number, 0, ',', '.');
    }

    /**
     * Format a number with comma as thousands separator and no decimal places (used in fleet counts).
     *
     * @param $number
     * @return string
     */
    public static function formatNumberComma($number)
    {
        return number_format($number, 0, '.', ',');
    }
}