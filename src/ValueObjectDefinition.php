<?php

namespace Spatie\ValueObject;

use ReflectionClass as BaseReflectionClass;
use ReflectionProperty;

class ValueObjectDefinition extends BaseReflectionClass
{
    /** @var array */
    protected $uses = [];

    /** @var \Spatie\ValueObject\ValueObject */
    protected $valueObject;

    public function __construct(ValueObject $valueObject)
    {
        parent::__construct($valueObject);

        $this->valueObject = $valueObject;

        $this->resolveUseStatements();
    }

    protected function resolveUseStatements()
    {
        $handle = fopen($this->getFileName(), 'r');

        while ($line = fgets($handle)) {
            $line = trim($line);

            if (strpos($line, 'use') !== 0) {
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

    public function resolveAlias(string $alias): string
    {
        return $this->uses[$alias];
    }

    public function hasAlias(string $alias): bool
    {
        return isset($this->uses[$alias]);
    }

    /**
     * @return \Spatie\ValueObject\ValueObjectProperty[]
     */
    public function getValueObjectProperties(): array
    {
        $properties = [];

        foreach (parent::getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = ValueObjectProperty::fromReflection(
                $this->valueObject,
                $this,
                $reflectionProperty
            );
        }

        return $properties;
    }
}
