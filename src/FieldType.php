<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

class FieldType
{
    /** @var string */
    private $valueType;

    /** @var string|null */
    private $keyType;

    public function __construct(string $valueType, ?string $keyType = null)
    {
        $this->valueType = $valueType;
        $this->keyType = $keyType;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }

    public function getKeyType(): string
    {
        return $this->keyType;
    }

    public function hasKeyType(): bool
    {
        return null !== $this->keyType;
    }
}
