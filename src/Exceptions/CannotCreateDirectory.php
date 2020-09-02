<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class CannotCreateDirectory extends RenderableException
{
    public function __construct(string $directory)
    {
        parent::__construct("Cannot create ${directory} directory");
    }
}
