<?php

namespace Spatie\DataTransferObject\Contracts;

interface PropertyContract
{
    public function getDefault();

    public function setDefault($default): void;

    public function isVisible(): bool;

    public function setVisible(bool $bool);

    public function getValue();

    public function getValueFromReflection($object);

    public function getName(): string;

    public function set($value): void;

    public function setInitialized(bool $bool): void;

    public function isInitialized(): bool;

    public function getTypes(): array;

    public function getFqn(): string;

    public function nullable(): bool;

    public function setNullable(bool $bool): void;

    public function immutable(): bool;

    public function setImmutable(bool $immutable): void;
}
