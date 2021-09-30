<?php

namespace Spatie\DataTransferObject\Tests\Descriptors;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use ReflectionType;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;
use Spatie\DataTransferObject\Tests\Stubs\DataTransferObjects\PersonData;

class PropertyDescriptorTest extends TestCase
{
    public function test_single_property_type()
    {
        $descriptor = new PropertyDescriptor(
            new ReflectionProperty(PersonData::class, 'name')
        );

        $this->assertSame('name', $descriptor->getName());
        $this->assertInstanceOf(ReflectionProperty::class, $descriptor->getReflection());

        $this->assertContainsOnlyInstancesOf(ReflectionType::class, $descriptor->getTypes());
        $this->assertCount(1, $descriptor->getTypes());

        $this->assertSame(['string'], $descriptor->getTypeNames());

        $this->assertTrue($descriptor->hasType('string'));
        $this->assertFalse($descriptor->hasType('int'));

        $this->assertFalse($descriptor->isOptional());
    }

    public function test_union_property_type()
    {
        $descriptor = new PropertyDescriptor(
            new ReflectionProperty(PersonData::class, 'money')
        );

        $this->assertContainsOnlyInstancesOf(ReflectionType::class, $descriptor->getTypes());
        $this->assertCount(2, $descriptor->getTypes());

        $this->assertEqualsCanonicalizing(['int', 'float'], $descriptor->getTypeNames());

        $this->assertTrue($descriptor->hasType('int'));
        $this->assertTrue($descriptor->hasType('float'));
        $this->assertFalse($descriptor->hasType('string'));

        $this->assertFalse($descriptor->isOptional());
    }

    public function test_optional_properties()
    {
        $spouseDescriptor = new PropertyDescriptor(
            new ReflectionProperty(PersonData::class, 'spouse')
        );

        $childrenDescriptor = new PropertyDescriptor(
            new ReflectionProperty(PersonData::class, 'children')
        );

        $this->assertCount(1, $spouseDescriptor->getTypes());
        $this->assertTrue($spouseDescriptor->isOptional());

        $this->assertCount(3, $childrenDescriptor->getTypes());
        $this->assertTrue($childrenDescriptor->isOptional());
    }
}
