<?php

namespace Spatie\DataTransferObject\Contracts;

interface DtoContract
{
    public function all(): array;

    public function only(string ...$keys): DtoContract;

    public function except(string ...$keys): DtoContract;

    public function toArray(): array;

    public function immutable() :DtoContract;

    public function isImmutable(): bool;
}
