<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class InputFileNotReadable extends RenderableException
{
    public function __construct(string $file)
    {
        parent::__construct("File ${file} is not readable");
    }
}
