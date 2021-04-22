<?php

namespace Spatie\DataTransferObject\Exceptions;

use Exception;
use Spatie\DataTransferObject\DataTransferObject;

class ValidationException extends Exception
{
    private array $validationErrors;

    public function __construct(DataTransferObject $dataTransferObject, array $validationErrors)
    {
        $className = $dataTransferObject::class;

        $this->validationErrors = $validationErrors;

        $messages = [];

        foreach ($validationErrors as $fieldName => $errorsForField) {
            /** @var \Spatie\DataTransferObject\Validation\ValidationResult $errorForField */
            foreach ($errorsForField as $errorForField) {
                $messages[] = "\t - `{$className}->{$fieldName}`: {$errorForField->message}";
            }
        }

        parent::__construct("Validation errors:" . PHP_EOL . implode(PHP_EOL, $messages));
    }

    public static function composite(DataTransferObject $dataTransferObject, ValidationException ...$exceptions): self
    {
        $validationErrors = [];

        foreach ($exceptions as $exception) {
            foreach ($exception->validationErrors as $key => $validationErrorForField) {
                $validationErrors[$key] ??= [];

                $validationErrors[$key] = [...$validationErrors[$key], ...$validationErrorForField];
            }
        }

        return new self($dataTransferObject, $validationErrors);
    }
}
