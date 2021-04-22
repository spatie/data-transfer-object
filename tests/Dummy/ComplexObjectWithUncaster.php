<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\Attributes\UncastWith;

#[UncastWith(ComplexObjectWithUncasterUncaster::class)]
class ComplexObjectWithUncaster
{
    public function __construct(
        public string $name,
    ) {
    }
}
