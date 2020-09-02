<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class UnexpectedException extends RenderableException
{
    public function __construct(\Throwable $throwable)
    {
        parent::__construct(
            'Oups, something went wrong',
            $throwable->getCode(),
            $throwable
        );
    }

    public function render(): void
    {
        parent::render();
        fwrite(STDERR, $this->getPrevious()->getTraceAsString() . PHP_EOL);
    }
}
