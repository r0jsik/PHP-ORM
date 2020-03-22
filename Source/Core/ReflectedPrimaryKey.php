<?php
namespace Source\Core;

use ReflectionProperty;

/**
 * Class ReflectedPrimaryKey
 * @package Source\Core
 *
 * A reflection-based implementation of the PrimaryKey interface.
 */
class ReflectedPrimaryKey implements PrimaryKey
{
    /**
     * @var mixed An examined object
     */
    private $object;

    /**
     * @var ReflectionProperty A reflected property.
     */
    private $property;

    /**
     * @param mixed $object An examined object.
     * @param ReflectionProperty $property An examined $object's property.
     */
    public function __construct($object, ReflectionProperty $property)
    {
        $this->object = $object;
        $this->property = $property;
    }

    /**
     * @return string A name of the primary key.
     */
    public function get_name(): string
    {
        return $this->property->getName();
    }

    /**
     * @return mixed A value of the primary key.
     */
    public function get_value()
    {
        $is_accessible = $this->property->isPublic();
        $this->property->setAccessible(true);
        $value = $this->property->getValue($this->object);
        $this->property->setAccessible($is_accessible);

        return $value;
    }

    /**
     * @param mixed $value A value that will be assigned to the primary key.
     */
    public function set_value($value): void
    {
        $is_accessible = $this->property->isPublic();
        $this->property->setAccessible(true);
        $this->property->setValue($this->object, $value);
        $this->property->setAccessible($is_accessible);
    }
}
