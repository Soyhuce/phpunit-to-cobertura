<?php

namespace Soyhuce\PhpunitToCobertura\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Soyhuce\PhpunitToCobertura\Commands\ConvertCommand;
use Soyhuce\PhpunitToCobertura\Exceptions\BadCall;

class ConvertCommandTest extends TestCase
{
    public function badArguments(): array
    {
        return [
            [['phpunit-to-cobertura']],
            [['phpunit-to-cobertura', 'first']],
            [['phpunit-to-cobertura', '-h', '-v']],
            [['phpunit-to-cobertura', '-h', 'first']],
            [['phpunit-to-cobertura', 'first', 'second', 'third']],
        ];
    }

    /**
     * @test
     * @dataProvider badArguments
     */
    public function badCallIsThrownWhenConvertCommandHaveBadArguments(array $args): void
    {
        $this->expectException(BadCall::class);

        new ConvertCommand($args);
    }

    public function goodArguments(): array
    {
        return [
            [['phpunit-to-cobertura', 'first', 'second']],
            [['phpunit-to-cobertura', '--foo', 'first', 'second']],
            [['phpunit-to-cobertura', 'first', 'second', '--bar']],
        ];
    }

    /**
     * @test
     * @dataProvider goodArguments
     */
    public function convertCommandCanParseGoodArguments(array $args): void
    {
        new ConvertCommand($args);

        $this->assertTrue(true);
    }

    /** @test */
    public function reportIsSuccessfullyGenerated(): void
    {
        $coverageFile = __DIR__ . '/../Fixtures/phpunit/codeCoverage.php';
        if (is_file($coverageFile)) {
            unlink($coverageFile);
        }

        $phpunit = __DIR__ . '/../../vendor/bin/phpunit';
        $config = __DIR__ . '/../Fixtures/phpunit.xml';
        shell_exec("${phpunit} --config=${config}");
        $this->assertFileExists($coverageFile);

        $coberturaFile = __DIR__ . '/../Fixtures/phpunit/cobertura.xml';
        $command = new ConvertCommand([
            'phpunit-to-cobertura',
            $coverageFile,
            $coberturaFile,
        ]);

        $command->run();

        $this->assertFileExists($coberturaFile);
        $this->assertFileEquals(__DIR__ . '/../Fixtures/expected/cobertura.stub', $coberturaFile);
    }
}
