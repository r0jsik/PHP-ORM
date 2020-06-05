<?php
namespace Source\Annotation;

use Generator;
use ReflectionClass;
use ReflectionException;
use Source\Core\PersistenceResolver;
use Source\Core\PropertyProxy;

/**
 * Class AnnotationPersistenceResolver
 * @package Source\Annotation
 *
 * An annotation-based implementation of the PersistenceResolver interface.
 */
class AnnotationPersistenceResolver implements PersistenceResolver
{
    /**
     * @param object $object An object that will be examined.
     * @return string A name of the table resolved as a value of the @Table annotation assigned to the $object's class.
     * @throws AnnotationNotFoundException Thrown when the $object's class is not annotated by the @Table annotation.
     * @throws ReflectionException Thrown when unable to resolve $object's class.
     */
    public function resolve_table_name($object): string
    {
        $doc_string = $this->get_doc_string_of($object);
        $table_name = $this->extract_annotation_value($doc_string, "Table");

        return $table_name;
    }

    /**
     * @param object $object An object that will be examined.
     * @return string A documentation comment assigned to the $object's class.
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     */
    private function get_doc_string_of($object): string
    {
        $reflection = new ReflectionClass($object);
        $doc_string = $reflection->getDocComment();

        return $doc_string;
    }

    /**
     * @param string $doc_string A documentation comment assigned to the $object's class.
     * @param string $annotation_name A name of the annotation that will be searched in the $doc_string string.
     * @return mixed The value of the annotation.
     * @throws AnnotationNotFoundException Thrown when $doc_string doesn't contain an Annotation named as $annotation_name.
     */
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

    /**
     * @param object $object An object that will be examined.
     * @return array An array of the AnnotatedColumnDefinition that defines each column of the $object's fields.
     * @throws AnnotationNotFoundException Thrown when some field of the $object's class is not annotated.
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     */
    public function resolve_column_definitions($object): array
    {
        $properties = $this->get_properties_of($object);
        $column_definitions = array();

        foreach ($properties as $property)
        {
            $column_definition_generator = $this->get_column_definition_generator($property);
            $column_definition = iterator_to_array($column_definition_generator);
            $column_definitions[] = new AnnotationColumnDefinition($column_definition);
        }

        return $column_definitions;
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An array of the object's properties.
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    private function get_properties_of($object): array
    {
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties();

        $property_proxies = array();

        foreach ($properties as $property)
        {
            $property_proxies[] = new PropertyProxy($property, $object);
        }

        return $property_proxies;
    }

    /**
     * @param PropertyProxy $property The property that will be examined.
     * @return Generator An object generating column definitions, for example:
     *                   field annotated as a @Column(name) will have generated definition as "Column" => "name" map.
     * @throws AnnotationNotFoundException Thrown when $property field is not annotated.
     */
    private function get_column_definition_generator(PropertyProxy $property): Generator
    {
        $doc_string = $property->get_documentation();
        $pattern = "/@(\w+)(\((.*)\))?/";
        $matches = array();

        if (preg_match_all($pattern, $doc_string, $matches))
        {
            return $this->generate_column_definition($matches[1], $matches[3]);
        }

        throw new AnnotationNotFoundException();
    }

    /**
     * @param $annotation_names array An array of each annotation name.
     * @param $annotation_values array An array of each annotation value.
     * @return Generator An object generating map of the definitions.
     */
    private function generate_column_definition(array $annotation_names, array $annotation_values): Generator
    {
        foreach ($annotation_names as $i => $annotation_name)
        {
            yield $annotation_name => $annotation_values[$i];
        }
    }

    /**
     * @param object $object An object that will be examined.
     * @return PropertyProxy The primary key.
     * @throws AnnotationNotFoundException Thrown when none of the $object's class' field is annotated by the @PrimaryKey annotation.
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     */
    public function resolve_primary_key($object): PropertyProxy
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

    /**
     * @param PropertyProxy $property The property that will be examined.
     * @param string $annotation_name A name of the annotation that will be searched in the $property field's documentation comment.
     * @return bool A flag checking if the field is annotated by the Annotation named as $annotation_name.
     */
    private function is_annotated(PropertyProxy $property, string $annotation_name): bool
    {
        return preg_match("/@$annotation_name/", $property->get_documentation());
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping column names to PropertyProxy fields, for example:
     *               Property of the class that is annotated by the @Column(column-name) annotation will be mapped as:
     *               "column-name" => PropertyProxy(Property)
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     * @throws AnnotationNotFoundException Thrown when some property is not annotated by the @Column annotation.
     */
    public function resolve_column_to_properties_map($object): array
    {
        $properties = $this->get_properties_of($object);
        $map = array();

        foreach ($properties as $property)
        {
            $map[$this->get_column_name($property)] = $property;
        }

        return $map;
    }

    /**
     * @param PropertyProxy $property An examined field.
     * @return string The content of the @Column annotation assigned to the examined field.
     * @throws AnnotationNotFoundException Thrown when field is not annotated by the @Column annotation.
     */
    private function get_column_name(PropertyProxy $property): string
    {
        return $this->extract_annotation_value($property->get_documentation(), "Column");
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping object's property names to corresponding column names.
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     * @throws AnnotationNotFoundException Thrown when some field of the $object is not annotated by the @Column annotation.
     */
    public function resolve_property_to_column_names_map($object): array
    {
        $properties = $this->get_properties_of($object);
        $map = array();

        foreach ($properties as $property)
        {
            $map[$property->get_name()] = $this->get_column_name($property);
        }

        return $map;
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping @Column's annotation value to the field's value, for example:
     *               Field with "example value" value that is annotated by the @Column(column-name) annotation will be mapped as:
     *               "column-name" => "example value"
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     * @throws AnnotationNotFoundException Thrown when some field of the $object is not annotated by the @Column annotation.
     */
    public function resolve_column_to_values_map($object): array
    {
        $properties = $this->get_properties_of($object);
        $map = array();

        foreach ($properties as $property)
        {
            $map[$this->get_column_name($property)] = $property->get_value();
        }

        return $map;
    }
}
