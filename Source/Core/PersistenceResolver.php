<?php
namespace Source\Core;

interface PersistenceResolver
{
    public function resolve_table_name($object): string;
    public function resolve_column_definitions($object): array;
    public function resolve_primary_key_name($object): string;
    public function resolve_primary_key_value($object);
    public function resolve_as_entry($object): array;
}
