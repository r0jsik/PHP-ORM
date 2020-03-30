<?php
namespace Source\Core;

use ArgumentCountError;
use PHPUnit\Util\Exception;

class ObjectFactory
{
    /**
     * @param string $class Path to the class of the retrieved object. Informs about type of the object.
     * @return object The constructed object. It's constructor cannot take any obligatory arguments.
     */
    public function instantiate(string $class)
    {
        try
        {
            return new $class();
        }
        catch (ArgumentCountError $exception)
        {
            throw new Exception();
        }
    }

    /**
     * @param array $entry An associative array mapping column names to its values.
     * @param array $column_properties An associative array mapping column names to appropriate PropertyProxy objects.
     */
    public function apply_properties(array $entry, array $column_properties)
    {
        foreach ($column_properties as $column_name => $property)
        {
            $property->set_value($entry[$column_name]);
        }
    }
}
