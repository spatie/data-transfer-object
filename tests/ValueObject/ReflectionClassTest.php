<?php

namespace Spatie\DataTransferObject\Tests\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectDefinition;
use Spatie\DataTransferObject\Tests\TestCase;
use Spatie\DataTransferObject\Tests\TestClasses\DummyClass;

class ReflectionClassTest extends TestCase
{
    /** @test */
    public function it_parses_use_statements()
    {
        $dataTransferObject = new class ([]) extends DataTransferObject {};

        $class = new DataTransferObjectDefinition($dataTransferObject);

        $this->assertEquals(DummyClass::class, $class->resolveAlias('DummyClass'));
    }
}
