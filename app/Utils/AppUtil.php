<?php

namespace OGame\Utils;

class AppUtil
{
    /**
     * Format a number.
     *
     * @param $number
     * @return string
     */
    public static function formatNumber($number)
    {
        return number_format($number, 0, ',', '.');
    }
}