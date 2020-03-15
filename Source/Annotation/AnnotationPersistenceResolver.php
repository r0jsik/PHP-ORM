<?php
require_once("Source/Core/PersistenceResolver.php");


class AnnotationPersistenceResolver implements PersistenceResolver
{
    public function resolve_table_name($object): string
    {
        // Resolving name of the table stored in the @Table annotation
        return "clients";
    }

    public function resolve_column_definitions($object): array
    {
        // Resolving value of each annotation assigned to the $object's field
        return null;
    }

    public function resolve_primary_key_column_name($object): string
    {
        // Resolving name of the Primary Key's column
        return "id";
    }

    public function resolve_primary_key_value($object)
    {
        // Resolving value of the Primary Key's field
        return 1;
    }
}
