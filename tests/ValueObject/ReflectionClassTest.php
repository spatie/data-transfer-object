<?php

namespace Spatie\ValueObject\Tests\ValueObject;

use Spatie\ValueObject\ValueObject;
use Spatie\ValueObject\ValueObjectDefinition;
use Spatie\ValueObject\Tests\TestCase;
use Spatie\ValueObject\Tests\TestClasses\DummyClass;

class ReflectionClassTest extends TestCase
{
    /** @test */
    public function it_parses_use_statements()
    {
        $valueObject = new class ([]) extends ValueObject {};

        $class = new ValueObjectDefinition($valueObject);

        $this->assertEquals(DummyClass::class, $class->resolveAlias('DummyClass'));
    }
}
