<?php

namespace Soyhuce\PhpunitToCobertura\Cobertura;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Node\File;
use Soyhuce\PhpunitToCobertura\Exceptions\UnableToFindClassName;
use Soyhuce\PhpunitToCobertura\Support\Utils;

class Translator
{
    /** @var \SebastianBergmann\CodeCoverage\CodeCoverage */
    private $codeCoverage;

    /** @var \Soyhuce\PhpunitToCobertura\Cobertura\CoberturaDocument */
    private $document;

    public function __construct(CodeCoverage $codeCoverage)
    {
        $this->codeCoverage = $codeCoverage;
        $this->document = new CoberturaDocument();
    }

    public function translate(): CoberturaDocument
    {
        foreach ($this->codeCoverage->getReport() as $file) {
            if (!$file instanceof File) {
                continue;
            }
            $this->addFile($file);
        }

        return $this->document;
    }

    private function addFile(File $file): void
    {
        $root = $file;
        while ($root->parent() !== null) {
            $root = $root->parent();
        }
        $this->document->addSource($root->pathAsString());

        $fileName = $file->pathAsString();
        $lineCoverage = $file->lineCoverageData();
        foreach ($file->classesAndTraits() as $class) {
            $this->addClass($class, $fileName, $lineCoverage);
        }
    }

    private function addClass(array $class, string $fileName, array $lineCoverage)
    {
        if (isset($class['package']['namespace'])) {
            $className = $class['package']['namespace'] . '\\' . $class['className'];
        } elseif (isset($class['className'])) {
            $className = $class['className'];
        } elseif (isset($class['traitName'])) {
            $className = $class['traitName'];
        } else {
            throw new UnableToFindClassName($class);
        }

        $coberturaClass = $this->document->createClass($className);
        $coberturaClass->filename($fileName)
            ->setExecutedLines($class['executedLines'])
            ->setExecutableLines($class['executableLines'])
            ->setExecutedBranches($class['executedBranches'])
            ->setExecutableBranches($class['executableBranches'])
            ->setComplexity($class['ccn']);

        foreach ($class['methods'] as $method) {
            $coberturaMethod = new CoberturaMethod($method['methodName']);
            $coberturaMethod->setSignature($method['signature'])
                ->setLineRate(Utils::rate($method['executedLines'], $method['executableLines']))
                ->setBranchRate(Utils::rate($method['executedBranches'], $method['executableBranches']))
                ->setComplexity($method['ccn'])
                ->setLines($this->extractLines($lineCoverage, $method['startLine'], $method['endLine']));

            $coberturaClass->addMethod($coberturaMethod);
        }
    }

    private function extractLines(array $lineCoverage, int $methodStart, int $methodEnd): array
    {
        $filtered = array_filter(
            $lineCoverage,
            function (int $line) use ($methodStart, $methodEnd) {
                return $line >= $methodStart && $line < $methodEnd;
            },
            ARRAY_FILTER_USE_KEY
        );

        return array_map(function (?array $coveredBy) {
            return $coveredBy === null ? 0 : count($coveredBy);
        }, $filtered);
    }
}
