<?php

namespace Spatie\DataTransferObject\Tests\Resolvers;

use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Resolvers\MapFromResolver;
use Spatie\DataTransferObject\Tests\Stubs\DataTransferObjects\MapFromData;
use Spatie\DataTransferObject\Tests\TestCase;

class MapFromResolverTest extends TestCase
{
    public function test_it_maps_successfully()
    {
        $resolver = new MapFromResolver(
            new ClassDescriptor(MapFromData::class)
        );

        $arguments = $resolver->mapArguments([
            'Set by an index',
            'attributeName' => 'Just an attribute',
            'array' => [
                'attribute' => 'Look, an array!',
            ],
        ]);

        $this->assertEqualsCanonicalizing([
            'indexMap' => 'Set by an index',
            'nameMap' => 'Just an attribute',
            'arrayMap' => 'Look, an array!',
        ], $arguments);
    }
}
