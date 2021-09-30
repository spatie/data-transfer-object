<?php

namespace Spatie\DataTransferObject\Tests\Stubs\DataTransferObjects;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\DataTransferObject;

class MapFromData extends DataTransferObject
{
    #[MapFrom(0)]
    public string $indexMap;

    #[MapFrom('attributeName')]
    public string $nameMap;

    #[MapFrom('array.attribute')]
    public string $arrayMap;
}
