<?php

namespace Soyhuce\PhpunitToCobertura\Commands;

use Soyhuce\PhpunitToCobertura\Exceptions\BadCall;

class ConvertCommand implements Command
{
    /** @var string */
    protected $inputFile;

    /** @var string */
    protected $outputFile;

    public function __construct(array $argv)
    {
        $args = array_filter($argv, function (string $value, int $key) {
            if ($key === 0) {
                return false;
            }
            if (str_starts_with($value, '-')) {
                return false;
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        if (count($args) != 2) {
            throw new BadCall('You must provide input and output files');
        }

        [$this->inputFile, $this->outputFile] = array_values($args);
    }

    public function run(): void
    {
        var_dump($this->inputFile);
        var_dump($this->outputFile);
    }
}
