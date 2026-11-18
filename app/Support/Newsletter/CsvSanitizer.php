<?php

namespace App\Support\Newsletter;

final class CsvSanitizer
{
    public static function cell(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value !== '' && preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'".$value;
        }

        return $value;
    }
}
