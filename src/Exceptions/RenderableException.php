<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

abstract class RenderableException extends \Exception
{
    public function render(): void
    {
        fwrite(STDERR, $this->getMessage() . PHP_EOL);
    }
}
