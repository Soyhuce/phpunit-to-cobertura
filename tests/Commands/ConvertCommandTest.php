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
}
