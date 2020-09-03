<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class UnableToFindClassName extends RenderableException
{
    public function __construct(array $classData)
    {
        parent::__construct(
            'Unable to find classname in ' . var_export($classData, true) . PHP_EOL
            . 'Please report your exception on Github (https://github.com/Soyhuce/phpunit-to-cobertura) in order for us to fix this.'
        );
    }
}
