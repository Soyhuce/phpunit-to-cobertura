<?php

namespace Soyhuce\PhpunitToCobertura\Tests\Fixtures\Src;

class Foo
{
    public function true(): bool
    {
        return true;
    }

    public function itHasBranches(bool $foo, bool $bar): string
    {
        if ($foo) {
            $result = 'With foo';
        } else {
            $result = 'Without foo';
        }

        if ($bar) {
            $result = concat($result, ' and bar');
        }

        return $result;
    }

    public function deadCode(): void
    {
        throw new \Exception('This should not be called');
    }
}
