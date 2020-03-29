<?php
namespace Source\Annotation;

use Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Source\Core\PersistenceResolver;
use Source\Core\PrimaryKey;
use Source\Core\ReflectedPrimaryKey;

/**
 * Class AnnotationPersistenceResolver
 * @package Source\Annotation
 *
 * An annotation-based implementation of the PersistenceResolver interface.
 */
class AnnotationPersistenceResolver implements PersistenceResolver
{
    /**
     * @param mixed $object An object that will be examined.
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
     * @param mixed $object An object that will be examined.
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
     * @param mixed $object An object that will be examined.
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
     * @param mixed $object An object that will be examined.
     * @return array An array of the $object's class fields.
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     */
    private function get_properties_of($object): array
    {
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties();

        return $properties;
    }

    /**
     * @param ReflectionProperty $property The property that will be examined.
     * @return Generator An object generating column definitions, for example:
     *                   field annotated as a @Column(name) will have generated definition as "Column" => "name" map.
     * @throws AnnotationNotFoundException Thrown when $property field is not annotated.
     */
    private function get_column_definition_generator(ReflectionProperty $property): Generator
    {
        $doc_string = $property->getDocComment();
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
     * @param mixed $object An examined object.
     * @return PrimaryKey The primary key.
     * @throws AnnotationNotFoundException Thrown when none of the $object's class' field is annotated by the @PrimaryKey annotation.
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     */
    public function resolve_primary_key($object): PrimaryKey
    {
        $properties = $this->get_properties_of($object);

        foreach ($properties as $property)
        {
            if ($this->is_annotated($property, "PrimaryKey"))
            {
                return new ReflectedPrimaryKey($object, $property);
            }
        }

        throw new AnnotationNotFoundException();
    }

    /**
     * @param ReflectionProperty $property The field that will be examined.
     * @param string $annotation_name A name of the annotation that will be searched in the $property field's documentation comment.
     * @return bool A flag checking if the field is annotated by the Annotation named as $annotation_name.
     */
    private function is_annotated(ReflectionProperty $property, string $annotation_name): bool
    {
        return preg_match("/@$annotation_name/", $property->getDocComment());
    }

    /**
     * @param mixed $object An object that will be examined.
     * @return array An associative array mapping @Column's annotation value to the field's value, for example:
     *               Field with "example value" value that is annotated by the @Column(column-name) annotation will be mapped as:
     *               "column-name" => "example value"
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     * @throws AnnotationNotFoundException Thrown when some field of the $object is not annotated by the @Column annotation.
     */
    public function resolve_as_entry($object): array
    {
        $properties = $this->get_properties_of($object);
        $fields_map = array();

        foreach ($properties as $property)
        {
            $fields_map[$this->get_column_name($property)] = $this->get_value_of_property($property, $object);
        }

        return $fields_map;
    }

    /**
     * @param ReflectionProperty $property An examined field.
     * @return string The content of the @Column annotation assigned to the examined field.
     * @throws AnnotationNotFoundException Thrown when field is not annotated by the @Column annotation.
     */
    private function get_column_name(ReflectionProperty $property): string
    {
        return $this->extract_annotation_value($property->getDocComment(), "Column");
    }

    /**
     * @param ReflectionProperty $property A field of the object that will be examined.
     * @param mixed $object An object that will be examined.
     * @return mixed The value of the $object's field.
     */
    private function get_value_of_property(ReflectionProperty $property, $object)
    {
        $is_accessible = $property->isPublic();
        $property->setAccessible(true);
        $value = $property->getValue($object);
        $property->setAccessible($is_accessible);

        return $value;
    }

    /**
     * @param mixed $object An object that data will be applied to.
     * @param array $entry An associative array mapping field's name to its value.
     * @throws AnnotationNotFoundException Thrown when some field in not annotated by the @Column annotation.
     * @throws ReflectionException Thrown when unable to reflect $object's class.
     */
    public function apply($object, array $entry): void
    {
        $properties = $this->get_properties_of($object);

        foreach ($properties as $property)
        {
            $column_name = $this->get_column_name($property);
            $value = $entry[$column_name];

            $this->set_value_of_property($property, $object, $value);
        }
    }

    /**
     * @param ReflectionProperty $property A field of the object that will be updated.
     * @param mixed $object An object which property's value will be updated.
     * @param mixed $value The value that will be applied to the property.
     */
    private function set_value_of_property(ReflectionProperty $property, $object, $value)
    {
        $is_accessible = $property->isPublic();
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible($is_accessible);
    }
}
