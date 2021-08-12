<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class MapFromTest extends TestCase
{
    /** @test */
    public function property_is_mapped_from_attribute_name()
    {
        $dto = new class(count: 42) extends DataTransferObject {
            #[MapFrom('count')]
            public int $originalCount;
        };

        $this->assertEquals(42, $dto->originalCount);
    }

    /** @test */
    public function property_is_mapped_from_index()
    {
        $dto = new class(['John', 'Doe']) extends DataTransferObject {
            #[MapFrom(1)]
            public string $lastName;
        };

        $this->assertEquals('Doe', $dto->lastName);
    }

    /** @test */
    public function property_is_mapped_from_dot_notation()
    {
        $dto = new class(['address' => ['city' => 'London']]) extends DataTransferObject {
            #[MapFrom('address.city')]
            public string $city;
        };

        $this->assertEquals('London', $dto->city);
    }
}
