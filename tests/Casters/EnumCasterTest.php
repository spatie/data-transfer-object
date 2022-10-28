<?php

namespace Spatie\DataTransferObject\Tests\Casters;

use LogicException;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\EnumCaster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\IntegerEnum;
use Spatie\DataTransferObject\Tests\Stubs\SimpleEnum;
use Spatie\DataTransferObject\Tests\Stubs\StringEnum;
use function PHPUnit\Framework\assertEquals;

beforeAll(function () {
    class EnumCastedDataTransferObject extends DataTransferObject
    {
        #[CastWith(EnumCaster::class, StringEnum::class)]
        public ?StringEnum $stringEnum;

        #[CastWith(EnumCaster::class, IntegerEnum::class)]
        public ?IntegerEnum $integerEnum;

        #[CastWith(EnumCaster::class, SimpleEnum::class)]
        public ?SimpleEnum $simpleEnum;
    }
});

/** @requires PHP >= 8.1 */
test('it cannot cast enum with wrong value type given', function () {
    new EnumCastedDataTransferObject([
        'stringEnum' => 5,
    ]);
})->throws(LogicException::class, 'Couldn\'t cast enum [' . StringEnum::class . '] with value [' . 5 . ']');

/** @requires PHP >= 8.1 */
test('it can cast enums', function () {
    $dto = new EnumCastedDataTransferObject([
        'integerEnum' => 1,
        'stringEnum' => 'test',
    ]);

    assertEquals(StringEnum::Test, $dto->stringEnum);
    assertEquals(IntegerEnum::Test, $dto->integerEnum);
});

/** @requires PHP >= 8.1 */
test('it can cast enums which are already enums', function () {
    $dto = new EnumCastedDataTransferObject([
        'integerEnum' => IntegerEnum::Test,
        'stringEnum' => StringEnum::Test,
    ]);

    assertEquals(StringEnum::Test, $dto->stringEnum);
    assertEquals(IntegerEnum::Test, $dto->integerEnum);
});

/** @requires PHP >= 8.1 */
test('it cannot cast simple enums', function () {
    new EnumCastedDataTransferObject([
        'simpleEnum' => 5,
    ]);
})->throws(LogicException::class, 'Caster [EnumCaster] may only be used to cast backed enums. Received [' . SimpleEnum::class . '].');
