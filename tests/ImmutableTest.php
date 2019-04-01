<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectError;

class ImmutableTest extends TestCase
{
    /** @test */
    public function immutable_properties_may_not_be_overridden()
    {
        $dto = new class(['prop' => true]) extends DataTransferObject {
            /** @var bool|immutable */
            public $prop;
        };

        $this->assertTrue($dto->prop);

        $this->expectException(DataTransferObjectError::class);

        $dto->prop = false;
    }

    /** @test */
    public function only_immutable_type()
    {
        $dto = new class(['prop' => true]) extends DataTransferObject {
            /** @var immutable */
            public $prop;
        };

        $this->assertTrue($dto->prop);

        $this->expectException(DataTransferObjectError::class);

        $dto->prop = false;
    }

    /** @test */
    public function mutable_arrays_are_still_accessible()
    {
        $dto = new class([]) extends DataTransferObject {
            /** @var array */
            public $array = [];
        };

        $dto->array[] = 'abc';

        $this->assertEquals(['abc'], $dto->array);
    }
}
