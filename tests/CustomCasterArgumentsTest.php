<?php

namespace Spatie\DataTransferObject\Tests;

use Illuminate\Support\Collection;
use LogicException;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class CustomCasterArgumentsTest extends TestCase
{
    /** @test */
    public function test_generic_array_caster_on_array_type()
    {
        $bar = new Bar(
            [
                'collectionOfFoo' => [
                    ['name' => 'a'],
                    new Foo(name: 'b'),
                    ['name' => 'c'],
                ],
                'collectionOfBaz' => [
                    ['name' => 'a'],
                    new Baz(name: 'b'),
                    ['name' => 'c'],
                ],
            ]
        );

        $this->assertIsArray($bar->collectionOfFoo);
        $this->assertCount(3, $bar->collectionOfFoo);
        $this->assertContainsOnlyInstancesOf(Foo::class, $bar->collectionOfFoo);

        $this->assertIsArray($bar->collectionOfBaz);
        $this->assertCount(3, $bar->collectionOfBaz);
        $this->assertContainsOnlyInstancesOf(Baz::class, $bar->collectionOfBaz);
    }

    public function test_generic_array_caster_on_array_access_type()
    {
        $bar = new BarJr(
            [
                'collectionOfFoo' => [
                    ['name' => 'a'],
                    new Foo(name: 'b'),
                    ['name' => 'c'],
                ],
                'collectionOfBaz' => [
                    ['name' => 'a'],
                    new Baz(name: 'b'),
                    ['name' => 'c'],
                ],
            ]
        );

        $this->assertInstanceOf(Collection::class, $bar->collectionOfFoo);
        $this->assertCount(3, $bar->collectionOfFoo);
        $this->assertContainsOnlyInstancesOf(Foo::class, $bar->collectionOfFoo);

        $this->assertInstanceOf(Collection::class, $bar->collectionOfFoo);
        $this->assertCount(3, $bar->collectionOfBaz);
        $this->assertContainsOnlyInstancesOf(Baz::class, $bar->collectionOfBaz);
    }

    public function test_generic_array_caster_on_invalid_type()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Caster [ArrayCaster] may only be used to cast arrays or objects that implement ArrayAccess.');

        new BarIllogical(
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

class BarJr extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\Foo[] */
    #[CastWith(ArrayCaster::class, itemType: Foo::class)]
    public Collection $collectionOfFoo;

    /** @var \Spatie\DataTransferObject\Tests\Baz[] */
    #[CastWith(ArrayCaster::class, itemType: Baz::class)]
    public Collection $collectionOfBaz;
}

class BarIllogical extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\Foo[] */
    #[CastWith(ArrayCaster::class, itemType: Foo::class)]
    public string $collectionOfFoo;

    /** @var \Spatie\DataTransferObject\Tests\Baz[] */
    #[CastWith(ArrayCaster::class, itemType: Baz::class)]
    public int $collectionOfBaz;
}

class Foo extends DataTransferObject
{
    public string $name;
}

class Baz extends DataTransferObject
{
    public string $name;
}
