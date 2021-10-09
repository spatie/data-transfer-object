<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use DateTime;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class ArrayCastedDataTransferObject extends DataTransferObject
{
   #[CastWith(new ArrayCaster(itemType: 'array'))]
    public readonly array $array;

   #[CastWith(new ArrayCaster(itemType: SimpleDataTransferObject::class))]
   public readonly array $personArray;

   #[CastWith(new ArrayCaster(itemType: SimpleDataTransferObject::class))]
   public readonly Collection $otherPersonData;

   #[CastWith(new ArrayCaster(itemType: DateTime::class))]
   public readonly array $dates;
}
