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

        $this->assertEquals(['int'], (new PropertyFieldValidator($int))->allowedTypes);
        $this->assertEquals([A::class], (new PropertyFieldValidator($a))->allowedTypes);
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

class A {}
