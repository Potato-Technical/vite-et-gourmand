<?php

declare(strict_types=1);

if (!function_exists('e')) {
    /**
     * Échappe une valeur avant son affichage dans du HTML.
     */
    function e(string|int|float|bool|null $value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}
