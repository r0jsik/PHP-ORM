<?php
namespace Source\Database\Table;

interface ColumnDefinition
{
    public function get_name(): string;
    public function get_type(): string;
    public function has_length(): bool;
    public function get_length(): int;
    public function is_not_null(): bool;
    public function is_unique(): bool;
    public function has_default_value(): bool;
    public function get_default_value();
    public function is_primary_key(): bool;
    public function is_autoincrement(): bool;
}
