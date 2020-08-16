<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\PHPStan;

use Spatie\DataTransferObject\DataTransferObject;

class ExampleDTO extends DataTransferObject
{
    public int $id;
}

$dto = new ExampleDTO(['id' => 1]);
echo $dto->id, "\n";
