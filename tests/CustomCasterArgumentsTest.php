<?php

namespace Spatie\DataTransferObject\Tests;

use Exception;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Caster;
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
    #[CastWith(GenericArrayCaster::class, targetClass: Foo::class)]
    public array $collectionOfFoo;

    /** @var \Spatie\DataTransferObject\Tests\Baz[] */
    #[CastWith(GenericArrayCaster::class, targetClass: Baz::class)]
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

class GenericArrayCaster implements Caster
{
    public function __construct(
        private string $type,
        private array $args
    ) {
    }

    public function cast(mixed $value): array
    {
        if ($this->type !== 'array') {
            throw new Exception("Can only cast arrays");
        }

        if (! isset($this->args['targetClass'])) {
            throw new Exception("targetClass argument is required");
        }

        return array_map(
            fn (array $data) => new $this->args['targetClass'](...$data),
            $value
        );
    }
}
