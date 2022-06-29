<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\Casters\NullableCaster;
use Spatie\DataTransferObject\DataTransferObject;
use TypeError;

class ImplicitCasterTest extends TestCase
{
    /** @test */
    public function cast_implicit_on_null_value()
    {
        $dto = new NullableStringDto(value: null);

        $this->assertEquals('foo', $dto->value);
    }

    /** @test */
    public function cast_implicit_on_populated_value()
    {
        $dto = new NullableStringDto(value: 'bar');

        $this->assertEquals('bar', $dto->value);
    }

    /** @test */
    public function does_not_fallback_on_empty_string()
    {
        $dto = new NullableStringDto(value: '');

        $this->assertEquals('', $dto->value);
    }

    /** @test */
    public function does_not_fallback_on_zero()
    {
        $dto = new NullableIntDto(value: 0);

        $this->assertEquals(0, $dto->value);
    }

    /** @test */
    public function does_not_fallback_on_false()
    {
        $dto = new NullableBoolDto(value: false);

        $this->assertEquals(false, $dto->value);
    }

    /** @test */
    public function does_not_fallback_on_empty_array()
    {
        $dto = new NullableArrayDto(value: []);

        $this->assertEquals([], $dto->value);
    }

    /** @test */
    public function does_not_cast_regular_caster_on_null_value()
    {
        $this->expectException(TypeError::class);

        new NotNullableValueDto(value: null);
    }
}

class NullableStringDto extends DataTransferObject
{
    #[CastWith(NullableCaster::class, default: 'foo')]
    public string $value;
}

class NullableIntDto extends DataTransferObject
{
    #[CastWith(NullableCaster::class, default: 3)]
    public int $value;
}

class NullableBoolDto extends DataTransferObject
{
    #[CastWith(NullableCaster::class, default: true)]
    public bool $value;
}

class NullableArrayDto extends DataTransferObject
{
    #[CastWith(NullableCaster::class, default: ['foo'])]
    public array $value;
}

class NotNullableValueDto extends DataTransferObject
{
    #[CastWith(RegularNullableCaster::class, default: 'foo')]
    public string $value;
}

class RegularNullableCaster implements Caster
{
    public function __construct(
        private array $types,
        private mixed $default
    ) {
    }

    public function cast(mixed $value): mixed
    {
        return $value ?: $this->default;
    }
}
