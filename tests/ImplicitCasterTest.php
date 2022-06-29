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
        $dto = new NullableValueDto(name: null);

        $this->assertEquals('foo', $dto->name);
    }

    /** @test */
    public function cast_implicit_on_populated_value()
    {
        $dto = new NullableValueDto(name: 'bar');

        $this->assertEquals('bar', $dto->name);
    }

    /** @test */
    public function does_not_cast_regular_caster_on_null_value()
    {
        $this->expectException(TypeError::class);

        new NotNullableValueDto(name: null);
    }
}

class NullableValueDto extends DataTransferObject
{
    #[CastWith(NullableCaster::class, default: 'foo')]
    public string $name;
}

class NotNullableValueDto extends DataTransferObject
{
    #[CastWith(RegularNullableCaster::class, default: 'foo')]
    public string $name;
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
