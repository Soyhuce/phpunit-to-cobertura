<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class InputFileDoesNotExist extends RenderableException
{
    public function __construct(string $file)
    {
        parent::__construct("File ${file} does not exist");
    }
}
