<?php

namespace Soyhuce\PhpunitToCobertura\Exceptions;

class MalformedReportGenerated extends RenderableException
{
    public function __construct()
    {
        parent::__construct('The Cobertura report generated is malformed.');
    }
}
