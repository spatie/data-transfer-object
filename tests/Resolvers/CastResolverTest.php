<?php

namespace Spatie\DataTransferObject\Tests\Resolvers;

use DateTime;
use PHPUnit\Framework\TestCase;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Resolvers\CastResolver;
use Spatie\DataTransferObject\Tests\Stubs\DataTransferObjects\CastData;

class CastResolverTest extends TestCase
{
    public function test_it_casts_properties()
    {
        $resolver = new CastResolver(
            new ClassDescriptor(CastData::class)
        );

        $arguments = $resolver->castArguments([
            'date' => '2021-09-30',
        ]);

        $this->assertEqualsCanonicalizing([
            'date' => new DateTime('2021-09-30'),
        ], $arguments);
    }
}
