<?php

namespace Spatie\DataTransferObject\Tests\Casters;

use Error;
use ValueError;
use Spatie\DataTransferObject\Casters\EnumCaster;
use Spatie\DataTransferObject\Tests\Stubs\EnumCastedDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\Enums\IntegerEnum;
use Spatie\DataTransferObject\Tests\Stubs\Enums\SimpleEnum;
use Spatie\DataTransferObject\Tests\Stubs\Enums\StringEnum;
use Spatie\DataTransferObject\Tests\TestCase;

class EnumCasterTest extends TestCase
{
    public function test_it_cannot_cast_enum_with_wrong_value_type_given(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage(
            '"5" is not a valid backing value for enum "Spatie\DataTransferObject\Tests\Stubs\Enums\StringEnum"'
        );

        $descriptor = $this->getDescriptor(EnumCastedDataTransferObject::class, [
            'stringEnum' => 5,
        ]);

        $caster = new EnumCaster(enumType: StringEnum::class);
        $caster->cast(
            $descriptor->getProperty('stringEnum'),
            $descriptor->getArgument('stringEnum')
        );
    }

    public function test_it_can_cast_enums(): void
    {
        $descriptor = $this->getDescriptor(EnumCastedDataTransferObject::class, [
            'stringEnum' => 'test',
            'integerEnum' => 1,
        ]);

        $stringEnumCaster = new EnumCaster(enumType: StringEnum::class);
        $stringEnum = $stringEnumCaster->cast(
            $descriptor->getProperty('stringEnum'),
            $descriptor->getArgument('stringEnum')
        );

        $integerEnumCaster = new EnumCaster(enumType: IntegerEnum::class);
        $integerEnum = $integerEnumCaster->cast(
            $descriptor->getProperty('integerEnum'),
            $descriptor->getArgument('integerEnum')
        );

        $this->assertEquals(StringEnum::Test, $stringEnum);
        $this->assertEquals(IntegerEnum::Test, $integerEnum);
    }

    public function test_it_cannot_cast_simple_enums(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage(
            'Call to undefined method Spatie\DataTransferObject\Tests\Stubs\Enums\SimpleEnum::from()'
        );

        $descriptor = $this->getDescriptor(EnumCastedDataTransferObject::class, [
            'simpleEnum' => 5,
        ]);

        $caster = new EnumCaster(enumType: SimpleEnum::class);
        $caster->cast(
            $descriptor->getProperty('simpleEnum'),
            $descriptor->getArgument('simpleEnum')
        );
    }
}
