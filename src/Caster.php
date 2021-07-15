<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject;

interface Caster
{
    public function cast(mixed $value): mixed;
}
