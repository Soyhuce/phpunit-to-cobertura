<?php

if (!function_exists('concat')) {
    function concat(string $first, string $second): string
    {
        return $first . $second;
    }
}

if (!function_exists('deadFunction')) {
    function deadFunction(): int
    {
        return 1;
    }
}
