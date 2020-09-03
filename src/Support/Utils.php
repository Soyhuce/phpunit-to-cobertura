<?php

namespace Soyhuce\PhpunitToCobertura\Support;

class Utils
{
    public static function rate(int $partial, int $total): float
    {
        return $total === 0 ? 0 : $partial / $total;
    }

    public static function strStartsWith(string $haystack, string $needle): bool
    {
        return 0 === \strncmp($haystack, $needle, \strlen($needle));
    }

    public static function strAfter(string $subject, string $search): string
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}
