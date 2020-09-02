<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class BadCall extends RenderableException
{
    public function render(): void
    {
        parent::render();
        fwrite(STDERR, 'Run phpunit-to-cobertura --help to get some help.' . PHP_EOL);
    }
}
