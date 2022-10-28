<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Dummy\BasicDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithCastedAttributeHavingCast;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithNullableProperty;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithParent;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithSelf;
use Spatie\DataTransferObject\Tests\Dummy\ComplexStrictDto;
use Spatie\DataTransferObject\Tests\Dummy\WithDefaultValueDto;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

test('array of', function () {
    $list = BasicDto::arrayOf([
        ['name' => 'a'],
        ['name' => 'b'],
    ]);

    assertCount(2, $list);

    assertEquals('a', $list[0]->name);
    assertEquals('b', $list[1]->name);
});

test('create with nested dto', function () {
    $dto = new ComplexDto([
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ]);

    assertEquals('a', $dto->name);
    assertEquals('b', $dto->other->name);
});

test('create with nested dto already casted', function () {
    $dto = new ComplexDto([
        'name' => 'a',
        'other' => new BasicDto([
            'name' => 'b',
        ]),
    ]);

    assertEquals('a', $dto->name);
    assertEquals('b', $dto->other->name);
});

test('create strict with nested dto', function () {
    $dto = new ComplexStrictDto([
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ]);

    assertEquals('a', $dto->name);
    assertEquals('b', $dto->other->name);
});

test('create strict with nested dto already casted', function () {
    $dto = new ComplexStrictDto([
        'name' => 'a',
        'other' => new BasicDto([
            'name' => 'b',
        ]),
    ]);

    assertEquals('a', $dto->name);
    assertEquals('b', $dto->other->name);
});

test('create with null nullable nested dto', function () {
    $dto = new ComplexDtoWithNullableProperty([
        'name' => 'a',
        'other' => null,
    ]);

    assertEquals('a', $dto->name);
    assertNull($dto->other);
});

test('create with nested dto having cast', function () {
    $dto = new ComplexDtoWithCastedAttributeHavingCast([
        'name' => 'a',
        'other' => [
            'name' => 'b',
            'object' => [
                'name' => 'c',
            ],
        ],
    ]);

    assertEquals('a', $dto->name);
    assertEquals('b', $dto->other->name);
    assertEquals('c', $dto->other->object->name);
});

test('all with nested dto', function () {
    $array = [
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ];

    $dto = new ComplexDto($array);

    $all = $dto->all();

    assertCount(2, $all);
    assertEquals('a', $all['name']);
    assertEquals('b', $all['other']->name);
});

test('to array with nested dto', function () {
    $array = [
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ];

    $dto = new ComplexDto($array);

    assertEquals($array, $dto->toArray());
});

test('to array with only', function () {
    $array = [
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ];

    $dto = new ComplexDto($array);

    assertEquals(['name' => 'a'], $dto->only('name')->toArray());
});

test('to array with except', function () {
    $array = [
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ];

    $dto = new ComplexDto($array);

    assertEquals(['other' => ['name' => 'b']], $dto->except('name')->toArray());
});

test('create with default value', function () {
    $dto = new WithDefaultValueDto();

    assertEquals(['name' => 'John'], $dto->toArray());
});

test('create with overriden default value', function () {
    $dto = new WithDefaultValueDto(name: 'Doe');

    assertEquals(['name' => 'Doe'], $dto->toArray());
});

test('clone', function () {
    $array = [
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ];

    $dto = new ComplexDto($array);

    $clone = $dto->clone(other: ['name' => 'a']);

    assertEquals('a', $clone->name);
    assertEquals('a', $clone->other->name);
});

test('create with nested self', function () {
    $dto = new ComplexDtoWithSelf([
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ]);

    assertEquals('a', $dto->name);
    assertEquals('b', $dto->other->name);
    assertNull($dto->other->other);
});

test('create with nested parent', function () {
    $dto = new ComplexDtoWithParent([
        'name' => 'a',
        'other' => [
            'name' => 'b',
        ],
    ]);

    assertEquals('a', $dto->name);
    assertEquals('b', $dto->other->name);
    assertNull($dto->other->other);
});
