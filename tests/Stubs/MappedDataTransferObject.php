<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class MappedDataTransferObject extends DataTransferObject
{
    #[MapFrom(0)]
    public readonly string $mappedFromKey;

    #[MapFrom('attribute')]
    public readonly string $mappedFromAttribute;

    #[MapFrom('nested.attribute')]
    public readonly string $mappedFromNested;
}
