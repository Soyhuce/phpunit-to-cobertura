<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class InputFileMustBePhpCodeCoverage extends RenderableException
{
    public function __construct()
    {
        parent::__construct('Input file must be a code coverage report in PHP format.');
    }
}
