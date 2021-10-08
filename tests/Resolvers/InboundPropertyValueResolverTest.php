<?php

namespace Spatie\DataTransferObject\Tests\Resolvers;

use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Resolvers\InboundPropertyValueResolver;
use Spatie\DataTransferObject\Tests\Stubs\SimpleDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\StrictDataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class InboundPropertyValueResolverTest extends TestCase
{
    public function test_it_throws_singular_exception_when_a_property_does_not_exist_in_strict_mode(): void
    {
        $this->expectExceptionMessage(UnknownProperties::class);
        $this->expectExceptionMessage(
            'Data Transfer Object [Spatie\DataTransferObject\Tests\Stubs\StrictDataTransferObject] does not have the referenced property: ["badProp1"].'
        );

        $descriptor = $this->getDescriptor(StrictDataTransferObject::class, [
            'badProp1' => 'Not here',
        ]);

        $resolver = new InboundPropertyValueResolver();
        $resolver->resolve($descriptor);
    }

    public function test_it_throws_plural_exception_when_properties_do_not_exist_in_strict_mode(): void
    {
        $this->expectExceptionMessage(UnknownProperties::class);
        $this->expectExceptionMessage(
            'Data Transfer Object [Spatie\DataTransferObject\Tests\Stubs\StrictDataTransferObject] does not have the referenced properties: ["badProp1","badProp2"].'
        );

        $descriptor = $this->getDescriptor(StrictDataTransferObject::class, [
            'badProp1' => 'Not here',
            'badProp2' => 'Not here either',
        ]);

        $resolver = new InboundPropertyValueResolver();
        $resolver->resolve($descriptor);
    }

    public function test_it_does_not_throw_exceptions_when_not_strict(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class, [
            'badProp1' => 'Not here',
            'badProp2' => 'Not here either',
        ]);

        $resolver = new InboundPropertyValueResolver();
        $resolver->resolve($descriptor);

        $this->assertNull($descriptor->getProperty('badProp1'));
        $this->assertNull($descriptor->getProperty('badProp2'));
    }

    public function test_it_sets_values_correctly(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class, [
            'firstName' => 'Jimmy',
            'lastName' => 'Dean',
        ]);

        $resolver = new InboundPropertyValueResolver();
        $resolver->resolve($descriptor);

        $this->assertSame('Jimmy', $descriptor->getDataTransferObject()->firstName);
        $this->assertSame('Dean', $descriptor->getDataTransferObject()->lastName);
    }
}
