<?php
namespace Test\Database\Table;

use Source\Database\Column\ColumnDefinition;

class InvalidMockColumnDefinition implements ColumnDefinition
{
    public function get_name(): string
    {
        return "mock-column-name";
    }

    public function get_type(): string
    {
        return "integer";
    }

    public function has_length(): bool
    {
        return true;
    }

    public function get_length(): int
    {
        return -23;
    }

    public function is_not_null(): bool
    {
        return true;
    }

    public function is_unique(): bool
    {
        return false;
    }

    public function has_default_value(): bool
    {
        return true;
    }

    public function get_default_value()
    {
        return null;
    }

    public function is_primary_key(): bool
    {
        return false;
    }

    public function is_autoincrement(): bool
    {
        return true;
    }
}
