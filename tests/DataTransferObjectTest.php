<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Stubs\SimpleDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\StrictDataTransferObject;

class DataTransferObjectTest extends TestCase
{
    public function test_it_can_make_data_transfer_object_from_named_arguments_without_validation(): void
    {
        $dto = SimpleDataTransferObject::newWithoutValidation(
            firstName: 'Clark',
            lastName: 'Kent'
        );

        $this->assertSame('Clark', $dto->firstName);
        $this->assertSame('Kent', $dto->lastName);
    }

    public function test_it_can_make_data_transfer_object_from_array_without_validation(): void
    {
        $dto = SimpleDataTransferObject::newWithoutValidation([
            'firstName' => 'Peter',
            'lastName' => 'Parker',
        ]);

        $this->assertSame('Peter', $dto->firstName);
        $this->assertSame('Parker', $dto->lastName);
    }

    public function test_it_can_make_strict_data_transfer_object_without_validation(): void
    {
        $dto = StrictDataTransferObject::newWithoutValidation(
            firstName: 'Steve',
            lastName: 'Rogers'
        );

        $this->assertSame('Steve', $dto->firstName);
        $this->assertSame('Rogers', $dto->lastName);
    }

    public function test_it_can_make_data_transfer_object(): void
    {
        $dto = StrictDataTransferObject::new(
            firstName: 'Tony',
            lastName: 'Stark'
        );

        $this->assertSame('Tony', $dto->firstName);
        $this->assertSame('Stark', $dto->lastName);
    }
}
