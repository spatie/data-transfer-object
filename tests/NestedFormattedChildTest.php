<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class NestedFormattedChildTest extends TestCase
{
    /** @test */
    public function it_use_fromRequest_method()
    {
        $a = new NestedParentFormattedChild([
            'name' => 'parent',
            'child' => [
                'name' => 'child',
            ],
        ]);

        $this->assertEquals('CHILD', $a->child->name);
    }
}

class NestedParentFormattedChild extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\TestClasses\NestedFormattedChild */
    public $child;

    /** @var string */
    public $name;
}
