<?php

namespace Spatie\DataTransferObject\Tests;

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Illuminate\Support\Collection;
use Iterator;
use IteratorAggregate;
use LogicException;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertContainsOnlyInstancesOf;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsArray;

beforeAll(function () {
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

    class ArrayAccessImplementation implements ArrayAccess
    {
        public function __construct(protected array $state = [])
        {
        }

        public function offsetExists(mixed $offset): bool
        {
            return key_exists($offset, $this->state);
        }

        public function offsetGet(mixed $offset): mixed
        {
            return $this->state[$offset];
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if (! empty($offset)) {
                $this->state[$offset] = $value;
            } else {
                $this->state[] = $value;
            }
        }

        public function offsetUnset(mixed $offset): void
        {
            unset($this->state[$offset]);
        }
    }

    class ArrayAccessIteratorAggregate extends ArrayAccessImplementation implements IteratorAggregate
    {
        public function getIterator(): \Traversable
        {
            return new ArrayIterator($this->state);
        }
    }

    class ArrayAccessIterator extends ArrayAccessImplementation implements Iterator
    {
        public function current(): mixed
        {
            return current($this->state);
        }

        public function key(): mixed
        {
            return key($this->state);
        }

        public function next(): void
        {
            next($this->state);
        }

        public function rewind(): void
        {
            reset($this->state);
        }

        public function valid(): bool
        {
            return isset($this->state[$this->key()]);
        }
    }

    class DTOWithArrayAccessImplementation extends DataTransferObject
    {
        /** @var \Spatie\DataTransferObject\Tests\Foo[] */
        #[CastWith(ArrayCaster::class, itemType: Foo::class)]
        public ArrayAccessImplementation $collectionOfFoo;

        /** @var \Spatie\DataTransferObject\Tests\Baz[] */
        #[CastWith(ArrayCaster::class, itemType: Baz::class)]
        public ArrayAccessImplementation $collectionOfBaz;
    }

    class DTOWithArrayAccessIteratorAggregate extends DataTransferObject
    {
        /** @var \Spatie\DataTransferObject\Tests\Foo[] */
        #[CastWith(ArrayCaster::class, itemType: Foo::class)]
        public ArrayAccessIteratorAggregate $collectionOfFoo;

        /** @var \Spatie\DataTransferObject\Tests\Baz[] */
        #[CastWith(ArrayCaster::class, itemType: Baz::class)]
        public ArrayAccessIteratorAggregate $collectionOfBaz;
    }

    class DTOWithArrayAccessIterator extends DataTransferObject
    {
        /** @var \Spatie\DataTransferObject\Tests\Foo[] */
        #[CastWith(ArrayCaster::class, itemType: Foo::class)]
        public ArrayAccessIterator $collectionOfFoo;

        /** @var \Spatie\DataTransferObject\Tests\Baz[] */
        #[CastWith(ArrayCaster::class, itemType: Baz::class)]
        public ArrayAccessIterator $collectionOfBaz;
    }

    class DTOWithArrayObject extends DataTransferObject
    {
        /** @var \Spatie\DataTransferObject\Tests\Foo[] */
        #[CastWith(ArrayCaster::class, itemType: Foo::class)]
        public ArrayObject $collectionOfFoo;

        /** @var \Spatie\DataTransferObject\Tests\Baz[] */
        #[CastWith(ArrayCaster::class, itemType: Baz::class)]
        public ArrayObject $collectionOfBaz;
    }
});

test('generic array caster on array type', function () {
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

    assertIsArray($bar->collectionOfFoo);
    assertCount(3, $bar->collectionOfFoo);
    assertContainsOnlyInstancesOf(Foo::class, $bar->collectionOfFoo);

    assertIsArray($bar->collectionOfBaz);
    assertCount(3, $bar->collectionOfBaz);
    assertContainsOnlyInstancesOf(Baz::class, $bar->collectionOfBaz);
});

test('generic array caster on invalid type', function () {
    new BarIllogical(
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
})->throws(LogicException::class, 'Caster [ArrayCaster] may only be used to cast arrays or objects that implement ArrayAccess.');

/**
 * @see https://github.com/spatie/data-transfer-object/issues/216
 */
test('casting an empty array object will not add ghost value', function () {
    $object = new BarJr([
        'collectionOfFoo' => new Collection(),
        'collectionOfBaz' => new Collection(),
    ]);

    assertInstanceOf(Collection::class, $object->collectionOfFoo);
    assertEmpty($object->collectionOfFoo);

    assertInstanceOf(Collection::class, $object->collectionOfBaz);
    assertEmpty($object->collectionOfBaz);
});

test('it cannot cast array access without traversable', function () {
    new DTOWithArrayAccessImplementation(
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
})->throws(LogicException::class, 'Caster [ArrayCaster] may only be used to cast ArrayAccess objects that are traversable.');

test('it can cast iterator aggregate', function () {
    $object = new DTOWithArrayAccessIteratorAggregate(
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
                ['name' => 'd'],
            ],
        ]
    );

    assertInstanceOf(ArrayAccessIteratorAggregate::class, $object->collectionOfFoo);
    assertContainsOnlyInstancesOf(Foo::class, $object->collectionOfFoo);
    assertCount(3, $object->collectionOfFoo);

    assertInstanceOf(ArrayAccessIteratorAggregate::class, $object->collectionOfBaz);
    assertContainsOnlyInstancesOf(Baz::class, $object->collectionOfBaz);
    assertCount(4, $object->collectionOfBaz);
});

test('it can cast array object', function () {
    $object = new DTOWithArrayObject(
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
                ['name' => 'd'],
            ],
        ]
    );

    assertInstanceOf(ArrayObject::class, $object->collectionOfFoo);
    assertContainsOnlyInstancesOf(Foo::class, $object->collectionOfFoo);
    assertCount(3, $object->collectionOfFoo);

    assertInstanceOf(ArrayObject::class, $object->collectionOfBaz);
    assertContainsOnlyInstancesOf(Baz::class, $object->collectionOfBaz);
    assertCount(4, $object->collectionOfBaz);
});

test('it throws exception when casting non array', function () {
    new DTOWithArrayObject(
        [
            'collectionOfFoo' => [
                ['name' => 'a'],
                1,
                ['name' => 'c'],
            ],
            'collectionOfBaz' => [
                ['name' => 'a'],
                ['name' => 'b'],
                ['name' => 'c'],
                ['name' => 'd'],
            ],
        ]
    );
})->throws(LogicException::class, 'Caster [ArrayCaster] each item must be an array or an instance of the specified item type [Spatie\DataTransferObject\Tests\Foo].');

test('that array keys get cast', function () {
    $object = new Bar(
        [
            'collectionOfFoo' => [
                'one' => ['name' => 'a'],
                'two' => ['name' => 'b'],
                'three' => ['name' => 'c'],
            ],
            'collectionOfBaz' => [
                ['name' => 'a'],
                ['name' => 'b'],
                ['name' => 'c'],
                ['name' => 'd'],
            ],
        ]
    );

    assertArrayHasKey('one', $object->collectionOfFoo);
    assertArrayHasKey('two', $object->collectionOfFoo);
    assertArrayHasKey('three', $object->collectionOfFoo);
    assertInstanceOf(Foo::class, $object->collectionOfFoo['one']);
    assertInstanceOf(Foo::class, $object->collectionOfFoo['two']);
    assertInstanceOf(Foo::class, $object->collectionOfFoo['three']);
});

test('that array object keys get cast', function () {
    $object = new DTOWithArrayObject(
        [
            'collectionOfFoo' => [
                'one' => ['name' => 'a'],
                'two' => ['name' => 'b'],
                'three' => ['name' => 'c'],
            ],
            'collectionOfBaz' => [
                ['name' => 'a'],
                ['name' => 'b'],
                ['name' => 'c'],
                ['name' => 'd'],
            ],
        ]
    );

    assertArrayHasKey('one', $object->collectionOfFoo);
    assertArrayHasKey('two', $object->collectionOfFoo);
    assertArrayHasKey('three', $object->collectionOfFoo);
    assertInstanceOf(Foo::class, $object->collectionOfFoo['one']);
    assertInstanceOf(Foo::class, $object->collectionOfFoo['two']);
    assertInstanceOf(Foo::class, $object->collectionOfFoo['three']);
});
