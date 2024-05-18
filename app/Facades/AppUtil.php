<?php

namespace OGame\Facades;

use Illuminate\Support\Facades\Facade;

class AppUtil extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'appUtil';
    }

    /**
     * Format a number without rounding it down. Show the full number.
     *
     * @param int|float $number
     * @return string
     */
    public static function formatNumber(int|float $number): string
    {
        // If number is less than 1,000, just return it with no formatting
        return number_format($number, 0, ',', ',');
    }

    /**
     * Format a number with K for thousands and Mn for millions, with one decimal place for thousands
     * when necessary, and no decimal places for millions.
     *
     * @param int|float $number
     * @return string
     */
    public static function formatNumberShort(int|float $number): string
    {
        if ($number >= 1000000) {
            // If number is 1,000,000 or higher, format as millions (Mn)
            return number_format($number / 1000000, 0) . 'Mn';
        } elseif ($number > 10000) {
            // If number is greater than 10,000 but less than 1,000,000, round down and format as thousands (K) with no decimal places
            return number_format(floor($number / 1000), 0) . 'K';
        } elseif ($number > 1000) {
            // If number is greater than 1,000 but less than or equal to 10,000, format as thousands (K) with up to 1 decimal place
            // Avoid rounding up by using floor function after multiplying by 10 and then dividing by 10
            $thousands = floor(($number / 1000) * 10) / 10;
            $decimalPlaces = ($number % 1000) == 0 ? 0 : 1; // Use 1 decimal place unless it's an exact thousand
            return number_format($thousands, $decimalPlaces, ',', '') . 'K';
        } else {
            // If number is less than 1,000, just return it with no formatting
            return number_format($number, 0, ',', ',');
        }
    }

    /**
     * Format a number above 1 million as "1.000Mn" with three decimal places, and
     * format numbers between 1,000 and 1 million with commas as thousands separator.
     *
     * @param int|float $number
     * @return string
     */
    public static function formatNumberLong(int|float $number): string
    {
        if ($number >= 1000000) {
            // If number is 1,000,000 or higher, format as "1.000Mn" with three decimal places

            // Divide by 1,000,000 and format with up to 3 decimal places
            $formattedNumber = number_format($number / 1000000, 3, '.', '');

            // If the number is a whole million, no decimals are needed
            if (floor($number / 1000000) == $number / 1000000) {
                return floor($number / 1000000) . 'Mn';
            }

            // Remove trailing zeros and the decimal point if it becomes unnecessary
            $formattedNumber = rtrim($formattedNumber, '0');  // Remove trailing zeros
            $formattedNumber = rtrim($formattedNumber, '.');  // Remove trailing decimal point if no decimal numbers are left

            return $formattedNumber . 'Mn';
        } elseif ($number >= 1000) {
            // If number is 1,000 or higher but less than 1,000,000, format with commas as thousands separator
            return number_format($number, 0, '.', ',');
        } else {
            // If number is less than 1,000, just return it with no formatting
            return number_format($number, 0, '.', ',');
        }
    }

    /**
     * Helper method to convert building/research time from seconds to human
     * readable format, including weeks and days if applicable.
     *
     * @param int|float $seconds
     * @return string
     */
    public static function formatTimeDuration(int|float $seconds): string
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

        if (empty($formatted_string)) {
            $formatted_string .= $seconds . 's';
        }

        return trim($formatted_string);
    }

    /**
     * Helper method to convert building/research time from seconds to <time datetime=""> format.
     *
     * @param int|float $seconds
     * @return string
     */
    public static function formatDateTimeDuration(int|float $seconds): string
    {
        // TODO: add unittest for this and check what is the expected output for hours/days/weeks.
        $weeks = floor($seconds / 604800); // 60*60*24*7
        $days = floor(($seconds % 604800) / 86400); // Remaining seconds divided by number of seconds in a day
        $hours = floor(($seconds % 86400) / 3600); // Remaining seconds divided by number of seconds in an hour
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        $formatted_string = '';
        if ($weeks > 0) {
            $formatted_string .= $weeks . 'W';
        }

        if ($days > 0) {
            $formatted_string .= $days . 'D';
        }

        if ($hours > 0) {
            $formatted_string .= $hours . 'H';
        }

        if ($minutes > 0) {
            $formatted_string .= $minutes . 'M';
        }

        if ($seconds > 0) {
            $formatted_string .= $seconds . 'S';
        }

        return 'PT' . trim($formatted_string);
    }
}
