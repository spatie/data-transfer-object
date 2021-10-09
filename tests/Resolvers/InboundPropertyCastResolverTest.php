<?php

namespace Spatie\DataTransferObject\Tests\Resolvers;

use DateTime;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Resolvers\InboundPropertyCastResolver;
use Spatie\DataTransferObject\Tests\Stubs\ArrayCastedDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\SimpleDataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class InboundPropertyCastResolverTest extends TestCase
{
    public function test_it_casts_based_on_attributes(): void
    {
        $descriptor = $this->getDescriptor(ArrayCastedDataTransferObject::class, [
            'array' => [
                ['somevalue', 'someotherval'],
                ['differentarray'],
            ],
            'personArray' => [
                ['firstName' => 'Jay', 'lastName' => 'Gatsby'],
                ['firstName' => 'Nick', 'lastName' => 'Carraway'],
            ],
            'otherPersonData' => [
                ['firstName' => 'Daisy', 'lastName' => 'Buchanan'],
                ['firstName' => 'Ginevra', 'lastName' => 'King'],
            ],
            'dates' => [
                '1922-01-01',
                '1924-04-10',
            ],
        ]);

        $resolver = new InboundPropertyCastResolver();
        $resolver->resolve($descriptor);

        $this->assertIsArray($descriptor->getArgument('array'));

        $this->assertIsArray($descriptor->getArgument('personArray'));
        $this->assertContainsOnlyInstancesOf(SimpleDataTransferObject::class, $descriptor->getArgument('personArray'));

        $this->assertInstanceOf(Collection::class, $descriptor->getArgument('otherPersonData'));
        $this->assertContainsOnlyInstancesOf(SimpleDataTransferObject::class, $descriptor->getArgument('otherPersonData'));

        $this->assertIsArray($descriptor->getArgument('dates'));
        $this->assertContainsOnlyInstancesOf(DateTime::class, $descriptor->getArgument('dates'));
    }
}
