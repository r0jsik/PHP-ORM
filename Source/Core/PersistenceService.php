<?php
namespace Source\Core;

/**
 * Interface PersistenceService
 * @package Source\Core
 *
 * An interface defining manager of the objects' persistence.
 */
interface PersistenceService
{
    /**
     * @param object $object An object that is inserted into the persistence data structure.
     */
    public function insert($object): void;

    /**
     * @param object $object An object that will be updated in the persistence data structure.
     */
    public function update($object): void;

    /**
     * @param object $object An object that will be removed from the persistence data structure.
     */
    public function remove($object): void;

    /**
     * @param string $class Path to the class of the retrieved object. Informs about type of the object.
     * @param mixed $primary_key_value An value of the primary key, pointing to the data source
     *                                 from which the object will be constructed.
     * @return object The constructed object.
     */
    public function select(string $class, $primary_key_value);

    /**
     * @param string $class Path to the class of the retrieved objects. Informs about type of the objects.
     * @return array An array containing constructed objects.
     */
    public function select_all(string $class): array;

    /**
     * @param string $class Path to the class of the retrieved object.
     * @param callable $filter A function accepting an associative array mapping column names to field values.
     *                         Objects will be created only for the entries for which this function returns true.
     * @return array An array containing constructed objects.
     */
    public function select_filtered(string $class, callable $filter): array;
}
