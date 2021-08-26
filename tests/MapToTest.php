<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Arr;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\DataTransferObject;

class MapToTest extends TestCase
{
    /** @test */
    public function property_is_mapped_to_attribute_name()
    {
        $dto = new class(originalCount: 42) extends DataTransferObject {
            #[MapTo('count')]
            public int $originalCount;
        };

        $this->assertEquals(42, $dto->toArray()['count']);
    }

    /** @test */
    public function dto_can_have_mapped_and_regular_properties()
    {
        $data = [
            'title' => 'Hello world',
            'username' => 'John Doe',
            'date' => '2021-01-01',
            'category' => [
                'name' => 'News',
            ],
        ];

        $dto = new class($data) extends DataTransferObject {
            public string $title;

            #[MapTo('author')]
            public string $username;

            public string $date;

            #[MapFrom('category.name')]
            public string $categoryName;
        };

        $dtoArray = $dto->toArray();
        $this->assertEquals('Hello world', $dtoArray['title']);
        $this->assertEquals('John Doe', $dtoArray['author']);
        $this->assertEquals('2021-01-01', $dtoArray['date']);
        $this->assertEquals('News', $dtoArray['categoryName']);
    }

    /** @test */
    public function dto_can_have_mapped_from_and_to_and_regular_properties()
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

        $dto = new class($data) extends DataTransferObject {
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
        $this->assertEquals('Hello world', $dtoArray['title']);
        $this->assertEquals('John Doe', $dtoArray['author']);
        $this->assertEquals('john.doe@example.com', $dtoArray['email']);
        $this->assertEquals('2021-01-01', $dtoArray['date']);
        $this->assertEquals('News', $dtoArray['categoryName']);
    }

    /** @test */
    public function mapped_property_can_be_except()
    {
        $dto = new class(originalCount: 42, villain: 'Johnny Lawrence') extends DataTransferObject {
            #[MapTo('count')]
            public int $originalCount;

            #[MapTo('hero')]
            public string $villain;
        };

        $dtoArray = $dto->except('count')->toArray();
        $this->assertEquals('Johnny Lawrence', $dtoArray['hero']);
        $this->assertFalse(Arr::exists($dtoArray, 'count'));
    }

    /** @test */
    public function mapped_property_can_be_only_exported()
    {
        $dto = new class(originalCount: 42, villain: 'Johnny Lawrence') extends DataTransferObject {
            #[MapTo('count')]
            public int $originalCount;

            #[MapTo('hero')]
            public string $villain;
        };

        $dtoArray = $dto->only('hero')->toArray();
        $this->assertEquals('Johnny Lawrence', $dtoArray['hero']);
        $this->assertFalse(Arr::exists($dtoArray, 'count'));
    }
}
