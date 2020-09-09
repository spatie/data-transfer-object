<?php

namespace Spatie\DataTransferObject;

abstract class FlexibleDataTransferObject extends DataTransferObject
{
    protected function ignoreMissing(): bool
    {
        return true;
    }
}
