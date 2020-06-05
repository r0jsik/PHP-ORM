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
     * @param object $object An object that will be examined.
     * @return string A name of the table pointed by the $object's class.
     */
    public function resolve_table_name($object): string;

    /**
     * @param object $object An object that will be examined.
     * @return array An array of the ColumnDefinition defining columns required by the $object's class fields.
     */
    public function resolve_column_definitions($object): array;

    /**
     * @param object $object An object that will be examined.
     * @return PropertyProxy The primary key.
     */
    public function resolve_primary_key($object): PropertyProxy;

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping column names to corresponding PropertyProxy objects.
     */
    public function resolve_column_to_properties_map($object): array;

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping object's property names to corresponding column names.
     */
    public function resolve_property_to_column_names_map($object): array;

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping column names to corresponding field values.
     */
    public function resolve_column_to_values_map($object): array;
}
