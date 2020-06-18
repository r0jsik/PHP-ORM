<?php
namespace Source\Annotation\Persistence;

use Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Source\Annotation\AnnotationNotFoundException;
use Source\Annotation\Column\AnnotationColumnDefinition;
use Source\Core\Persistence\PersistenceResolver;
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
     * @return string A name of the table resolved as a value of the @Table annotation assigned to the object's class.
     * @throws AnnotationNotFoundException Thrown when the object's class is not annotated with the @Table annotation.
     * @throws ReflectionException Thrown when unable to resolve object's class.
     */
    public function resolve_table_name($object): string
    {
        $doc_string = $this->get_documentation_of($object);
        $table_name = $this->get_annotation_value($doc_string, "Table");
        $table_name = preg_replace("/\W/", "", $table_name);

        return $table_name;
    }

    /**
     * @param object $object An object that will be examined.
     * @return string A documentation comment assigned to the object's class.
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    private function get_documentation_of($object): string
    {
        $reflection = new ReflectionClass($object);
        $doc_string = $reflection->getDocComment();

        return $doc_string;
    }

    /**
     * @param string $doc_string A documentation comment assigned to the object's class.
     * @param string $annotation_name A name of the annotation that will be searched in the $doc_string string.
     * @return mixed The value of the annotation.
     * @throws AnnotationNotFoundException Thrown when the documentation doesn't contain specified annotation.
     */
    private function get_annotation_value(string $doc_string, string $annotation_name)
    {
        $pattern = "/@$annotation_name\((.*)?\)/";
        $matches = [];

        if (preg_match($pattern, $doc_string, $matches))
        {
            return $matches[1];
        }

        throw new AnnotationNotFoundException($annotation_name);
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An array of the AnnotationColumnDefinition that defines each column of the object's fields.
     * @throws AnnotationNotFoundException Thrown when some field of the object's class is not annotated properly.
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    public function resolve_column_definitions($object): array
    {
        $properties = $this->get_properties_of($object);
        $column_definitions = [];

        foreach ($properties as $property)
        {
            $column_definition = $this->generate_column_definition_of($property);
            $column_definition = iterator_to_array($column_definition);
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

        return $properties;
    }

    /**
     * @param ReflectionProperty $property The property that will be examined.
     * @return Generator An object generating column definitions, for example:
     *                   Field annotated with the @Column(name) annotation will have generated association: "Column" => "name".
     * @throws AnnotationNotFoundException Thrown when property's field is not annotated.
     */
    private function generate_column_definition_of(ReflectionProperty $property): Generator
    {
        $doc_string = $property->getDocComment();
        $pattern = "/@(\w+)(\((.*)\))?/";
        $matches = [];

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
     * @throws AnnotationNotFoundException Thrown when none of the object's fields is annotated with the @PrimaryKey annotation.
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    public function resolve_primary_key($object): PropertyProxy
    {
        $property = $this->get_annotated_property($object, "PrimaryKey");
        $property_proxy = new PropertyProxy($property, $object);

        return $property_proxy;
    }

    /**
     * @param object $object An object that will be examined.
     * @param string $annotation_name A name of the annotation that will be searched.
     * @return ReflectionProperty The found property.
     * @throws AnnotationNotFoundException Thrown when none of the object's fields is annotated with the $annotation_name annotation.
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    private function get_annotated_property($object, string $annotation_name): ReflectionProperty
    {
        $properties = $this->get_properties_of($object);

        foreach ($properties as $property)
        {
            if ($this->is_annotated($property, $annotation_name))
            {
                return $property;
            }
        }

        throw new AnnotationNotFoundException();
    }

    /**
     * @param ReflectionProperty $property The property that will be examined.
     * @param string $annotation_name A name of the annotation that will be searched in the $property field's documentation comment.
     * @return bool A flag checking if the field is annotated with the annotation named as $annotation_name.
     */
    private function is_annotated(ReflectionProperty $property, string $annotation_name): bool
    {
        return preg_match("/@$annotation_name/", $property->getDocComment());
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping column names to PropertyProxy fields, for example:
     *               Property of the class that is annotated with the @Column(column-name) annotation will be mapped as:
     *               "column-name" => PropertyProxy object.
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    public function resolve_properties($object): array
    {
        return $this->resolve_associations($object, function($property) use ($object) {
            yield $this->get_column_name_of($property) => new PropertyProxy($property, $object);
        });
    }

    /**
     * @param object $object An object that will be examined.
     * @param callable $factory A closure returning generator of the associations that will be packed into array.
     * @return array An associative array containing associations provided by the factory, for example:
     *               the factory yields association defined as key => value; multiple times, for different properties.
     *               Each entry of this array will associate all key to its values (as the factory yields).
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    private function resolve_associations($object, callable $factory): array
    {
        $properties = $this->get_properties_of($object);
        $resolved = [];

        foreach ($properties as $property)
        {
            foreach ($factory($property) as $key => $value)
            {
                $resolved[$key] = $value;
            }
        }

        return $resolved;
    }

    /**
     * @param ReflectionProperty $property An examined field.
     * @return string The content of the @Column annotation assigned to the examined field.
     * @throws AnnotationNotFoundException Thrown when field is not annotated with the @Column annotation.
     */
    private function get_column_name_of(ReflectionProperty $property): string
    {
        $doc_comment = $property->getDocComment();
        $column_name = $this->get_annotation_value($doc_comment, "Column");
        $column_name = preg_replace("/\W/", "", $column_name);

        return $column_name;
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping object's property names to corresponding column names, for example:
     *               A field named as "field_name" is annotated with the @Column(column-name) annotation.
     *               It will be mapped as "field_name" => "column_name".
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    public function resolve_column_names($object): array
    {
        return $this->resolve_associations($object, function($property) {
            yield $property->getName() => $this->get_column_name_of($property);
        });
    }

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping @Column's annotation values to the field's values, for example:
     *               Field with "example value" value that is annotated with the @Column(column-name) annotation will be mapped as:
     *               "column-name" => "example value"
     * @throws ReflectionException Thrown when unable to reflect object's class.
     */
    public function resolve_as_entry($object): array
    {
        return $this->resolve_associations($object, function($property) use ($object) {
            yield $this->get_column_name_of($property) => PropertyProxy::get_value_of($property, $object);
        });
    }
}
