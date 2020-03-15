<?php
interface PersistenceResolver
{
    public function resolve_table_name($object) : string;
    public function resolve_column_definitions($object) : array;
    public function resolve_primary_key_column_name($object) : string;
    public function resolve_primary_key_value($object);
}
