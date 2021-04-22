<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\ValidationException;
use Spatie\DataTransferObject\Tests\Dummy\NumberBetween;

class ValidationTest extends TestCase
{
    /** @test */
    public function test_validation()
    {
        $dto = new class(foo: 50) extends DataTransferObject {
            public int $foo;
        };

//        $this->assertEquals(50, $dto->foo);

//        $this->expectException(ValidationException::class);

        new class(foo: 150) extends DataTransferObject {
            #[NumberBetween(1, 100)]
            public int $foo;
        };
    }

    /** @test */
    public function nested_validation()
    {
        try {
            new NestedBaz(
                [
                    'a' => ['x' => 150, 'y' => 150,],
                    'b' => ['x' => 150, 'y' => 150,],
                ]
            );
        } catch (ValidationException $e) {
            $this->assertStringContainsString('nestedBaz->x');
        }
    }
}

class NestedBaz extends DataTransferObject
{
    public NestedFoo $a;

    public NestedFoo $b;
}

class NestedFoo extends DataTransferObject
{
    #[NumberBetween(1, 100)]
    public int $x;

    #[NumberBetween(1, 100)]
    public int $y;
}

