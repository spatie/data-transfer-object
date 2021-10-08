<?php

namespace Spatie\DataTransferObject\Resolvers;

use Spatie\DataTransferObject\Descriptors\ClassDescriptor;

interface InboundResolver
{
    public function resolve(ClassDescriptor $descriptor): void;
}
