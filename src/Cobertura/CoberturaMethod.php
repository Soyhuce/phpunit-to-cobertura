<?php

namespace Soyhuce\PhpunitToCobertura\Cobertura;

class CoberturaMethod
{
    /** @var string */
    private $name;

    /** @var string */
    private $signature;

    /** @var float */
    private $lineRate;

    /** @var float */
    private $branchRate;

    /** @var string */
    private $complexity;

    /** @var array */
    private $lines;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    public function setLineRate(float $lineRate): self
    {
        $this->lineRate = $lineRate;

        return $this;
    }

    public function setBranchRate(float $branchRate): self
    {
        $this->branchRate = $branchRate;

        return $this;
    }

    public function setComplexity(string $complexity): self
    {
        $this->complexity = $complexity;

        return $this;
    }

    public function setLines(array $lines): self
    {
        $this->lines = $lines;

        return $this;
    }

    public function lines(): array
    {
        return $this->lines;
    }

    public function wrapWith(\DOMDocument $domDocument): \DOMElement
    {
        $method = $domDocument->createElement('method');
        $method->setAttribute('name', $this->name);
        $method->setAttribute('signature', $this->signature);
        $method->setAttribute('line-rate', $this->lineRate);
        $method->setAttribute('branch-rate', (string) $this->branchRate);
        $method->setAttribute('complexity', (string) $this->complexity);

        $lines = $domDocument->createElement('lines');
        foreach ($this->lines as $number => $hits) {
            $line = $domDocument->createElement('line');
            $line->setAttribute('number', $number);
            $line->setAttribute('hits', $hits);
            $lines->appendChild($line);
        }
        $method->appendChild($lines);

        return $method;
    }
}
