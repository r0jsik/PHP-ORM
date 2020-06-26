<?php
namespace Vadorco\Core;

use ReflectionProperty;

/**
 * Class PropertyProxy
 * @package Vadorco\Core
 *
 * Represents simple proxy of the ReflectionProperty.
 */
class PropertyProxy
{
    /**
     * @var ReflectionProperty An object being proxied.
     */
    private $property;

    /**
     * @var mixed An object owning the property.
     */
    private $object;

    /**
     * @param ReflectionProperty $property An object that will be proxied.
     * @param $object mixed An object owning the property.
     */
    public function __construct(ReflectionProperty $property, $object)
    {
        $this->property = $property;
        $this->object = $object;
    }

    /**
     * @return string The name of the property.
     */
    public function get_name(): string
    {
        return $this->property->getName();
    }

    /**
     * @param mixed $value The value that will be applied to the property.
     */
    public function set_value($value): void
    {
        static::set_value_of($this->property, $this->object, $value);
    }

    /**
     * @param ReflectionProperty $property The property that will be examined.
     * @param object $object An object that will be examined.
     * @param mixed $value The value that will be set.
     */
    public static function set_value_of(ReflectionProperty $property, $object, $value)
    {
        $is_accessible = $property->isPublic();

        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible($is_accessible);
    }

    /**
     * @return mixed The value of the property.
     */
    public function get_value()
    {
        return static::get_value_of($this->property, $this->object);
    }

    /**
     * @param ReflectionProperty $property The property that will be examined.
     * @param object $object An object that will be examined.
     * @return mixed The value of the property.
     */
    public static function get_value_of(ReflectionProperty $property, $object)
    {
        $is_accessible = $property->isPublic();

        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible($is_accessible);

        return $value;
    }
}
