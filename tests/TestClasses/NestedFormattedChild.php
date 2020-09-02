<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class NestedFormattedChild extends DataTransferObject
{
    /** @var string */
    public $name;

    public static function fromRequest($data): self
    {
        return new self([
            'name' => strtoupper($data['name']),
        ]);
    }
}
