<?php
namespace Source\Database;

use ArgumentCountError;
use PHPUnit\Util\Exception;
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
        $entry = $this->persistence_resolver->resolve_as_entry($object);
        $record_id = $table->insert($entry);

        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $primary_key->set_value($record_id);
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
        $primary_key_value = $primary_key->get_value();
        $entry = $this->persistence_resolver->resolve_as_entry($object);

        $table = $this->choose_table($table_name, $object);
        $table->update($primary_key_value, $entry);
    }

    /**
     * @param mixed $object An object that will be removed from the database.
     */
    public function remove($object): void
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $primary_key_value = $primary_key->get_value();

        $table = $this->choose_table($table_name, $object);
        $table->remove($primary_key_value);
    }

    /**
     * @param string $class Path to the class of the retrieved object. Informs about type of the object.
     * @param mixed $primary_key_value An value of the primary key, pointing to the record from which data will be used
     *                                 to create retrieved object.
     * @return mixed An object constructed using data from the database.
     */
    public function select(string $class, $primary_key_value)
    {
        $object = $this->create_object($class);
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $primary_key_name = $primary_key->get_name();
        $table = $this->database->choose_table($table_name, $primary_key_name);
        $entry = $table->select($primary_key_value);

        $this->persistence_resolver->apply($object, $entry);

        return $object;
    }

    /**
     * @param string $class Path to the class of the retrieved object. Informs about type of the object.
     * @return mixed The constructed object. It's constructor cannot take any obligatory arguments.
     */
    private function create_object($class)
    {
        try
        {
            return new $class();
        }
        catch (ArgumentCountError $exception)
        {
            throw new Exception();
        }
    }
}
