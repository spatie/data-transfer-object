<?php

namespace Spatie\DataTransferObject;

interface Uncaster
{
    public function uncast($value): mixed;
}
