<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class MapFromTest extends TestCase
{
    /** @test */
    public function property_is_mapped_from_attribute_name()
    {
        $dto = new class (count: 42) extends DataTransferObject {
            #[MapFrom('count')]
            public int $originalCount;
        };

        $this->assertEquals(42, $dto->originalCount);
    }

    /** @test */
    public function property_is_mapped_from_index()
    {
        $dto = new class (['John', 'Doe']) extends DataTransferObject {
            #[MapFrom(1)]
            public string $lastName;
        };

        $this->assertEquals('Doe', $dto->lastName);
    }

    /** @test */
    public function property_is_mapped_from_dot_notation()
    {
        $dto = new class (['address' => ['city' => 'London']]) extends DataTransferObject {
            #[MapFrom('address.city')]
            public string $city;
        };

        $this->assertEquals('London', $dto->city);
    }

    /** @test */
    public function dto_can_have_mapped_and_regular_properties()
    {
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

        $this->assertEquals('Hello world', $dto->title);
        $this->assertEquals('John Doe', $dto->username);
        $this->assertEquals('john.doe@example.com', $dto->email);
        $this->assertEquals('2021-01-01', $dto->date);
        $this->assertEquals('News', $dto->categoryName);
    }

    /** @test */
    public function mapped_from_works_with_default_values()
    {
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

        $this->assertEquals('Hello world', $dto->title);
        $this->assertEquals('Test Text', $dto->description);
        $this->assertFalse($dto->isPublic);
        $this->assertEquals(42, $dto->randomInt);
    }

    /** @test */
    public function dto_can_have_numeric_keys()
    {
        $data = [
            'title' => 'Hello world',
            '0' => 10,
        ];

        $dto = new DTOInner($data);

        $this->assertEquals('Hello world', $dto->title);
        $this->assertEquals(10, $dto->zero);
    }

    /** @test */
    public function dto_can_have_numeric_keys_in_nested_dto()
    {
        $data = [
            'innerDTO' => [
                'title' => 'Hello world',
                '0' => 10,
            ],
        ];

        $dtoOuter = new class ($data) extends DataTransferObject {
            public DTOInner $innerDTO;
        };

        $this->assertEquals('Hello world', $dtoOuter->innerDTO->title);
        $this->assertEquals(10, $dtoOuter->innerDTO->zero);
    }
}

class DTOInner extends DataTransferObject
{
    public string $title;

    #[MapFrom('0')]
    public int $zero;
}
