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
     * @param mixed $object An object that is inserted into the persistence data structure.
     */
    public function insert($object): void;

    /**
     * @param mixed $object An object that will be updated in the persistence data structure.
     */
    public function update($object): void;

    /**
     * @param mixed $object An object that will be removed from the persistence data structure.
     */
    public function remove($object): void;
}
