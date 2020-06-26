<?php
namespace Vadorco\Core\Persistence;

use Vadorco\Core\PropertyProxy;

/**
 * Interface PersistenceResolver
 * @package Vadorco\Core
 *
 * An interface defining mechanism resolving persistence information of the objects.
 */
interface PersistenceResolver
{
    /**
     * @param object $object An object that will be examined.
     * @return string The name of the object's table.
     */
    public function resolve_table_name($object): string;

    /**
     * @param object $object An object that will be examined.
     * @return array An array of the ColumnDefinition objects defining structure of the object's table.
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
    public function resolve_properties($object): array;

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping object's property names to corresponding column names.
     */
    public function resolve_column_names($object): array;

    /**
     * @param object $object An object that will be examined.
     * @return array An associative array mapping column names to corresponding field values.
     */
    public function resolve_as_entry($object): array;
}
