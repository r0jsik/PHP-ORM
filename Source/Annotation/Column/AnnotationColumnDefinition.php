<?php
namespace Source\Annotation\Column;

use Source\Annotation\AnnotationNotFoundException;
use Source\Database\Column\ColumnDefinition;

/**
 * Class AnnotationColumnDefinition
 * @package Source\Annotation
 *
 * Represents annotation-based implementation of the ColumnDefinition.
 * Each definition processes annotations array delivered by the AnnotationPersistenceResolver.
 */
class AnnotationColumnDefinition implements ColumnDefinition
{
    /**
     * @var array An associative array mapping each annotation to its value, for example:
     *            The annotation @Example(value 123) is mapped as "Example" => "value 123"
     */
    private $annotations;

    /**
     * @param array $annotations An associative array containing annotations with its values.
     * @throws AnnotationNotFoundException
     */
    public function __construct(array $annotations)
    {
        if ( !key_exists("Type", $annotations))
        {
            throw new AnnotationNotFoundException();
        }

        $this->annotations = $annotations;
    }

    /**
     * @return string A name of the column resolved from the @Column annotation.
     */
    public function get_name(): string
    {
        return $this->annotations["Column"];
    }

    /**
     * @return string A type of the column resolved from the @Type annotation.
     */
    public function get_type(): string
    {
        return $this->annotations["Type"];
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Length annotation.
     */
    public function has_length(): bool
    {
        return $this->has_annotation("Length");
    }

    /**
     * @param string $name A name of the annotation.
     * @return bool A flag checking if the annotation with the specified name defines the column.
     */
    private function has_annotation(string $name): bool
    {
        return key_exists($name, $this->annotations);
    }

    /**
     * @return int A length of the column resolved as a value of the @Length annotation.
     */
    public function get_length(): int
    {
        return $this->annotations["Length"];
    }

    /**
     * @return bool A flag checking if the column is annotated by the @NotNull annotation.
     */
    public function is_not_null(): bool
    {
        return $this->has_annotation("NotNull");
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Unique annotation.
     */
    public function is_unique(): bool
    {
        return $this->has_annotation("Unique");
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Default annotation.
     */
    public function has_default_value(): bool
    {
        return $this->has_annotation("Default");
    }

    /**
     * @return mixed A default value of the column resolved as a value of the @Default annotation.
     */
    public function get_default_value()
    {
        return $this->annotations["Default"];
    }

    /**
     * @return bool A flag checking if the column is annotated by the @PrimaryKey annotation.
     */
    public function is_primary_key(): bool
    {
        return $this->has_annotation("PrimaryKey");
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Autoincrement annotation.
     */
    public function is_autoincrement(): bool
    {
        return $this->has_annotation("Autoincrement");
    }
}
