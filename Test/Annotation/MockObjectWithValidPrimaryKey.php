<?php
namespace Test\Annotation;

class MockObjectWithValidPrimaryKey
{
    /**
     * @PrimaryKey
     */
    private $property;

    public function set_value($value)
    {
        $this->property = $value;
    }

    public function get_value()
    {
        return $this->property;
    }
}
