<?php

namespace Spatie\DataTransferObject\Tests\Descriptors;

use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;
use Spatie\DataTransferObject\Tests\Stubs\DataTransferObjects\PersonData;
use stdClass;

class ClassDescriptorTest extends TestCase
{
    public function test_it_cannot_create_class_descriptor_with_non_dto()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Spatie\DataTransferObject\Descriptors\ClassDescriptor can only describe classes that extend Spatie\DataTransferObject\DataTransferObject.'
        );

        new ClassDescriptor(stdClass::class);
    }

    public function test_it_can_create_class_descriptor_with_dto()
    {
        $descriptor = new ClassDescriptor(PersonData::class);

        $this->assertInstanceOf(ClassDescriptor::class, $descriptor);
        $this->assertSame(PersonData::class, $descriptor->getClassFqdn());
        $this->assertInstanceOf(ReflectionClass::class, $descriptor->getReflection());
    }

    public function test_it_can_get_property_descriptors()
    {
        $descriptor = new ClassDescriptor(PersonData::class);

        $this->assertCount(5, $descriptor->getProperties());
        $this->assertContainsOnlyInstancesOf(PropertyDescriptor::class, $descriptor->getProperties());
    }

    public function test_it_can_get_property_descriptor_by_name()
    {
        $descriptor = new ClassDescriptor(PersonData::class);

        $this->assertInstanceOf(PropertyDescriptor::class, $descriptor->getPropertyByName('money'));
    }

    public function test_it_cannot_get_non_existent_property_by_name()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Property with the name [foo] does not exist on ' . PersonData::class . '.');

        (new ClassDescriptor(PersonData::class))->getPropertyByName('foo');
    }
}
