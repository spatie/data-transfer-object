<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectError;
use Spatie\DataTransferObject\Tests\TestClasses\TestDataTransferObject;

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
    public function a_dto_can_be_made_immutable_as_a_whole()
    {
        $dto = TestDataTransferObject::immutable([
            'testProperty' => 1,
        ]);

        $this->assertEquals(1, $dto->testProperty);

        $this->expectException(DataTransferObjectError::class);

        $dto->testProperty = 2;
    }
}
