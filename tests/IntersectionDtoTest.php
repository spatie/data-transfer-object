<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class IntersectionDtoTest extends TestCase
{
    /** @test */
    public function cast_intersection_type()
    {
        $dto = new IntersectionDto([
            'fooBar' => new FooBarIntersection,
        ]);

        $this->assertInstanceOf(FooInterface::class, $dto->fooBar);
        // $this->assertEquals(3, $dto->foo->value);
    }

    /** @test */
    public function cast_intersection_type_on_invalid_type()
    {
        $this->expectException(\TypeError::class);

        new IntersectionDto([
            'fooBar' => new FooIntersection,
        ]);
    }
}

class IntersectionDto extends DataTransferObject
{
    public FooInterface&BarInterface $fooBar;
}

class FooIntersection implements FooInterface
{
}


class FooBarIntersection implements FooInterface, BarInterface
{
}

interface FooInterface
{
}

interface BarInterface
{
}
