<?php

namespace Spatie\DataTransferObject\Tests\Casters;

use Exception;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;
use function PHPUnit\Framework\assertCount;

beforeAll(function () {
    class Bar extends DataTransferObject
    {
        /** @var \Spatie\DataTransferObject\Tests\Foo[] */
        #[CastWith(FooArrayCaster::class)]
        public array $collectionOfFoo;
    }

    class Foo extends DataTransferObject
    {
        public string $name;
    }

    class FooArrayCaster implements Caster
    {
        public function cast(mixed $value): array
        {
            if (! is_array($value)) {
                throw new Exception("Can only cast arrays to Foo");
            }

            return array_map(
                fn (array $data) => new Foo(...$value),
                $value
            );
        }
    }
});

test('collection caster', function () {
    $bar = new Bar([
        'collectionOfFoo' => [
            ['name' => 'a'],
            ['name' => 'b'],
            ['name' => 'c'],
        ],
    ]);

    assertCount(3, $bar->collectionOfFoo);
});
