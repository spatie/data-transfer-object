<?php

namespace Spatie\DataTransferObject;

abstract class FlexibleDataTransferObject extends DataTransferObject
{
    protected bool $ignoreMissing = true;
}
