<?php
namespace Test\Database\Table;

use Source\Database\Column\ColumnDefinition;

class MockColumnDefinition implements ColumnDefinition
{
    private $name;
    private $type;
    private $is_primary_key;
    private $is_unique;
    private $is_not_null;

    public function __construct(string $name, string $type, bool $is_primary_key = false, bool $is_unique = false, bool $is_not_null = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->is_primary_key = $is_primary_key;
        $this->is_unique = $is_unique;
        $this->is_not_null = $is_not_null;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function has_length(): bool
    {
        return false;
    }

    public function get_length(): int
    {
        return 0;
    }

    public function is_not_null(): bool
    {
        return $this->is_not_null;
    }

    public function is_unique(): bool
    {
        return $this->is_unique;
    }

    public function has_default_value(): bool
    {
        return false;
    }

    public function get_default_value()
    {
        return null;
    }

    public function is_primary_key(): bool
    {
        return $this->is_primary_key;
    }

    public function is_autoincrement(): bool
    {
        return $this->is_primary_key;
    }
}