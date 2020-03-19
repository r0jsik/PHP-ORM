<?php
namespace Source\Database;

use Source\Database\Table\DatabaseTable;

interface Database
{
    public function table_exists(string $name): bool;
    public function create_table(string $name, array $column_definitions);
    public function choose_table(string $name, string $primary_key_name): DatabaseTable;
}
