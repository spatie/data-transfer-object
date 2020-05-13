<?php

namespace Spatie\DataTransferObject\Tests;

use ReflectionClass;
use Spatie\DataTransferObject\PropertyFieldValidator;

class PropertyFieldValidatorTest extends TestCase
{
    /** @test */
    public function nullable()
    {
        [$nullableProperty, $notNullableProperty] = $this->getProperties(new class() {
            public ?int $nullable;
            public int $notNullable;
        });

        $this->assertTrue((new PropertyFieldValidator($nullableProperty))->isNullable);
        $this->assertFalse((new PropertyFieldValidator($notNullableProperty))->isNullable);
    }

    /** @test */
    public function allowed_types()
    {
        [$int, $a] = $this->getProperties(new class() {
            public int $int;
            public A $a;
        });

        $this->assertEquals(['integer'], (new PropertyFieldValidator($int))->allowedTypes);
        $this->assertEquals([A::class], (new PropertyFieldValidator($a))->allowedTypes);
    }

    /** @test */
    public function allowed_types_are_valid()
    {
        [$int, $bool, $string, $float, $a] = $this->getProperties(new class() {
            public int $int;
            public bool $bool;
            public string $string;
            public float $float;
            public A $a;
        });

        $this->assertTrue((new PropertyFieldValidator($int))->isValidType(1), "Failed asserting that '1' is a valid integer!");
        $this->assertTrue((new PropertyFieldValidator($bool))->isValidType(true), "Failed asserting that 'true' is a valid boolean!");
        $this->assertTrue((new PropertyFieldValidator($string))->isValidType('string'), "Failed asserting that 'string' is a valid string!");
        $this->assertTrue((new PropertyFieldValidator($float))->isValidType(10.55), "Failed asserting that '10.55' is a valid float!");
        $this->assertTrue((new PropertyFieldValidator($a))->isValidType(new A()), "Failed asserting that the object 'A' is a valid type!");
    }

    /** @test */
    public function empty_type_is_always_valid()
    {
        [$property] = $this->getProperties(new class() {
            public $property;
        });

        $this->assertTrue((new PropertyFieldValidator($property))->isValidType(1));
        $this->assertTrue((new PropertyFieldValidator($property))->isValidType('a'));
        $this->assertTrue((new PropertyFieldValidator($property))->isValidType(null));
    }

    private function getProperties(object $class): array
    {
        $reflectionClass = new ReflectionClass($class);

        return $reflectionClass->getProperties();
    }
}

class A
{
}
