<?php

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class NestedCollection extends DataTransferObjectCollection
{
    public function current(): NestedParent
    {
        return parent::current();
    }
}
