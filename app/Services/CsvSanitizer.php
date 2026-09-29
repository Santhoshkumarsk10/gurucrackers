<?php

namespace App\Services;

class CsvSanitizer
{
    /**
     * Characters that can trigger formula or DDE execution in spreadsheet software.
     */
    private const TRIGGER_CHARS = ['=', '+', '-', '@', "\t", "\r", '|', '%'];

    /**
     * Sanitize a single CSV cell value to prevent CSV Formula / DDE Injection.
     * Prepend a single quote if the string begins with any trigger character.
     */
    public static function sanitize(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $trimmed = ltrim($value);
        if ($trimmed !== '' && in_array($trimmed[0], self::TRIGGER_CHARS, true)) {
            return "'" . $value;
        }

        return $value;
    }

    /**
     * Sanitize an entire row array for CSV output.
     */
    public static function sanitizeRow(array $row): array
    {
        return array_map([self::class, 'sanitize'], $row);
    }
}
