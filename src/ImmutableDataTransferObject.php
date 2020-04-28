<?php

namespace Spatie\DataTransferObject;

class ImmutableDataTransferObject
{
    protected DataTransferObject $dataTransferObject;

    public function __construct(DataTransferObject $dataTransferObject)
    {
        foreach (get_object_vars($dataTransferObject) as $k => $v) {
            if (is_subclass_of($v, DataTransferObject::class)) {
                $dataTransferObject->{$k} = new self($v);
            };
        }
        $this->dataTransferObject = $dataTransferObject;
    }

    public function __set($name, $value)
    {
        throw DataTransferObjectError::immutable($name);
    }

    public function __get($name)
    {
        return $this->dataTransferObject->{$name};
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->dataTransferObject, $name], $arguments);
    }
}
