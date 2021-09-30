<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class MapFromTest extends TestCase
{
    /** @test */
    public function property_is_mapped_from_attribute_name()
    {
        $dto = new class(count: 42) extends AnonymousDataTransferObject {
            #[MapFrom('count')]
            public int $originalCount;
        };

        $this->assertEquals(42, $dto->originalCount);
    }

    /** @test */
    public function property_is_mapped_from_index()
    {
        $dto = new class(['John', 'Doe']) extends AnonymousDataTransferObject {
            #[MapFrom(1)]
            public string $lastName;
        };

        $this->assertEquals('Doe', $dto->lastName);
    }

    /** @test */
    public function property_is_mapped_from_dot_notation()
    {
        $dto = new class(['address' => ['city' => 'London']]) extends AnonymousDataTransferObject {
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

        $dto = new class($data) extends AnonymousDataTransferObject {
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
}
