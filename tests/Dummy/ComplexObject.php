<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\Dummy;

class ComplexObject
{
    public function __construct(
        public string $name,
    ) {
    }
}
