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

        if ($file->numberOfFunctions() > 0) {
            $this->addFunctions($file->functions(), $fileName, $lineCoverage);
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
            $coberturaClass->addMethod($this->makeMethod($method, $lineCoverage));
        }
    }

    private function makeMethod(array $method, array $lineCoverage): CoberturaMethod
    {
        $coberturaMethod = new CoberturaMethod($method['methodName']);

        return $coberturaMethod->setSignature($method['signature'])
            ->setLineRate(Utils::rate($method['executedLines'], $method['executableLines']))
            ->setBranchRate(Utils::rate($method['executedBranches'], $method['executableBranches']))
            ->setComplexity($method['ccn'])
            ->setLines($this->extractLines($lineCoverage, $method['startLine'], $method['endLine']));
    }

    private function extractLines(array $lineCoverage, int $methodStart, int $methodEnd): array
    {
        $filtered = array_filter(
            $lineCoverage,
            function (?array $coveredBy, int $line) use ($methodStart, $methodEnd) {
                if ($coveredBy === null) {
                    return false;
                }

                return $line >= $methodStart && $line < $methodEnd;
            },
            ARRAY_FILTER_USE_BOTH
        );

        return array_map(function (array $coveredBy) {
            return count($coveredBy);
        }, $filtered);
    }

    private function addFunctions(array $functions, string $fileName, array $lineCoverage): void
    {
        $coberturaClass = $this->document->createClass('functions\\' . basename($fileName));
        $coberturaClass->filename($fileName)
            ->setExecutedLines(Utils::arraySum($functions, 'executedLines'))
            ->setExecutableLines(Utils::arraySum($functions, 'executableLines'))
            ->setExecutedBranches(Utils::arraySum($functions, 'executedBranches'))
            ->setExecutableBranches(Utils::arraySum($functions, 'executableBranches'))
            ->setComplexity(Utils::arraySum($functions, 'ccn'));

        // Functions does not have endLine. Hack to create one
        $previousKey = null;
        foreach ($functions as $key => $function) {
            if ($previousKey === null) {
                $previousKey = $key;
                continue;
            }
            $functions[$previousKey]['endLine'] = $function['startLine'] - 1;
            $previousKey = $key;
        }
        $functions[$previousKey]['endLine'] = array_keys($lineCoverage)[count($lineCoverage) - 1];

        foreach ($functions as $function) {
            $function['methodName'] = $function['functionName'];
            $coberturaClass->addMethod($this->makeMethod($function, $lineCoverage));
        }
    }
}
