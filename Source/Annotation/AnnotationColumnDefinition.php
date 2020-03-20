<?php
namespace Source\Annotation;

use Source\Database\Table\ColumnDefinition;

class AnnotationColumnDefinition implements ColumnDefinition
{
    private $annotations;

    public function __construct(array $annotations)
    {
        $this->annotations = $annotations;
    }

    public function get_name(): string
    {
        return $this->annotations["Column"];
    }

    public function get_type(): string
    {
        return $this->annotations["Type"];
    }

    public function has_length(): bool
    {
        return $this->has_annotation("Length");
    }

    private function has_annotation(string $name): bool
    {
        return key_exists($name, $this->annotations);
    }

    public function get_length(): int
    {
        return $this->annotations["Length"];
    }

    public function is_not_null(): bool
    {
        return $this->has_annotation("NotNull");
    }

    public function is_unique(): bool
    {
        return $this->has_annotation("Unique");
    }

    public function has_default_value(): bool
    {
        return $this->has_annotation("Default");
    }

    public function get_default_value()
    {
        return $this->annotations["Default"];
    }

    public function is_primary_key(): bool
    {
        return $this->has_annotation("PrimaryKey");
    }

    public function is_autoincrement(): bool
    {
        return $this->has_annotation("Autoincrement");
    }
}
