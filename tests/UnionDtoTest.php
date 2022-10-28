<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\RoundingCaster;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertIsInt;

beforeAll(function () {
    class UnionDto extends DataTransferObject
    {
        public string | int $foo;
    }

    class UnionDtoWithCast extends DataTransferObject
    {
        #[CastWith(RoundingCaster::class)]
        public float | int $bar;
    }

    class ComplexUnionDto extends DataTransferObject
    {
        public Dto1 | Dto2 $baz;
    }

    class Dto1 extends DataTransferObject
    {
        public int $value = 1;
    }

    class Dto2 extends DataTransferObject
    {
        public int $value = 2;
    }

});

test('union types are allowed', function () {
    $dto = new UnionDto(foo: 1);

    assertEquals(1, $dto->foo);
});

test('union types rounding float', function () {
    $dto = new UnionDtoWithCast(bar: 123.456);

    assertEquals(123.46, $dto->bar);
});

test('union types rounding integer', function () {
    $dto = new UnionDtoWithCast(bar: 123);

    assertIsInt($dto->bar);
    assertEquals(123, $dto->bar);
});

test('complex union types fallback', function () {
    $dto = new ComplexUnionDto([
        'baz' => ['value' => 3],
    ]);

    assertInstanceOf(Dto1::class, $dto->baz);
    assertEquals(3, $dto->baz->value);
});

test('complex union types force', function () {
    $dto = new ComplexUnionDto(
        baz: new Dto2(value: 3),
    );

    assertInstanceOf(Dto2::class, $dto->baz);
    assertEquals(3, $dto->baz->value);
});
