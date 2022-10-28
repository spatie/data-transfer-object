<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;

beforeAll(function () {
    class DTOInner extends DataTransferObject
    {
        public string $title;

        #[MapFrom('0')]
        public int $zero;
    }
});

test('property is mapped from attribute name', function () {
    $dto = new class (count: 42) extends DataTransferObject {
        #[MapFrom('count')]
        public int $originalCount;
    };

    assertEquals(42, $dto->originalCount);
});

test('property is mapped from index', function () {
    $dto = new class (['John', 'Doe']) extends DataTransferObject {
        #[MapFrom(1)]
        public string $lastName;
    };

    assertEquals('Doe', $dto->lastName);
});

test('property is mapped from dot notation', function () {
    $dto = new class (['address' => ['city' => 'London']]) extends DataTransferObject {
        #[MapFrom('address.city')]
        public string $city;
    };

    assertEquals('London', $dto->city);
});

test('dto can have mapped and regular properties', function () {
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
        public string $username;

        #[MapFrom('user.email')]
        public string $email;

        public string $date;

        #[MapFrom('category.name')]
        public string $categoryName;
    };

    assertEquals('Hello world', $dto->title);
    assertEquals('John Doe', $dto->username);
    assertEquals('john.doe@example.com', $dto->email);
    assertEquals('2021-01-01', $dto->date);
    assertEquals('News', $dto->categoryName);
});

test('mapped from works with default values', function () {
    $data = [
        'title' => 'Hello world',
    ];

    $dto = new class ($data) extends DataTransferObject {
        public string $title;

        #[MapFrom('desc')]
        public string $description = 'Test Text';

        #[MapFrom('is_public')]
        public bool $isPublic = false;

        #[MapFrom('random_int')]
        public int $randomInt = 42;
    };

    assertEquals('Hello world', $dto->title);
    assertEquals('Test Text', $dto->description);
    assertFalse($dto->isPublic);
    assertEquals(42, $dto->randomInt);
});

test('dto can have numeric keys', function () {
    $data = [
        'title' => 'Hello world',
        '0' => 10,
    ];

    $dto = new DTOInner($data);

    assertEquals('Hello world', $dto->title);
    assertEquals(10, $dto->zero);
});

test('dto can have numeric keys in nested dto', function () {
    $data = [
        'innerDTO' => [
            'title' => 'Hello world',
            '0' => 10,
        ],
    ];

    $dtoOuter = new class ($data) extends DataTransferObject {
        public DTOInner $innerDTO;
    };

    assertEquals('Hello world', $dtoOuter->innerDTO->title);
    assertEquals(10, $dtoOuter->innerDTO->zero);
});
