<?php

namespace Spatie\DataTransferObject;

interface Caster
{
    public function cast(mixed $value): mixed;
}
