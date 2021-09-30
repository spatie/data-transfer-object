<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Reflection\DataTransferObjectClass;

class AnonymousDataTransferObject extends DataTransferObject
{
    public function __construct(...$args)
    {
        $class = new DataTransferObjectClass($this);

        $this
            ->setUp($class, ...$args)
            ->validate($class);
    }
}
