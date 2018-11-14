<?php

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class NestedParentCollection extends DataTransferObjectCollection
{
    public function current(): NestedChildCollection
    {
        return parent::current();
    }
}
