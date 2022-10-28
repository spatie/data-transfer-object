<?php

namespace Spatie\DataTransferObject\Tests;

use Attribute;
use DateTimeImmutable;
use Spatie\DataTransferObject\Attributes\DefaultCast;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;

beforeAll(function () {

    #[DefaultCast(DateTimeImmutable::class, DateTimeImmutableCaster::class)]
    class DtoWithDefaultCaster extends DataTransferObject
    {
        public DateTimeImmutable $date;
    }

    #[DefaultCast(DateTimeImmutable::class, DateTimeImmutableCaster::class)]
    abstract class AbstractWithDefaultCaster extends DataTransferObject
    {
    }

    class ChildDto extends AbstractWithDefaultCaster
    {
        public DateTimeImmutable $date;
    }

    #[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
    class DateTimeImmutableCaster implements Caster
    {
        /**
         * @param string|mixed $value
         *
         * @return DateTimeImmutable
         */
        public function cast(mixed $value): DateTimeImmutable
        {
            return DateTimeImmutable::createFromFormat('Y-m-d', $value);
        }
    }
});

test('property is casted', function () {
    $dto = new DtoWithDefaultCaster(date: '2020-01-01');

    $this->markTestSucceeded();
});

test('child property is casted', function () {
    $dto = new ChildDto(date: '2020-01-01');

    $this->markTestSucceeded();
});
