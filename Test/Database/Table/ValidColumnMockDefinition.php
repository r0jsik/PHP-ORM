<?php
namespace Test\Database\Table;

use Source\Database\Table\ColumnDefinition;

class ValidColumnMockDefinition implements ColumnDefinition
{
    public function get_name(): string
    {
        return "mock-column-name";
    }

    public function get_type(): string
    {
        return "varchar";
    }

    public function has_length(): bool
    {
        return true;
    }

    public function get_length(): int
    {
        return 64;
    }

    public function is_not_null(): bool
    {
        return true;
    }

    public function is_unique(): bool
    {
        return true;
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
        return true;
    }

    public function is_autoincrement(): bool
    {
        return false;
    }
}
