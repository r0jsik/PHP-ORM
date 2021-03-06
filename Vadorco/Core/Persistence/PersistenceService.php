<?php
namespace Vadorco\Core\Persistence;

/**
 * Interface PersistenceService
 * @package Vadorco\Core
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
     * @param callable $filter A function accepting an associative array mapping property names to values.
     *                         Objects will be created only for the entries for which this function returns true.
     * @return array An array containing constructed objects.
     */
    public function select_individually(string $class, callable $filter): array;

    /**
     * @param string $class Path to the class of the retrieved object.
     * @param callable $build_condition A function returning the condition builder which will be used to create the condition.
     *                         Accepts an object which can be used to build the query resistant to SQL Injection attacks.
     * @return array An array containing constructed objects.
     */
    public function select_on_condition(string $class, callable $build_condition): array;

    /**
     * @param callable $action An action that will be invoked within transaction.
     *                         If the action throws an exception, the transaction is interrupted and rolled-back.
     */
    public function within_transaction(callable $action): void;
}
