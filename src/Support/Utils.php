<?php

namespace Soyhuce\PhpunitToCobertura\Support;

class Utils
{
    public static function rate(int $partial, int $total): float
    {
        return $total === 0 ? 0 : $partial / $total;
    }

    public static function strAfter(string $subject, string $search): string
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}
