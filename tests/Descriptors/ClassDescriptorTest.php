<?php

namespace Spatie\DataTransferObject\Tests\Descriptors;

use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;
use Spatie\DataTransferObject\Tests\Stubs\ExtraPropertiesDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\SimpleDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\StrictDataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class ClassDescriptorTest extends TestCase
{
    public function test_it_can_manipulate_arguments(): void
    {
        $arguments = [
            'firstName' => 'Dan',
            'lastName' => 'Johnson',
        ];

        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class, $arguments);

        $this->assertEquals(new Collection($arguments), $descriptor->getArguments());

        $arguments = new Collection([
            'firstName' => 'James',
            'lastName' => 'Johnson',
        ]);

        $descriptor->setArguments($arguments);

        $this->assertEquals($arguments, $descriptor->getArguments());
    }

    public function test_it_can_get_attributes(): void
    {
        $descriptor = $this->getDescriptor(StrictDataTransferObject::class);

        $this->assertInstanceOf(Collection::class, $descriptor->getAttributes());
        $this->assertInstanceOf(Strict::class, $descriptor->getAttributes()->first());
    }

    public function test_it_can_get_specific_attribute(): void
    {
        $descriptor = $this->getDescriptor(StrictDataTransferObject::class);

        $this->assertInstanceOf(Strict::class, $descriptor->getAttribute(Strict::class));
    }

    public function test_it_can_get_data_transfer_object(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class);

        $this->assertInstanceOf(SimpleDataTransferObject::class, $descriptor->getDataTransferObject());
    }

    public function test_it_can_get_fqdn(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class);

        $this->assertSame(
            "Spatie\\DataTransferObject\\Tests\\Stubs\\SimpleDataTransferObject",
            $descriptor->getFqdn()
        );
    }

    public function test_it_can_get_properties(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class);

        $this->assertInstanceOf(Collection::class, $descriptor->getProperties());
        $this->assertCount(2, $descriptor->getProperties());
        $this->assertContainsOnlyInstancesOf(PropertyDescriptor::class, $descriptor->getProperties());
    }

    public function test_it_can_get_specific_properties(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class);

        $this->assertInstanceOf(PropertyDescriptor::class, $descriptor->getProperty('firstName'));
        $this->assertInstanceOf(PropertyDescriptor::class, $descriptor->getProperty('lastName'));

        $this->assertEquals('firstName', $descriptor->getProperty('firstName')->getName());
        $this->assertEquals('lastName', $descriptor->getProperty('lastName')->getName());

        $this->assertNull($descriptor->getProperty('unknownProperty'));
    }

    public function test_it_can_get_reflection_class(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class);

        $this->assertInstanceOf(ReflectionClass::class, $descriptor->getReflection());
        $this->assertSame(
            "Spatie\\DataTransferObject\\Tests\\Stubs\\SimpleDataTransferObject",
            $descriptor->getReflection()->getName()
        );
    }

    public function test_it_can_determine_strictness(): void
    {
        $relaxedDescriptor = $this->getDescriptor(SimpleDataTransferObject::class);
        $strictDescriptor = $this->getDescriptor(StrictDataTransferObject::class);

        $this->assertFalse($relaxedDescriptor->isStrict());
        $this->assertTrue($strictDescriptor->isStrict());
    }

    public function test_it_will_not_resolve_static_properties(): void
    {
        $descriptor = $this->getDescriptor(ExtraPropertiesDataTransferObject::class);

        $this->assertNull($descriptor->getProperty('staticProperty'));
        $this->assertCount(3, $descriptor->getProperties());
    }

    public function test_it_will_not_resolve_writeable_properties(): void
    {
        $descriptor = $this->getDescriptor(ExtraPropertiesDataTransferObject::class);

        $this->assertNull($descriptor->getProperty('writeableProperty'));
        $this->assertCount(3, $descriptor->getProperties());
    }
}
