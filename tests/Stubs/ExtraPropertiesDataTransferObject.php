<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use Spatie\DataTransferObject\DataTransferObject;

class ExtraPropertiesDataTransferObject extends DataTransferObject
{
    public static string $staticProperty;

    public string $writeableProperty;

    public readonly int $integer;

    public readonly float $float;

    public readonly string $string;
}
