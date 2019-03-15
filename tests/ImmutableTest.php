<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObjectError;
use Spatie\DataTransferObject\Tests\TestClasses\TestDataTransferObject;

class ImmutableTest extends TestCase
{
    /** @test */
    public function it()
    {
        $dto = TestDataTransferObject::immutable([
            'testProperty' => 1
        ]);

        $this->assertEquals(1, $dto->testProperty);

        $this->expectException(DataTransferObjectError::class);

        $dto->testProperty = 2;
    }
}
