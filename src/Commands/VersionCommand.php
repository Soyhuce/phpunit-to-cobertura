<?php

namespace Soyhuce\PhpunitToCobertura\Commands;

use Soyhuce\PhpunitToCobertura\Main;

class VersionCommand implements Command
{
    public function run(): void
    {
        echo 'PHPUnit to Cobertura version ' . Main::VERSION . PHP_EOL . PHP_EOL;
    }
}
