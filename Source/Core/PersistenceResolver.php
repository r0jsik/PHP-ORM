<?php
namespace Source\Core;

/**
 * Interface PersistenceResolver
 * @package Source\Core
 *
 * An interface defining mechanism resolving persistence information of the objects.
 */
interface PersistenceResolver
{
    /**
     * @param mixed $object An object that will be examined.
     * @return string A name of the table pointed by the $object's class.
     */
    public function resolve_table_name($object): string;

    /**
     * @param mixed $object An object that will be examined.
     * @return array An array of the ColumnDefinition that defines columns required by the $object's class' fields.
     */
    public function resolve_column_definitions($object): array;

    /**
     * @param mixed $object An object that will be examined.
     * @return PrimaryKey The primary key.
     */
    public function resolve_primary_key($object): PrimaryKey;

    /**
     * @param mixed $object An object that will be examined.
     * @return array An associative array mapping column names to corresponding field values.
     */
    public function resolve_as_entry($object): array;

    /**
     * @param mixed $object An object that data will be applied to.
     * @param array $entry An associative array mapping field's name to its value.
     */
    public function apply($object, array $entry): void;
}
