<?php
namespace Source\Database;

use Source\Core\PersistenceResolver;
use Source\Core\PersistenceService;
use Source\Database\Table\DatabaseTable;

/**
 * Class DatabasePersistenceService
 * @package Source\Database
 *
 * Represents the mechanism managing persistent objects stored in the Database.
 */
class DatabasePersistenceService implements PersistenceService
{
    /**
     * @var Database An object representing database in which persistent objects are stored and managed.
     */
    private $database;

    /**
     * @var PersistenceResolver An object representing mechanism of resolving persistence information.
     */
    private $persistence_resolver;

    /**
     * @param Database $database An object representing database in which persistent objects are stored and managed.
     * @param PersistenceResolver $persistence_resolver An object representing mechanism resolving persistence information.
     */
    public function __construct(Database $database, PersistenceResolver $persistence_resolver)
    {
        $this->database = $database;
        $this->persistence_resolver = $persistence_resolver;
    }

    /**
     * @param mixed $object An object that is inserted into the database.
     */
    public function insert($object): void
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);

        $this->create_table_if_not_exists($table_name, $object);

        $table = $this->choose_table($table_name, $object);
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $entry = $this->persistence_resolver->resolve_as_entry($object);
        $table->insert($primary_key, $entry);
    }

    /**
     * @param string $table_name A name of the examined table.
     * @param mixed $object An object whose persistence information corresponds with the table specified by $table_name.
     */
    private function create_table_if_not_exists(string $table_name, $object)
    {
        if ( !$this->database->table_exists($table_name))
        {
            $column_definitions = $this->persistence_resolver->resolve_column_definitions($object);
            $this->database->create_table($table_name, $column_definitions);
        }
    }

    /**
     * @param string $table_name A name of the table that will be created.
     * @param mixed $object An object whose persistence information corresponds with the table specified by $table_name.
     * @return DatabaseTable An object representing table stored in the database.
     */
    private function choose_table(string $table_name, $object): DatabaseTable
    {
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $primary_key_name = $primary_key->get_name();
        $table = $this->database->choose_table($table_name, $primary_key_name);

        return $table;
    }

    /**
     * @param mixed $object An object that will be updated in the database.
     */
    public function update($object): void
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $entry = $this->persistence_resolver->resolve_as_entry($object);

        $table = $this->choose_table($table_name, $object);
        $table->update($primary_key, $entry);
    }

    /**
     * @param mixed $object An object that will be removed from the database.
     */
    public function remove($object): void
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);

        $table = $this->choose_table($table_name, $object);
        $table->remove($primary_key);
    }
}
