<?php

namespace Soyhuce\PhpunitToCobertura\Cobertura;

use Soyhuce\PhpunitToCobertura\Exceptions\MalformedReportGenerated;
use Soyhuce\PhpunitToCobertura\Support\Utils;

class CoberturaDocument
{
    /** @var \DOMDocument */
    private $document;

    /** @var \DOMElement */
    private $coverage;

    /** @var array<string> */
    private $sources;

    /** @var array<string, \Soyhuce\PhpunitToCobertura\Cobertura\CoberturaPackage> */
    private $packages;

    /** @var bool */
    private $wrapped = false;

    public function __construct()
    {
        $this->document = $this->baseDocument();
        $this->coverage = $this->document->createElement('coverage');
        $this->sources = [];
        $this->packages = [];
    }

    protected function baseDocument(): \DOMDocument
    {
        $domImplementation = new \DOMImplementation();
        $dtd = $domImplementation->createDocumentType(
            'coverage',
            '',
            'http://cobertura.sourceforge.net/xml/coverage-04.dtd'
        );
        $document = $domImplementation->createDocument('', '', $dtd);
        $document->xmlVersion = '1.0';
        $document->encoding = 'UTF-8';
        $document->formatOutput = true;
        $document->preserveWhiteSpace = false;

        return $document;
    }

    public function addSource(string $source): void
    {
        if (in_array($source, $this->sources)) {
            return;
        }
        $this->sources[] = $source;
    }

    public function createClass(string $className): CoberturaClass
    {
        $coberturaClass = new CoberturaClass($className);

        $packageName = explode('\\', $className)[0];
        $this->packages[$packageName] = $this->packages[$packageName] ?? new CoberturaPackage($packageName);
        $this->packages[$packageName]->addClass($coberturaClass);

        return $coberturaClass;
    }

    public function output(): string
    {
        $this->wrap();

        if (!$this->document->validate()) {
            throw new MalformedReportGenerated();
        }

        return $this->document->saveXML();
    }

    protected function wrap(): void
    {
        if ($this->wrapped) {
            return;
        }

        $sources = $this->document->createElement('sources');
        foreach ($this->sources as $source) {
            $sources->appendChild(
                $this->document->createElement('source', Utils::strAfter($source, getcwd() . '/'))
            );
        }
        $this->coverage->appendChild($sources);

        $packages = $this->document->createElement('packages');
        foreach ($this->packages as $package) {
            $packages->appendChild($package->wrapWith($this->document));
        }
        $this->coverage->appendChild($packages);

        $executedLines = $this->executedLines();
        $executableLines = $this->executableLines();
        $this->coverage->setAttribute('lines-valid', (string) $executableLines);
        $this->coverage->setAttribute('lines-covered', (string) $executedLines);
        $this->coverage->setAttribute('line-rate', (string) Utils::rate($executedLines, $executableLines));

        $executedBranches = $this->executedBranches();
        $executableBranches = $this->executableBranches();
        $this->coverage->setAttribute('branches-valid', (string) $executableBranches);
        $this->coverage->setAttribute('branches-covered', (string) $executedBranches);
        $this->coverage->setAttribute('branch-rate', (string) Utils::rate($executedBranches, $executableBranches));

        $this->coverage->setAttribute('complexity', $this->complexity());
        $this->coverage->setAttribute('timestamp', $_SERVER['REQUEST_TIME']);
        $this->coverage->setAttribute('version', '0.1');

        $this->document->appendChild($this->coverage);
    }

    protected function executedLines(): int
    {
        return array_reduce(
            $this->packages,
            function (int $total, CoberturaPackage $package) {
                return $total + $package->executedLines();
            },
            0
        );
    }

    protected function executableLines(): int
    {
        return array_reduce(
            $this->packages,
            function (int $total, CoberturaPackage $package) {
                return $total + $package->executableLines();
            },
            0
        );
    }

    protected function executedBranches(): int
    {
        return array_reduce(
            $this->packages,
            function (int $total, CoberturaPackage $package) {
                return $total + $package->executedBranches();
            },
            0
        );
    }

    protected function executableBranches(): int
    {
        return array_reduce(
            $this->packages,
            function (int $total, CoberturaPackage $package) {
                return $total + $package->executableBranches();
            },
            0
        );
    }

    protected function complexity(): int
    {
        return array_reduce(
            $this->packages,
            function (int $total, CoberturaPackage $package) {
                return $total + $package->complexity();
            },
            0
        );
    }
}
