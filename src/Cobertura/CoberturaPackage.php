<?php

namespace Soyhuce\PhpunitToCobertura\Cobertura;

use Soyhuce\PhpunitToCobertura\Support\Utils;

class CoberturaPackage
{
    /** @var string */
    private $name;

    /** @var array<\Soyhuce\PhpunitToCobertura\Cobertura\CoberturaClass> */
    private $classes;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->classes = [];
    }

    public function addClass(CoberturaClass $coberturaClass): void
    {
        $this->classes[] = $coberturaClass;
    }

    public function wrapWith(\DOMDocument $domDocument): \DOMElement
    {
        $classes = $domDocument->createElement('classes');
        foreach ($this->classes as $class) {
            $classes->appendChild($class->wrapWith($domDocument));
        }

        $package = $domDocument->createElement('package');
        $package->appendChild($classes);
        $package->setAttribute('name', $this->name);
        $package->setAttribute('line-rate', (string) $this->lineRate());
        $package->setAttribute('branch-rate', (string) $this->branchRate());
        $package->setAttribute('complexity', (string) $this->complexity());

        return $package;
    }

    public function executedLines(): int
    {
        return Utils::arraySum($this->classes, function (CoberturaClass $class) {
            return $class->executedLines();
        });
    }

    public function executableLines(): int
    {
        return Utils::arraySum($this->classes, function (CoberturaClass $class) {
            return $class->executableLines();
        });
    }

    private function lineRate(): float
    {
        return Utils::rate($this->executedLines(), $this->executableLines());
    }

    public function executedBranches(): int
    {
        return Utils::arraySum($this->classes, function (CoberturaClass $class) {
            return $class->executedBranches();
        });
    }

    public function executableBranches(): int
    {
        return Utils::arraySum($this->classes, function (CoberturaClass $class) {
            return $class->executableBranches();
        });
    }

    private function branchRate(): float
    {
        return Utils::rate($this->executedBranches(), $this->executableBranches());
    }

    public function complexity(): int
    {
        return Utils::arraySum($this->classes, function (CoberturaClass $class) {
            return $class->complexity();
        });
    }
}
