<?php

namespace Spatie\DataObject\Tests;

use Spatie\DataObject\DataObjectCollection;
use Spatie\DataObject\Tests\TestClasses\TestDataObject;

class DataObjectCollectionTest extends TestCase
{
    /** @test */
    public function it_can_hold_value_objects_of_a_certain_type()
    {
        $objects = [
            new TestDataObject(['testProperty' => 1]),
            new TestDataObject(['testProperty' => 2]),
            new TestDataObject(['testProperty' => 3]),
        ];

        $list = new class($objects) extends DataObjectCollection {
        };

        $this->assertCount(3, $list);
    }
}
