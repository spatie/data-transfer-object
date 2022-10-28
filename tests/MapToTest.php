<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Arr;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;

test('property is mapped to attribute name', function () {
    $dto = new class (originalCount: 42) extends DataTransferObject {
        #[MapTo('count')]
        public int $originalCount;
    };

    assertEquals(42, $dto->toArray()['count']);
});

test('dto can have mapped and regular properties', function () {
    $data = [
        'title' => 'Hello world',
        'username' => 'John Doe',
        'date' => '2021-01-01',
        'category' => [
            'name' => 'News',
        ],
    ];

    $dto = new class ($data) extends DataTransferObject {
        public string $title;

        #[MapTo('author')]
        public string $username;

        public string $date;

        #[MapFrom('category.name')]
        public string $categoryName;
    };

    $dtoArray = $dto->toArray();
    assertEquals('Hello world', $dtoArray['title']);
    assertEquals('John Doe', $dtoArray['author']);
    assertEquals('2021-01-01', $dtoArray['date']);
    assertEquals('News', $dtoArray['categoryName']);
});

test('dto can have mapped from and to and regular properties', function () {
    $data = [
        'title' => 'Hello world',
        'user' => [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ],
        'date' => '2021-01-01',
        'category' => [
            'name' => 'News',
        ],
    ];

    $dto = new class ($data) extends DataTransferObject {
        public string $title;

        #[MapFrom('user.name')]
        #[MapTo('author')]
        public string $username;

        #[MapFrom('user.email')]
        public string $email;

        public string $date;

        #[MapFrom('category.name')]
        public string $categoryName;
    };

    $dtoArray = $dto->toArray();
    assertEquals('Hello world', $dtoArray['title']);
    assertEquals('John Doe', $dtoArray['author']);
    assertEquals('john.doe@example.com', $dtoArray['email']);
    assertEquals('2021-01-01', $dtoArray['date']);
    assertEquals('News', $dtoArray['categoryName']);
});

test('mapped property can be except', function () {
    $dto = new class (originalCount: 42, villain: 'Johnny Lawrence') extends DataTransferObject {
        #[MapTo('count')]
        public int $originalCount;

        #[MapTo('hero')]
        public string $villain;
    };

    $dtoArray = $dto->except('count')->toArray();
    assertEquals('Johnny Lawrence', $dtoArray['hero']);
    assertFalse(Arr::exists($dtoArray, 'count'));
});

test('mapped property can be only exported', function () {
    $dto = new class (originalCount: 42, villain: 'Johnny Lawrence') extends DataTransferObject {
        #[MapTo('count')]
        public int $originalCount;

        #[MapTo('hero')]
        public string $villain;
    };

    $dtoArray = $dto->only('hero')->toArray();
    assertEquals('Johnny Lawrence', $dtoArray['hero']);
    assertFalse(Arr::exists($dtoArray, 'count'));
});
