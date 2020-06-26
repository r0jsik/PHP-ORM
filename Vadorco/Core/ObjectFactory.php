<?php
namespace Vadorco\Core;

use ArgumentCountError;

class ObjectFactory
{
    /**
     * @param string $class Path to the class of the retrieved object. Informs about type of the object.
     * @return object The constructed object. It's constructor cannot take any obligatory arguments.
     * @throws ArgumentCountError Thrown when unable to instantiate object due to arguments mismatch.
     */
    public function instantiate(string $class)
    {
        return new $class();
    }

    /**
     * @param array $entry An associative array mapping property names to its values.
     * @param array $properties An associative array mapping property names to appropriate PropertyProxy objects.
     */
    public function apply_properties(array $entry, array $properties): void
    {
        foreach ($properties as $property_name => $property)
        {
            $property->set_value($entry[$property_name]);
        }
    }
}
