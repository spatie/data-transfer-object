<?php

namespace Spatie\DataTransferObject\Tests;

use Attribute;
use DateTimeImmutable;
use InvalidArgumentException;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\Uncaster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Attributes\DefaultCast;
use Spatie\DataTransferObject\Attributes\DefaultUncast;

class DefaultUncasterTest extends TestCase
{
    /** @test */
    public function property_is_uncasted()
    {
        $dto = new DtoWithDefaultUncaster(date: DateTimeImmutable::createFromFormat('Y-m-d', '2020-01-01'));

        $this->assertEquals(['date' => '2020-01-01'], $dto->toArray());
    }
}

#[DefaultUncast(DateTimeImmutable::class, DateTimeImmutableUncaster::class)]
class DtoWithDefaultUncaster extends DataTransferObject
{
    public DateTimeImmutable $date;
}

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class DateTimeImmutableUncaster implements Uncaster
{
    /**
     * @param string|mixed $value
     *
     * @return DateTimeImmutable
     */
    public function uncast($value): string
    {
        if (! $value instanceof DateTimeImmutable) {
            throw new InvalidArgumentException('Cannot uncast an object that is not a DateTimeImmutable');
        }

        return $value->format('Y-m-d');
    }
}
