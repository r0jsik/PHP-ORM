<?php
namespace Source\Annotation;

use Source\Core\PersistenceResolver;
use ReflectionClass;
use ReflectionProperty;

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
        $groups = array();
        $pattern = "/@$annotation_name\((.*)?\)/";

        if (preg_match($pattern, $doc_string, $groups))
        {
            return $groups[1];
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
            $annotation_map = $this->get_column_definition_map($property);

            $definitions[$column_name] = $annotation_map;
        }

        return $definitions;
    }

    private function get_column_definition_map(ReflectionProperty $property): array
    {
        $doc_string = $property->getDocComment();
        $pattern = "/@(\w+)(\((.*)\))?/";
        $match_result = array();

        if (preg_match_all($pattern, $doc_string, $match_result))
        {
            return $this->map_column_definitions($match_result[1], $match_result[3]);
        }

        throw new AnnotationNotFoundException();
    }

    private function map_column_definitions($annotation_names, $annotation_values)
    {
        $definition = array();

        foreach ($annotation_names as $i => $annotation)
        {
            $definition[$annotation] = $this->map_column_definition($annotation_values, $i);
        }

        return $definition;
    }

    private function map_column_definition($annotation_values, $i)
    {
        if ($annotation_values[$i] === "")
        {
            return true;
        }

        return $annotation_values[$i];
    }

    private function get_column_name(ReflectionProperty $property): string
    {
        return $this->extract_annotation_value($property->getDocComment(), "Column");
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

    private function get_properties_of($object): array
    {
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties();

        return $properties;
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
        $is_accessible = $property->isPrivate();
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
            $column_name = $this->get_column_name($property);
            $property_value = $this->get_value_of_property($property, $object);

            $fields_map[$column_name] = $property_value;
        }

        return $fields_map;
    }
}
