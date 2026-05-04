<?php

if (!function_exists('formatWeight')) {
    function formatWeight($g)
    {
        if ($g >= 1000) {
            $kg = $g / 1000;
            return rtrim(rtrim(number_format($kg, 2, '.', ''), '0'), '.') . ' kg';
        }

        return number_format($g, 2, '.', '') . ' g';
    }
}