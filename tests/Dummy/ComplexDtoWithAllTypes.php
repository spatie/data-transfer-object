<?php
declare(strict_types=1);
namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class ComplexDtoWithAllTypes extends DataTransferObject
{
    public string $string;
    public int $int;
    public float $float;
    public bool $bool;
    public BasicDto $basicDto;
}
