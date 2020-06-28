<?php
namespace Test\Annotation;

use PHPUnit\Framework\TestCase;
use Vadorco\Annotation\Column\AnnotationColumnDefinition;

class AnnotationColumnDefinitionTest extends TestCase
{
    public function test_resolve_column_name()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Column" => "column_name"
        ]);

        $column_name = $column_definition->get_name();
        $this->assertEquals("column_name", $column_name);
    }

    public function test_sanitize_column_name()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Column" => "\\\"column_?name`--"
        ]);

        $column_name = $column_definition->get_name();
        $this->assertEquals("column_name", $column_name);
    }

    public function test_resolve_type()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Type" => "type_name"
        ]);

        $type_name = $column_definition->get_type();
        $this->assertEquals("type_name", $type_name);
    }

    public function test_sanitize_type()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Type" => ") type_name \\"
        ]);

        $type_name = $column_definition->get_type();
        $this->assertEquals("type_name", $type_name);
    }

    public function test_resolve_length()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Length" => "64"
        ]);

        $length = $column_definition->get_length();
        $this->assertEquals(64, $length);
    }

    public function test_sanitize_length()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Length" => "64), injected_column TEXT\\"
        ]);

        $length = $column_definition->get_length();
        $this->assertEquals(64, $length);
    }

    public function test_has_length()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Length" => "64"
        ]);

        $length_exists = $column_definition->has_length();
        $this->assertTrue($length_exists);
    }

    public function test_has_not_length()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Legnht" => "64"
        ]);

        $length_exists = $column_definition->has_length();
        $this->assertFalse($length_exists);
    }

    public function test_null_column()
    {
        $column_definition = new AnnotationColumnDefinition([
            "NotNioul" => null
        ]);

        $is_not_null = $column_definition->is_not_null();
        $this->assertFalse($is_not_null);
    }

    public function test_not_null_column()
    {
        $column_definition = new AnnotationColumnDefinition([
            "NotNull" => null
        ]);

        $is_not_null = $column_definition->is_not_null();
        $this->assertTrue($is_not_null);
    }

    public function test_unique_column()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Unique" => null
        ]);

        $is_unique = $column_definition->is_unique();
        $this->assertTrue($is_unique);
    }

    public function test_not_unique_column()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Uniueq" => null
        ]);

        $is_unique = $column_definition->is_unique();
        $this->assertFalse($is_unique);
    }

    public function test_autoincrement_column()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Autoincrement" => null
        ]);

        $is_autoincrement = $column_definition->is_autoincrement();
        $this->assertTrue($is_autoincrement);
    }

    public function test_not_autoincrement_column()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Autoincermnt" => null
        ]);

        $is_autoincrement = $column_definition->is_autoincrement();
        $this->assertFalse($is_autoincrement);
    }

    public function test_has_default_value()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Default" => "value"
        ]);

        $default_value_exists = $column_definition->has_default_value();
        $this->assertTrue($default_value_exists);
    }

    public function test_has_not_default_value()
    {
        $column_definition = new AnnotationColumnDefinition([
            "Defatult" => "value"
        ]);

        $default_value_exists = $column_definition->has_default_value();
        $this->assertFalse($default_value_exists);
    }

    public function test_default_value()
    {
        $expected_value = rand(1, 9999999);

        $column_definition = new AnnotationColumnDefinition([
            "Default" => $expected_value
        ]);

        $default_value = $column_definition->get_default_value();
        $this->assertEquals($expected_value, $default_value);
    }
}
