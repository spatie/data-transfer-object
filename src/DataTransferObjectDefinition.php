<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ReflectionClass as BaseReflectionClass;
use ReflectionProperty;

class DataTransferObjectDefinition extends BaseReflectionClass
{
    /** @var array */
    protected $uses = [];

    /** @var \Spatie\DataTransferObject\DataTransferObject */
    protected $dataTransferObject;

    public function __construct(DataTransferObject $dataTransferObject)
    {
        parent::__construct($dataTransferObject);

        $this->dataTransferObject = $dataTransferObject;

        $this->resolveUseStatements();
    }

    protected function resolveUseStatements()
    {
        $handle = fopen($this->getFileName(), 'r');

        while ($line = fgets($handle)) {
            $line = trim($line);

            if (strpos($line, 'use ') !== 0) {
                continue;
            }

            $fqcn = str_replace(['use ', ';'], '', $line);

            $classParts = explode('\\', $fqcn);

            $alias = end($classParts);

            $this->uses[$alias] = $fqcn;

            if (strpos($line, 'class') !== false) {
                break;
            }
        }

        fclose($handle);
    }

    public function hasAlias(string $alias): bool
    {
        return isset($this->uses[$alias]);
    }

    public function resolveAlias(string $alias): string
    {
        return $this->uses[$alias];
    }

    /**
     * @return \Spatie\DataTransferObject\DataTransferObjectProperty[]
     */
    public function getDataTransferObjectProperties(): array
    {
        $properties = [];

        foreach (parent::getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = DataTransferObjectProperty::fromReflection(
                $this->dataTransferObject,
                $this,
                $reflectionProperty
            );
        }

        return $properties;
    }
}
