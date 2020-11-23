<?php

namespace Spatie\DataTransferObject\Validation;

use JetBrains\PhpStorm\Immutable;

class ValidationResult
{
    public function __construct(
        #[Immutable]
        public bool $isValid,

        #[Immutable]
        public ?string $message = null
    ) {
    }
}
