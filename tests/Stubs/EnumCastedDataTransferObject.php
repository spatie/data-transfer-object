<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\EnumCaster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\Enums\IntegerEnum;
use Spatie\DataTransferObject\Tests\Stubs\Enums\SimpleEnum;
use Spatie\DataTransferObject\Tests\Stubs\Enums\StringEnum;

class EnumCastedDataTransferObject extends DataTransferObject
{
    #[CastWith(new EnumCaster(enumType: StringEnum::class))]
    public readonly StringEnum $stringEnum;

    #[CastWith(new EnumCaster(enumType: IntegerEnum::class))]
    public readonly IntegerEnum $integerEnum;

    #[CastWith(new EnumCaster(enumType: SimpleEnum::class))]
    public readonly SimpleEnum $simpleEnum;
}
