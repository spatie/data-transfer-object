<?php

namespace Spatie\DataTransferObject\Tests\CollectionCaster;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;

beforeAll(function () {
    class Bar extends DataTransferObject
    {
        #[CastWith(FooCollectionCaster::class)]
        public CollectionOfFoo $collectionOfFoo;
    }

    class Foo extends DataTransferObject
    {
        public string $name;
    }

    class CollectionOfFoo extends Collection
    {
        public function offsetGet($key): Foo
        {
            return parent::offsetGet($key);
        }
    }

    class FooCollectionCaster implements Caster
    {
        public function cast(mixed $value): CollectionOfFoo
        {
            return new CollectionOfFoo(array_map(
                fn (array $data) => new Foo(...$data),
                $value
            ));
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
    assertInstanceOf(Foo::class, $bar->collectionOfFoo[0]);
});
