<?php

namespace Spatie\ValueObject\Tests;

use Spatie\ValueObject\ValueObject;
use Spatie\ValueObject\ValueObjectException;

class ValueObjectTest extends TestCase
{
    /** @test */
    public function only_the_type_hinted_type_may_be_passed()
    {
        new class(['foo' => 'value']) extends ValueObject {
            /** @var string */
            public $foo;
        };

        $this->markTestSucceeded();

        $this->expectException(ValueObjectException::class);

        new class(['foo' => false]) extends ValueObject {
            /** @var string */
            public $foo;
        };
    }

    /** @test */
    public function union_types_are_supported()
    {
        new class(['foo' => 'value']) extends ValueObject {
            /** @var string|bool */
            public $foo;
        };

        new class(['foo' => false]) extends ValueObject {
            /** @var string|bool */
            public $foo;
        };

        $this->markTestSucceeded();
    }

    /** @test */
    public function nullable_types_are_supported()
    {
        new class(['foo' => null]) extends ValueObject {
            /** @var string|null */
            public $foo;
        };

        $this->markTestSucceeded();
    }

    /** @test */
    public function unknown_properties_throw_an_error()
    {
        $this->expectException(ValueObjectException::class);

        new class(['bar' => null]) extends ValueObject {
        };
    }

    /** @test */
    public function it_supports_fillable_properties()
    {
        $valueObject = new class(['foo' => 1, 'bar' => 2]) extends ValueObject {
            /** @var int */
            public $foo;

            /** @var int */
            public $bar;

            protected $fillable = [
                'foo',
            ];
        };

        $this->assertEquals(['foo' => 1], $valueObject->fillable()->toArray());
    }

    /** @test */
    public function only_returns_filtered_properties()
    {
        $valueObject = new class(['foo' => 1, 'bar' => 2]) extends ValueObject {
            /** @var int */
            public $foo;

            /** @var int */
            public $bar;
        };

        $this->assertEquals(['foo' => 1], $valueObject->only('foo')->toArray());
    }

    /** @test */
    public function except_returns_filtered_properties()
    {
        $valueObject = new class(['foo' => 1, 'bar' => 2]) extends ValueObject {
            /** @var int */
            public $foo;

            /** @var int */
            public $bar;
        };

        $this->assertEquals(['foo' => 1], $valueObject->except('bar')->toArray());
    }
}
