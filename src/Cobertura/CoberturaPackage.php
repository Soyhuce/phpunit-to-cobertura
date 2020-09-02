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
        return array_reduce(
            $this->classes,
            function (int $total, CoberturaClass $class) {
                return $total + $class->executedLines();
            },
            0
        );
    }

    public function executableLines(): int
    {
        return array_reduce(
            $this->classes,
            function (int $total, CoberturaClass $class) {
                return $total + $class->executableLines();
            },
            0
        );
    }

    private function lineRate(): float
    {
        return Utils::rate($this->executedLines(), $this->executableLines());
    }

    public function executedBranches(): int
    {
        return array_reduce(
            $this->classes,
            function (int $total, CoberturaClass $class) {
                return $total + $class->executedBranches();
            },
            0
        );
    }

    public function executableBranches(): int
    {
        return array_reduce(
            $this->classes,
            function (int $total, CoberturaClass $class) {
                return $total + $class->executableBranches();
            },
            0
        );
    }

    private function branchRate(): float
    {
        return Utils::rate($this->executedBranches(), $this->executableBranches());
    }

    public function complexity(): int
    {
        return array_reduce(
            $this->classes,
            function (int $total, CoberturaClass $class) {
                return $total + $class->complexity();
            },
            0
        );
    }
}
