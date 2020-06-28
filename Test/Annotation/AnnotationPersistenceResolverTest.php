<?php
namespace Test\Annotation;

use PHPUnit\Framework\TestCase;
use Vadorco\Annotation\AnnotationNotFoundException;
use Vadorco\Annotation\Column\AnnotationColumnDefinition;
use Vadorco\Annotation\Persistence\AnnotationPersistenceResolver;

class AnnotationPersistenceResolverTest extends TestCase
{
    private $persistence_resolver;

    public function setUp(): void
    {
        $this->persistence_resolver = new AnnotationPersistenceResolver();
    }

    public function test_resolve_table_name()
    {
        $object = new MockObjectWithValidTableAnnotation();
        $table_name = $this->persistence_resolver->resolve_table_name($object);

        $this->assertEquals("table_name", $table_name);
    }

    public function test_sanitize_table_name()
    {
        $object = new MockObjectWithInvalidTableAnnotation();
        $table_name = $this->persistence_resolver->resolve_table_name($object);

        $this->assertEquals("table_name", $table_name);
    }

    public function test_table_annotation_not_found_exception()
    {
        $object = new MockObjectWithoutTableName();

        $this->expectException(AnnotationNotFoundException::class);
        $this->persistence_resolver->resolve_table_name($object);
    }

    public function test_resolve_column_definitions()
    {
        $expected_column_definitions = [
            new AnnotationColumnDefinition([
                "Column" => "column_name_A", "Annotation" => "value 1"
            ]),
            new AnnotationColumnDefinition([
                "Column" => "column_name_B", "Annotation" => "value 2"
            ])
        ];

        $object = new MockObjectToTestProperties();
        $column_definitions = $this->persistence_resolver->resolve_column_definitions($object);

        $this->assertEquals($expected_column_definitions,$column_definitions);
    }

    public function test_column_annotation_not_found_exception()
    {
        $object = new MockObjectWithInvalidPrimaryKey();

        $this->expectException(AnnotationNotFoundException::class);
        $this->persistence_resolver->resolve_column_names($object);
    }

    public function test_resolve_primary_key()
    {
        $expected_value = rand(1, 9999999);

        $object = new MockObjectWithValidPrimaryKey();
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $primary_key->set_value($expected_value);
        $current_value = $primary_key->get_value();

        $this->assertEquals($expected_value, $current_value);
    }

    public function test_primary_key_annotation_not_found_exception()
    {
        $object = new MockObjectWithInvalidPrimaryKey();

        $this->expectException(AnnotationNotFoundException::class);
        $this->persistence_resolver->resolve_primary_key($object);
    }

    public function test_resolve_properties()
    {
        $expected_value_A = rand(1, 9999999);
        $expected_value_B = rand(1, 9999999);

        $object = new MockObjectToTestProperties();
        $properties = $this->persistence_resolver->resolve_properties($object);
        $properties["column_name_A"]->set_value($expected_value_A);
        $properties["column_name_B"]->set_value($expected_value_B);

        $value_A = $object->get_value_A();
        $value_B = $object->get_value_B();

        $this->assertEquals($expected_value_A, $value_A);
        $this->assertEquals($expected_value_B, $value_B);
    }

    public function test_resolve_column_names()
    {
        $object = new MockObjectToTestProperties();
        $column_names = $this->persistence_resolver->resolve_column_names($object);
        $column_name_of_property_A = $column_names["property_A"];
        $column_name_of_property_B = $column_names["property_B"];

        $this->assertEquals("column_name_A", $column_name_of_property_A);
        $this->assertEquals("column_name_B", $column_name_of_property_B);
    }

    public function test_resolve_as_entry()
    {
        $expected_value_A = rand(1, 9999999);
        $expected_value_B = rand(1, 9999999);
        $expected_entry = ["column_name_A" => $expected_value_A, "column_name_B" => $expected_value_B];

        $object = new MockObjectToTestProperties();
        $object->set_value_A($expected_value_A);
        $object->set_value_B($expected_value_B);

        $entry = $this->persistence_resolver->resolve_as_entry($object);

        $this->assertEquals($expected_entry, $entry);
    }
}
