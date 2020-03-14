<?php
require_once("Source/Core/PersistenceResolver.php");


class AnnotationPersistenceResolver implements PersistenceResolver
{
    public function resolve_table_name($object): string
    {
        // Resolving name of the table stored in the @Table annotation
        return "clients";
    }

    public function resolve_columns($object): array
    {
        // Resolving value of each annotation assigned to the $object's field
        return null;
    }
}
