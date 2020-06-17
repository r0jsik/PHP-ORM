<?php
namespace Source\Annotation\Column;

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
     */
    public function __construct(array $annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * @return string A name of the column resolved from the @Column annotation.
     */
    public function get_name(): string
    {
        return filter_var($this->annotations["Column"], FILTER_SANITIZE_STRING);
    }

    /**
     * @return string A type of the column resolved from the @Type annotation.
     */
    public function get_type(): string
    {
        return filter_var($this->annotations["Type"], FILTER_SANITIZE_STRING);
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Length annotation.
     */
    public function has_length(): bool
    {
        return key_exists("Length", $this->annotations);
    }

    /**
     * @return int A length of the column resolved as a value of the @Length annotation.
     */
    public function get_length(): int
    {
        return filter_var($this->annotations["Length"], FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @return bool A flag checking if the column is annotated by the @NotNull annotation.
     */
    public function is_not_null(): bool
    {
        return key_exists("NotNull", $this->annotations);
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Unique annotation.
     */
    public function is_unique(): bool
    {
        return key_exists("Unique", $this->annotations);
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Default annotation.
     */
    public function has_default_value(): bool
    {
        return key_exists("Default", $this->annotations);
    }

    /**
     * @return mixed A default value of the column resolved as a value of the @Default annotation.
     */
    public function get_default_value()
    {
        return filter_var($this->annotations["Default"], FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * @return bool A flag checking if the column is annotated by the @PrimaryKey annotation.
     */
    public function is_primary_key(): bool
    {
        return key_exists("PrimaryKey", $this->annotations);
    }

    /**
     * @return bool A flag checking if the column is annotated by the @Autoincrement annotation.
     */
    public function is_autoincrement(): bool
    {
        return key_exists("Autoincrement", $this->annotations);
    }
}
