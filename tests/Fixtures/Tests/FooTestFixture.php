<?php

namespace Soyhuce\PhpunitToCobertura\Tests\Fixtures\Tests;

use PHPUnit\Framework\TestCase;
use Soyhuce\PhpunitToCobertura\Tests\Fixtures\Src\Foo;

class FooTestFixture extends TestCase
{
    /** @test */
    public function fooTrueIsTrue(): void
    {
        $foo = new Foo();
        $this->assertTrue($foo->true());
    }

    /** @test */
    public function itHasBranchesWithFooAndBar(): void
    {
        $foo = new Foo();
        $this->assertEquals('With foo and bar', $foo->itHasBranches(true, true));
    }
}
