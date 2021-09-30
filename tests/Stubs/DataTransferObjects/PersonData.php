<?php

namespace Spatie\DataTransferObject\Tests\Stubs\DataTransferObjects;

use ArrayAccess;
use Spatie\DataTransferObject\DataTransferObject;

class PersonData extends DataTransferObject
{
    public string $name;

    public int $age;

    public ?PersonData $spouse;

    public null|array|ArrayAccess $children;

    public float|int $money;
}
