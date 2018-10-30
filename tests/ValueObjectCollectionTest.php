<?php

namespace Spatie\ValueObject\Tests;

use Spatie\ValueObject\ValueObjectCollection;
use Spatie\ValueObject\Tests\TestClasses\TestValueObject;

class ValueObjectCollectionTest extends TestCase
{
    /** @test */
    public function it_can_hold_value_objects_of_a_certain_type()
    {
        $objects = [
            new TestValueObject(['testProperty' => 1]),
            new TestValueObject(['testProperty' => 2]),
            new TestValueObject(['testProperty' => 3]),
        ];

        $list = new class($objects) extends ValueObjectCollection {
        };

        $this->assertCount(3, $list);
    }
}
