<?php
namespace App\Utils;

class DecimalTruncator
{
    /**
     * Truncate a number to a fixed number of decimal places WITHOUT rounding
     *
     * @param mixed $number (float|string|int)
     * @param int $precision
     * @return string
     */
    public static function truncate($number, $precision)
    {
        $precision = (int)$precision;

        // Convert to string to avoid float precision issues
        $numberStr = (string)$number;

        // Handle negative numbers
        $isNegative = false;
        if (strpos($numberStr, '-') === 0) {
            $isNegative = true;
            $numberStr = substr($numberStr, 1);
        }

        // Check if decimal exists
        if (strpos($numberStr, '.') !== false) {
            $parts = explode('.', $numberStr);
            $intPart = $parts[0];
            $decimalPart = isset($parts[1]) ? $parts[1] : '';
        } else {
            // No decimal, return as-is
            return ($isNegative ? '-' : '') . $numberStr;
        }

        // Truncate decimal part
        $truncatedDecimal = substr($decimalPart, 0, $precision);

        // Pad with zeros if needed
        while (strlen($truncatedDecimal) < $precision) {
            $truncatedDecimal .= '0';
        }

        // Build result
        if ($precision > 0) {
            $result = $intPart . '.' . $truncatedDecimal;
        } else {
            $result = $intPart;
        }

        return ($isNegative ? '-' : '') . $result;
    }
}
