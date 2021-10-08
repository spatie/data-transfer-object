<?php

namespace Spatie\DataTransferObject\Tests\Resolvers;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Resolvers\InboundMappedArgumentResolver;
use Spatie\DataTransferObject\Tests\Stubs\MappedDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\SimpleDataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class InboundMappedArgumentResolverTest extends TestCase
{
    public function test_it_does_not_map_arguments_without_map_from_attribute(): void
    {
        $descriptor = $this->getDescriptor(SimpleDataTransferObject::class, [
            'firstName' => 'Harold',
            'lastName' => 'Lisby',
        ]);

        $resolver = new InboundMappedArgumentResolver();
        $resolver->resolve($descriptor);

        $this->assertEquals(
            new Collection([
                'firstName' => 'Harold',
                'lastName' => 'Lisby',
            ]),
            $descriptor->getArguments()
        );
    }

    public function test_it_maps_arguments_correctly(): void
    {
        $descriptor = $this->getDescriptor(MappedDataTransferObject::class, [
            'I was mapped from a key.',
            'attribute' => 'I was mapped from an attribute!',
            'nested' => [
                'attribute' => 'But I was mapped from a nested attribute. 😎',
            ],
        ]);

        $resolver = new InboundMappedArgumentResolver();
        $resolver->resolve($descriptor);

        $this->assertEquals(
            new Collection([
                'mappedFromKey' => 'I was mapped from a key.',
                'mappedFromAttribute' => 'I was mapped from an attribute!',
                'mappedFromNested' => 'But I was mapped from a nested attribute. 😎',
            ]),
            $descriptor->getArguments()
        );
    }
}
