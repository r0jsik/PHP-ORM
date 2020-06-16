<?php
namespace Test\Annotation;

spl_autoload_register(function ($path) {
    require_once("$path.php");
});

use PHPUnit\Framework\TestCase;
use Source\Annotation\AnnotationNotFoundException;
use Source\Annotation\Persistence\AnnotationPersistenceResolver;

class AnnotationPersistenceResolverTest extends TestCase
{
    private $annotation_resolver;
    private $valid_mock_object;
    private $invalid_mock_object;

    public function setUp(): void
    {
        $this->annotation_resolver = new AnnotationPersistenceResolver();
        $this->valid_mock_object = new ValidMockAnnotatedClass();
        $this->invalid_mock_object = new InvalidMockAnnotatedClass();
    }

    public function test_resolve_valid_table_name()
    {
        $table_name = $this->annotation_resolver->resolve_table_name($this->valid_mock_object);
        $this->assertNotNull($table_name);
    }

    public function test_resolve_invalid_table_name()
    {
        $this->expectException(AnnotationNotFoundException::class);
        $this->annotation_resolver->resolve_table_name($this->invalid_mock_object);
    }

    public function test_resolve_valid_column_definitions()
    {
        $definitions = $this->annotation_resolver->resolve_column_definitions($this->valid_mock_object);
        $mock_column_definition = $definitions[0];

        $this->assertEquals("column-name", $mock_column_definition->get_name());
        $this->assertEquals("varchar", $mock_column_definition->get_type());

        $this->assertTrue($mock_column_definition->is_not_null());
        $this->assertFalse($mock_column_definition->has_default_value());
    }

    public function test_resolve_valid_primary_key()
    {
        $primary_key = $this->annotation_resolver->resolve_primary_key($this->valid_mock_object);
        $this->assertNotNull($primary_key);
    }

    public function test_resolve_invalid_primary_key()
    {
        $this->expectException(AnnotationNotFoundException::class);
        $this->annotation_resolver->resolve_primary_key($this->invalid_mock_object);
    }

    public function test_resolve_primary_key_value()
    {
        $primary_key = $this->annotation_resolver->resolve_primary_key($this->valid_mock_object);
        $primary_key_value = $primary_key->get_value();
        $this->assertEquals("TEST", $primary_key_value);
    }
}
