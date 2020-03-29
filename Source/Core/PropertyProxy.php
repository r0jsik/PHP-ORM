<?php
namespace Source\Core;

use ReflectionProperty;

/**
 * Class PropertyProxy
 * @package Source\Core
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
        $is_accessible = $this->property->isPublic();

        $this->property->setAccessible(true);
        $this->property->setValue($this->object, $value);
        $this->property->setAccessible($is_accessible);
    }

    /**
     * @return mixed The value of the property.
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
     * @return string A documentation comment assigned to the property.
     */
    public function get_documentation(): string
    {
        return $this->property->getDocComment();
    }
}
