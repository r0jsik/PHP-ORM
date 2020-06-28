<?php
namespace Test\Annotation;

/**
 * @Table(table_name)
 */
class MockObjectToTestProperties
{
    /**
     * @Column(column_name_A)
     * @Annotation(value 1)
     */
    private $property_A;

    /**
     * @Column(column_name_B)
     * @Annotation(value 2)
     */
    private $property_B;

    public function set_value_A(int $value)
    {
        $this->property_A = $value;
    }

    public function get_value_A()
    {
        return $this->property_A;
    }

    public function set_value_B(int $value)
    {
        $this->property_B = $value;
    }

    public function get_value_B()
    {
        return $this->property_B;
    }
}
