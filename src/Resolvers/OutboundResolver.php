<?php

namespace Spatie\DataTransferObject\Resolvers;

use Spatie\DataTransferObject\Descriptors\ClassDescriptor;

interface OutboundResolver
{
    public function resolve(ClassDescriptor $descriptor): void;
}
