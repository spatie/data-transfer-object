<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\Attributes\CastWith;

#[CastWith(ComplexObjectWithCasterCaster::class)]
class ComplexObjectWithCaster
{
    public function __construct(
        public string $name,
    ) {
    }
}
