<?php

namespace Spatie\DataTransferObject\Tests;

use LogicException;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;

class TestCase extends BaseTestCase
{
    protected function getDescriptor(string $dataTransferObject, array $arguments = []): ClassDescriptor
    {
        $reflection = new ReflectionClass($dataTransferObject);

        $dataTransferObject = $this->getDataTransferObject($dataTransferObject, $arguments);

        while ($reflection->getName() != DataTransferObject::class) {
            $reflection = $reflection->getParentClass();
        }

        $descriptor = $reflection->getProperty('descriptor');
        $descriptor->setAccessible(true);

        return $descriptor->getValue($dataTransferObject);
    }

    protected function getDataTransferObject(string $dataTransferObject, array $arguments = []): DataTransferObject
    {
        if (is_subclass_of($dataTransferObject, DataTransferObject::class) === false) {
            throw new LogicException(
                "Class [{$dataTransferObject}] does not extend " . DataTransferObject::class . "."
            );
        }

        $reflection = new ReflectionClass($dataTransferObject);

        /** @var DataTransferObject $class */
        $class = $reflection->newInstanceWithoutConstructor();

        // It's cheating, but it works!
        call_user_func_array(
            $reflection->getMethod('__construct')->getClosure($class),
            $arguments
        );

        return $class;
    }
}
