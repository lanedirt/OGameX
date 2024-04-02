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
    public static function formatNumber($number): string
    {
        return number_format($number, 0, ',', '.');
    }

    /**
     * Format a number with comma as thousands separator and no decimal places (used in fleet counts).
     *
     * @param $number
     * @return string
     */
    public static function formatNumberComma($number): string
    {
        return number_format($number, 0, '.', ',');
    }

    /**
     * Helper method to convert building/research time from seconds to human
     * readable format, including weeks and days if applicable.
     */
    public static function formatTimeDuration($seconds): string
    {
        $weeks = floor($seconds / 604800); // 60*60*24*7
        $days = floor(($seconds % 604800) / 86400); // Remaining seconds divided by number of seconds in a day
        $hours = floor(($seconds % 86400) / 3600); // Remaining seconds divided by number of seconds in an hour
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        $formatted_string = '';
        if ($weeks > 0) {
            $formatted_string .= $weeks . 'w ';
        }

        if ($days > 0) {
            $formatted_string .= $days . 'd ';
        }

        if ($hours > 0) {
            $formatted_string .= $hours . 'h ';
        }

        if ($minutes > 0) {
            $formatted_string .= $minutes . 'm ';
        }

        if ($seconds > 0) {
            $formatted_string .= $seconds . 's';
        }

        return trim($formatted_string);
    }
}