<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class CustomCasterArgumentsTest extends TestCase
{
    /** @test */
    public function test_generic_collection_caster()
    {
        $bar = new Bar(
            [
                'collectionOfFoo' => [
                    ['name' => 'a'],
                    ['name' => 'b'],
                    ['name' => 'c'],
                ],
                'collectionOfBaz' => [
                    ['name' => 'a'],
                    ['name' => 'b'],
                    ['name' => 'c'],
                ],
            ]
        );

        $this->assertCount(3, $bar->collectionOfFoo);
        $this->assertInstanceOf(Foo::class, $bar->collectionOfFoo[0]);

        $this->assertInstanceOf(Baz::class, $bar->collectionOfBaz[0]);
        $this->assertCount(3, $bar->collectionOfBaz);
    }
}

class Bar extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\Foo[] */
    #[CastWith(ArrayCaster::class, itemType: Foo::class)]
    public array $collectionOfFoo;

    /** @var \Spatie\DataTransferObject\Tests\Baz[] */
    #[CastWith(ArrayCaster::class, itemType: Baz::class)]
    public array $collectionOfBaz;
}

class Foo extends DataTransferObject
{
    public string $name;
}

class Baz extends DataTransferObject
{
    public string $name;
}
