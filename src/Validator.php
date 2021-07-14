<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject;

use Spatie\DataTransferObject\Validation\ValidationResult;

interface Validator
{
    public function validate(mixed $value): ValidationResult;
}
