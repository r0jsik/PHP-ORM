<?php
namespace Source\Annotation;

use Generator;
use ReflectionClass;
use ReflectionProperty;
use Source\Core\PersistenceResolver;

class AnnotationPersistenceResolver implements PersistenceResolver
{
    public function resolve_table_name($object): string
    {
        $doc_string = $this->get_doc_string_of($object);
        $table_name = $this->extract_annotation_value($doc_string, "Table");

        return $table_name;
    }

    private function get_doc_string_of($object): string
    {
        $reflection = new ReflectionClass($object);
        $doc_string = $reflection->getDocComment();

        return $doc_string;
    }

    private function extract_annotation_value(string $doc_string, string $annotation_name)
    {
        $pattern = "/@$annotation_name\((.*)?\)/";
        $matches = array();

        if (preg_match($pattern, $doc_string, $matches))
        {
            return $matches[1];
        }

        throw new AnnotationNotFoundException($annotation_name);
    }

    public function resolve_column_definitions($object): array
    {
        $properties = $this->get_properties_of($object);
        $definitions = array();

        foreach ($properties as $property)
        {
            $column_name = $this->get_column_name($property);
            $definitions_generator = $this->get_column_definitions_generator($property);

            $definitions[$column_name] = iterator_to_array($definitions_generator);
        }

        return $definitions;
    }

    private function get_properties_of($object): array
    {
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties();

        return $properties;
    }

    private function get_column_name(ReflectionProperty $property): string
    {
        return $this->extract_annotation_value($property->getDocComment(), "Column");
    }

    private function get_column_definitions_generator(ReflectionProperty $property): Generator
    {
        $doc_string = $property->getDocComment();
        $pattern = "/@(\w+)(\((.*)\))?/";
        $matches = array();

        if (preg_match_all($pattern, $doc_string, $matches))
        {
            return $this->generate_column_definitions($matches[1], $matches[3]);
        }

        throw new AnnotationNotFoundException();
    }

    private function generate_column_definitions($annotation_names, $annotation_values): Generator
    {
        foreach ($annotation_names as $i => $annotation_name)
        {
            yield $annotation_name => $annotation_values[$i];
        }
    }

    public function resolve_primary_key_name($object): string
    {
        return $this->get_primary_key_property($object)->getName();
    }

    private function get_primary_key_property($object): ReflectionProperty
    {
        $properties = $this->get_properties_of($object);

        foreach ($properties as $property)
        {
            if ($this->is_annotated($property, "PrimaryKey"))
            {
                return $property;
            }
        }

        throw new AnnotationNotFoundException();
    }

    private function is_annotated(ReflectionProperty $property, string $annotation_name): bool
    {
        return preg_match("/@$annotation_name/", $property->getDocComment());
    }

    public function resolve_primary_key_value($object)
    {
        return $this->get_value_of_property($this->get_primary_key_property($object), $object);
    }

    private function get_value_of_property(ReflectionProperty $property, $object)
    {
        $is_accessible = $property->isPublic();
        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible($is_accessible);

        return $value;
    }

    public function resolve_fields_map($object): array
    {
        $properties = $this->get_properties_of($object);
        $fields_map = array();

        foreach ($properties as $property)
        {
            $fields_map[$this->get_column_name($property)] = $this->get_value_of_property($property, $object);
        }

        return $fields_map;
    }
}
