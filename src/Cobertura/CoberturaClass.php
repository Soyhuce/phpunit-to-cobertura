<?php

namespace Soyhuce\PhpunitToCobertura\Cobertura;

use Soyhuce\PhpunitToCobertura\Support\Utils;

class CoberturaClass
{
    /** @var string */
    private $name;

    /** @var string */
    private $filename;

    /** @var int */
    private $executedLines;

    /** @var int */
    private $executableLines;

    /** @var int */
    private $executedBranches;

    /** @var int */
    private $executableBranches;

    /** @var int */
    private $complexity;

    /** @var array<\Soyhuce\PhpunitToCobertura\Cobertura\CoberturaMethod> */
    private $methods;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->methods = [];
    }

    public function filename(string $fileName): self
    {
        $this->filename = $fileName;

        return $this;
    }

    public function setExecutedLines(int $executedLines): self
    {
        $this->executedLines = $executedLines;

        return $this;
    }

    public function executedLines(): int
    {
        return $this->executedLines;
    }

    public function setExecutableLines(int $executableLines): self
    {
        $this->executableLines = $executableLines;

        return $this;
    }

    public function executableLines(): int
    {
        return $this->executableLines;
    }

    public function setExecutedBranches(int $executedBranches): self
    {
        $this->executedBranches = $executedBranches;

        return $this;
    }

    public function executedBranches(): int
    {
        return $this->executedBranches;
    }

    public function setExecutableBranches(int $executableBranches): self
    {
        $this->executableBranches = $executableBranches;

        return $this;
    }

    public function executableBranches(): int
    {
        return $this->executableBranches;
    }

    public function setComplexity(int $complexity): self
    {
        $this->complexity = $complexity;

        return $this;
    }

    public function complexity(): int
    {
        return $this->complexity;
    }

    public function addMethod(CoberturaMethod $coberturaMethod): void
    {
        $this->methods[] = $coberturaMethod;
    }

    public function wrapWith(\DOMDocument $domDocument): \DOMElement
    {
        $class = $domDocument->createElement('class');
        $class->setAttribute('name', $this->name);
        $class->setAttribute('filename', Utils::strAfter($this->filename, getcwd() . '/'));
        $class->setAttribute('line-rate', (string) $this->lineRate());
        $class->setAttribute('branch-rate', (string) $this->branchRate());
        $class->setAttribute('complexity', (string) $this->complexity);

        $methods = $domDocument->createElement('methods');
        $lines = $domDocument->createElement('lines');

        foreach ($this->methods as $method) {
            $methods->appendChild($method->wrapWith($domDocument));

            foreach ($method->lines() as $number => $hits) {
                $line = $domDocument->createElement('line');
                $line->setAttribute('number', $number);
                $line->setAttribute('hits', $hits);
                $lines->appendChild($line);
            }
        }

        $class->appendChild($methods);
        $class->appendChild($lines);

        return $class;
    }

    private function lineRate(): float
    {
        return Utils::rate($this->executedLines, $this->executableLines);
    }

    private function branchRate(): float
    {
        return Utils::rate($this->executedBranches, $this->executableBranches);
    }
}
