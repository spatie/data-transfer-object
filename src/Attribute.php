<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 25.03.19
 * Time: 20:15.
 */

namespace Spatie\DataTransferObject;

use Closure;

/**
 * Class Attribute.
 */
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
     * @return Attribute
     */
    public function required(): self
    {
        $this->property->setRequired(true);

        return $this;
    }

    /**
     * @return Attribute
     */
    public function optional(): self
    {
        $this->property->setRequired(false);

        return $this;
    }

    /**
     * @param $rules
     * @return Attribute
     */
    public function rule($rules): self
    {
        $this->property->addRule($rules);

        return $this;
    }

    /**
     * @return Attribute
     */
    public function nullable(): self
    {
        $this->property->setNullable(false);

        return $this;
    }

    /**
     * @param $value
     * @return Attribute
     */
    public function default($value): self
    {
        $this->property->setDefault($value);

        return $this;
    }

    /**
     * @param Closure $callback
     * @return Attribute
     */
    public function constraint(Closure $callback): self
    {
        $this->property->addConstraint($callback);

        return $this;
    }

    /**
     * @return Property
     */
    public function getProperty(): Property
    {
        return $this->property;
    }
}
