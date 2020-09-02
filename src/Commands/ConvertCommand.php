<?php

namespace Soyhuce\PhpunitToCobertura\Commands;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use Soyhuce\PhpunitToCobertura\Cobertura\CoberturaDocument;
use Soyhuce\PhpunitToCobertura\Cobertura\Translator;
use Soyhuce\PhpunitToCobertura\Exceptions\BadCall;
use Soyhuce\PhpunitToCobertura\Exceptions\CannotCreateDirectory;
use Soyhuce\PhpunitToCobertura\Exceptions\InputFileDoesNotExist;
use Soyhuce\PhpunitToCobertura\Exceptions\InputFileMustBePhpCodeCoverage;
use Soyhuce\PhpunitToCobertura\Exceptions\InputFileNotReadable;

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
        $this->ensureInputFileExists();

        $codeCoverage = $this->importCodeCoverage();

        $translator = new Translator($codeCoverage);
        $coberturaDocument = $translator->translate();

        $this->saveDocument($coberturaDocument);

        echo "PHPUnit file {$this->inputFile} was successfully converted to Cobertura file {$this->outputFile}" . PHP_EOL;
    }

    protected function ensureInputFileExists(): void
    {
        if (!is_file($this->inputFile)) {
            throw new InputFileDoesNotExist($this->inputFile);
        }
        if (!is_readable($this->inputFile)) {
            throw new InputFileNotReadable($this->inputFile);
        }
    }

    protected function importCodeCoverage(): CodeCoverage
    {
        $codeCoverage = require $this->inputFile;

        if (!$codeCoverage instanceof CodeCoverage) {
            throw new InputFileMustBePhpCodeCoverage();
        }

        return $codeCoverage;
    }

    private function saveDocument(CoberturaDocument $coberturaDocument): void
    {
        if (!is_dir(dirname($this->outputFile))) {
            $this->createOutputDirectory();
        }

        file_put_contents($this->outputFile, $coberturaDocument->output());
    }

    private function createOutputDirectory(): void
    {
        $dir = dirname($this->outputFile);
        if (!@mkdir($dir, 0777, true) || !is_dir($dir)) {
            throw new CannotCreateDirectory($dir);
        }
    }
}
