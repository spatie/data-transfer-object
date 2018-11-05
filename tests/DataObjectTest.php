<?php

namespace Spatie\DataObject\Tests;

use Spatie\DataObject\DataObject;
use Spatie\DataObject\DataObjectError;
use Spatie\DataObject\Tests\TestClasses\DummyClass;
use Spatie\DataObject\Tests\TestClasses\OtherClass;

class DataObjectTest extends TestCase
{
    /** @test */
    public function only_the_type_hinted_type_may_be_passed()
    {
        new class(['foo' => 'value']) extends DataObject {
            /** @var string */
            public $foo;
        };

        $this->markTestSucceeded();

        $this->expectException(DataObjectError::class);

        new class(['foo' => false]) extends DataObject {
            /** @var string */
            public $foo;
        };
    }

    /** @test */
    public function union_types_are_supported()
    {
        new class(['foo' => 'value']) extends DataObject {
            /** @var string|bool */
            public $foo;
        };

        new class(['foo' => false]) extends DataObject {
            /** @var string|bool */
            public $foo;
        };

        $this->markTestSucceeded();
    }

    /** @test */
    public function nullable_types_are_supported()
    {
        new class(['foo' => null]) extends DataObject {
            /** @var string|null */
            public $foo;
        };

        $this->markTestSucceeded();
    }

    /** @test */
    public function null_is_allowed_only_if_explicitly_specified()
    {
        $this->expectException(DataObjectError::class);

        new class(['foo' => null]) extends DataObject {
            /** @var string */
            public $foo;
        };
    }

    /** @test */
    public function unknown_properties_throw_an_error()
    {
        $this->expectException(DataObjectError::class);

        new class(['bar' => null]) extends DataObject {
        };
    }

    /** @test */
    public function unknown_properties_show_a_comprehensive_error_message()
    {
        try {
            new class(['foo' => null, 'bar' => null]) extends DataObject {
            };
        } catch (DataObjectError $error) {
            $this->assertTrue(strpos($error, '`foo`') !== false);
            $this->assertTrue(strpos($error, '`bar`') !== false);
        }
    }

    /** @test */
    public function only_returns_filtered_properties()
    {
        $valueObject = new class(['foo' => 1, 'bar' => 2]) extends DataObject {
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
        $valueObject = new class(['foo' => 1, 'bar' => 2]) extends DataObject {
            /** @var int */
            public $foo;

            /** @var int */
            public $bar;
        };

        $this->assertEquals(['foo' => 1], $valueObject->except('bar')->toArray());
    }

    /** @test */
    public function all_returns_all_properties()
    {
        $valueObject = new class(['foo' => 1, 'bar' => 2]) extends DataObject {
            /** @var int */
            public $foo;

            /** @var int */
            public $bar;
        };

        $this->assertEquals(['foo' => 1, 'bar' => 2], $valueObject->all());
    }

    /** @test */
    public function mixed_is_supported()
    {
        new class(['foo' => 'abc']) extends DataObject {
            /** @var mixed */
            public $foo;
        };

        new class(['foo' => 1]) extends DataObject {
            /** @var mixed */
            public $foo;
        };

        $this->markTestSucceeded();
    }

    /** @test */
    public function classes_are_supported()
    {
        new class(['foo' => new DummyClass()]) extends DataObject {
            /** @var \Spatie\DataObject\Tests\TestClasses\DummyClass */
            public $foo;
        };

        $this->markTestSucceeded();

        $this->expectException(DataObjectError::class);

        new class(['foo' => new class() {
        }]) extends DataObject {
            /** @var \Spatie\DataObject\Tests\TestClasses\DummyClass */
            public $foo;
        };
    }

    /** @test */
    public function generic_collections_are_supported()
    {
        new class(['foo' => [new DummyClass()]]) extends DataObject {
            /** @var \Spatie\DataObject\Tests\TestClasses\DummyClass[] */
            public $foo;
        };

        $this->markTestSucceeded();

        $this->expectException(DataObjectError::class);

        new class(['foo' => [new OtherClass()]]) extends DataObject {
            /** @var \Spatie\DataObject\Tests\TestClasses\DummyClass[] */
            public $foo;
        };
    }

    /** @test */
    public function an_exception_is_thrown_for_a_generic_collection_of_null()
    {
        $this->expectException(DataObjectError::class);

        new class(['foo' => [null]]) extends DataObject {
            /** @var string[] */
            public $foo;
        };
    }

    /** @test */
    public function an_exception_is_thrown_when_property_was_not_initialised()
    {
        $this->expectException(DataObjectError::class);

        new class([]) extends DataObject {
            /** @var string */
            public $foo;
        };
    }

    /** @test */
    public function empty_type_declaration_allows_everything()
    {
        new class(['foo' => new DummyClass()]) extends DataObject {
            public $foo;
        };

        new class(['foo' => null]) extends DataObject {
            public $foo;
        };

        new class(['foo' => null]) extends DataObject {
            /** This is a variable without type declaration */
            public $foo;
        };

        new class(['foo' => 1]) extends DataObject {
            public $foo;
        };

        $this->markTestSucceeded();
    }
}
