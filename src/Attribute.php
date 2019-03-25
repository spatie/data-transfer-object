<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 25.03.19
 * Time: 20:15
 */

namespace Spatie\DataTransferObject;


use Closure;

class Attribute
{
    /**
     * @var Attribute
     */
    protected $property;

    /**
     * PropertyFactory constructor.
     * @param Property $property
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     *
     */
    public function required(): self
    {
        $this->property->setRequired(true);
        return $this;
    }

    /**
     *
     */
    public function optional(): self
    {
        $this->property->setRequired(false);
        return $this;
    }

    /**
     *
     */
    public function rule($rules): self
    {
        $this->property->addRule($rules);
        return $this;
    }

    public function nullable(): self
    {
        $this->property->setNullable(false);
        return $this;
    }

    public function default($value): self
    {
        $this->property->setDefault($value);
        return $this;
    }

    /**
     *
     */
    public function constraint(Closure $callback): self
    {
        $this->property->addConstraint($callback);
        return $this;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }


}