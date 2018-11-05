<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObjectCollection;
use Spatie\DataTransferObject\Tests\TestClasses\TestDataTransferObject;

class DataTransferObjectCollectionTest extends TestCase
{
    /** @test */
    public function it_can_hold_value_objects_of_a_certain_type()
    {
        $objects = [
            new TestDataTransferObject(['testProperty' => 1]),
            new TestDataTransferObject(['testProperty' => 2]),
            new TestDataTransferObject(['testProperty' => 3]),
        ];

        $list = new class($objects) extends DataTransferObjectCollection {
        };

        $this->assertCount(3, $list);
    }
}
