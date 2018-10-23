<?php

namespace Spatie\ValueObject\Tests;

use Spatie\ValueObject\ValueObject;
use Spatie\ValueObject\ValueObjectException;
use Spatie\ValueObject\Tests\TestClasses\DummyClass;

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

    /** @test */
    public function mixed_is_supported()
    {
        new class(['foo' => 'abc']) extends ValueObject {
            /** @var mixed */
            public $foo;
        };

        new class(['foo' => 1]) extends ValueObject {
            /** @var mixed */
            public $foo;
        };

        $this->markTestSucceeded();
    }

    /** @test */
    public function classes_are_supported()
    {
        new class(['foo' => new DummyClass()]) extends ValueObject {
            /** @var \Spatie\ValueObject\Tests\TestClasses\DummyClass */
            public $foo;
        };

        $this->markTestSucceeded();

        $this->expectException(ValueObjectException::class);

        new class(['foo' => new class() {
        }]) extends ValueObject {
            /** @var \Spatie\ValueObject\Tests\TestClasses\DummyClass */
            public $foo;
        };
    }

    /** @test */
    public function an_exception_is_thrown_when_property_was_not_initialised()
    {
        $this->expectException(ValueObjectException::class);

        new class([]) extends ValueObject {
            /** @var string */
            public $foo;
        };
    }

    /** @test */
    public function empty_type_declaration_allows_everything()
    {
        new class(['foo' => new DummyClass()]) extends ValueObject {
            public $foo;
        };

        new class(['foo' => null]) extends ValueObject {
            public $foo;
        };

        new class(['foo' => 1]) extends ValueObject {
            public $foo;
        };

        $this->markTestSucceeded();
    }
}
