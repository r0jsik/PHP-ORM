<?php
namespace Source\Annotation;

use Source\Core\PersistenceResolver;

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
        return array(
            "id"        => array("type" => "integer"),
            "name"      => array("type" => "varchar", "length" => 32),
            "surname"   => array("type" => "varchar", "length" => 32),
            "phone"     => array("type" => "varchar", "length" => 32),
            "email"     => array("type" => "varchar", "length" => 32, "unique" => true)
        );
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

    public function resolve_fields($object): array
    {
        // Resolving values of each field
        return array(
            "id"        => 1,
            "name"      => "RESOLVED NAME",
            "surname"   => "RESOLVED SURNAME",
            "phone"     => "RESOLVED PHONE",
            "email"     => "RESOLVED EMAIL"
        );
    }
}
