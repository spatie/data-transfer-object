<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\DefaultCast;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * @requires PHP >= 8.1
 */
class IntersectionDtoTest81 extends TestCase
{
    /** @test */
    public function intersection_types_are_allowed()
    {
        $value = new SomeFooBar;

        $dto = new DtoWithIntersectionTypeProp(foobar: $value);

        $this->assertEquals($value, $dto->foobar);
    }

    /** @test */
    public function when_intersection_type_is_set_as_target_class_for_default_caster_the_value_is_cast_using_said_caster()
    {
        new DtoWithDefaultCastOnIntersectionTypeProp(foobar: 'to-be-cast');

        $this->markTestSucceeded();
    }
}

class SomeFoo {};
interface SomeBar {};
class SomeFooBar extends SomeFoo implements SomeBar {};

class DtoWithIntersectionTypeProp extends DataTransferObject
{
    public SomeFoo & SomeBar $foobar;
}

class SomeFooAndSomeBarCaster implements Caster
{
    public function cast(mixed $value): SomeFooBar
    {
        return new SomeFooBar;
    }
}

#[DefaultCast(SomeFoo::class.'&'.SomeBar::class, SomeFooAndSomeBarCaster::class)]
class DtoWithDefaultCastOnIntersectionTypeProp extends DataTransferObject
{
    public SomeFoo & SomeBar $foobar;
}
