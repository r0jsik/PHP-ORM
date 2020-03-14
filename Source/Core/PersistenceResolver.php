<?php
interface PersistenceResolver
{
    public function resolve_table_name($object) : string;
    public function resolve_columns($object) : array;
}
