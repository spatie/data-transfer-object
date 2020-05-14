<?php

namespace Spatie\DataTransferObject\Tests;

use ReflectionClass;
use Spatie\DataTransferObject\PropertyFieldValidator;

class PropertyFieldValidatorTest extends TestCase
{
    /** @test */
    public function nullable()
    {
        [$class, $nullableProperty, $notNullableProperty] = $this->getClassAndProperties(new class() {
            public ?int $nullable;
            public int $notNullable;
        });

        $this->assertTrue((new PropertyFieldValidator($nullableProperty, $class))->isNullable);
        $this->assertFalse((new PropertyFieldValidator($notNullableProperty, $class))->isNullable);
    }

    /** @test */
    public function allowed_types()
    {
        [$class, $int, $a] = $this->getClassAndProperties(new class() {
            public int $int;
            public A $a;
        });

        $this->assertEquals(['integer'], (new PropertyFieldValidator($int, $class))->allowedTypes);
        $this->assertEquals([A::class], (new PropertyFieldValidator($a, $class))->allowedTypes);
    }

    /** @test */
    public function allowed_types_are_valid()
    {
        [$class, $int, $bool, $string, $float, $a] = $this->getClassAndProperties(new class() {
            public int $int;
            public bool $bool;
            public string $string;
            public float $float;
            public A $a;
        });

        $this->assertTrue((new PropertyFieldValidator($int, $class))->isValidType(1), "Failed asserting that '1' is a valid integer!");
        $this->assertTrue((new PropertyFieldValidator($bool, $class))->isValidType(true), "Failed asserting that 'true' is a valid boolean!");
        $this->assertTrue((new PropertyFieldValidator($string, $class))->isValidType('string'), "Failed asserting that 'string' is a valid string!");
        $this->assertTrue((new PropertyFieldValidator($float, $class))->isValidType(10.55), "Failed asserting that '10.55' is a valid float!");
        $this->assertTrue((new PropertyFieldValidator($a, $class))->isValidType(new A()), "Failed asserting that the object 'A' is a valid type!");
    }

    /** @test */
    public function empty_type_is_always_valid()
    {
        [$class, $property] = $this->getClassAndProperties(new class() {
            public $property;
        });

        $this->assertTrue((new PropertyFieldValidator($property, $class))->isValidType(1));
        $this->assertTrue((new PropertyFieldValidator($property, $class))->isValidType('a'));
        $this->assertTrue((new PropertyFieldValidator($property, $class))->isValidType(null));
    }

    /** @test */
    public function self_type_is_expanded()
    {
        [$class, $self] = $this->getClassAndProperties(new FooChild);

        $this->assertEquals([Foo::class], (new PropertyFieldValidator($self, $class))->allowedTypes);
        $this->assertTrue((new PropertyFieldValidator($self, $class))->isValidType(new FooChild));
        $this->assertTrue((new PropertyFieldValidator($self, $class))->isValidType(new Foo));
        $this->assertFalse((new PropertyFieldValidator($self, $class))->isValidType(new Bar));
    }

    private function getClassAndProperties(object $class): array
    {
        $reflectionClass = new ReflectionClass($class);

        return [$reflectionClass, ...$reflectionClass->getProperties()];
    }
}

class A
{
}
