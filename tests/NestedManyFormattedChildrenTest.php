<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class NestedManyFormattedChildrenTest extends TestCase
{
    /** @test */
    public function it_use_fromRequest_method()
    {
        $a = new NestedParentOfManyFormatted([
            'name' => 'parent',
            'children' => [
                ['name' => 'child_1'],
                ['name' => 'child_2'],
            ],
        ]);

        $this->assertEquals('CHILD_1', $a->children[0]->name);
    }
}

class NestedParentOfManyFormatted extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\TestClasses\NestedFormattedChild[] */
    public $children;

    /** @var string */
    public $name;
}
