<?php
namespace Vadorco\Database\Persistence;

use Vadorco\Core\ObjectFactory;
use Vadorco\Core\Persistence\PersistenceResolver;
use Vadorco\Core\Persistence\PersistenceService;
use Vadorco\Database\Database;
use Vadorco\Database\Table\DatabaseTable;
use ArgumentCountError;

/**
 * Class DatabasePersistenceService
 * @package Vadorco\Database
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
     * @var ObjectFactory A factory converting data loaded from the database to standalone objects.
     */
    private $object_factory;

    /**
     * @param Database $database An object representing database in which persistent objects are stored and managed.
     * @param PersistenceResolver $persistence_resolver An object representing mechanism resolving persistence information.
     * @param ObjectFactory $object_factory A factory converting data loaded from the database to standalone objects.
     */
    public function __construct(Database $database, PersistenceResolver $persistence_resolver, ObjectFactory $object_factory)
    {
        $this->database = $database;
        $this->persistence_resolver = $persistence_resolver;
        $this->object_factory = $object_factory;
    }

    /**
     * @inheritDoc
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
     * @param object $object An object whose persistence information corresponds with the table specified by $table_name.
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
     * @param object $object An object whose persistence information corresponds with the table specified by $table_name.
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     * @throws ArgumentCountError Thrown when unable to instantiate object due to arguments mismatch.
     */
    public function select(string $class, $primary_key_value)
    {
        $object = $this->object_factory->instantiate($class);
        $table = $this->choose_table_for($object);
        $entry = $table->select($primary_key_value);
        $properties = $this->persistence_resolver->resolve_properties($object);

        $this->object_factory->apply_properties($entry, $properties);

        return $object;
    }

    /**
     * @param object $object An examined object.
     * @return DatabaseTable The selected table.
     */
    private function choose_table_for($object): DatabaseTable
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key = $this->persistence_resolver->resolve_primary_key($object);
        $primary_key_name = $primary_key->get_name();
        $table = $this->database->choose_table($table_name, $primary_key_name);

        return $table;
    }

    /**
     * @inheritDoc
     * @throws ArgumentCountError Thrown when unable to instantiate object due to arguments mismatch.
     */
    public function select_all(string $class): array
    {
        $object = $this->object_factory->instantiate($class);
        $table = $this->choose_table_for($object);
        $entries = $table->select_all();
        $objects = $this->convert_to_objects($class, $entries);

        return $objects;
    }

    /**
     * @param string $class Path to the class of the retrieved objects.
     * @param array $entries An array of associative arrays mapping column names to field values.
     * @return array An array containing constructed objects.
     * @throws ArgumentCountError Thrown when unable to instantiate object due to arguments mismatch.
     */
    private function convert_to_objects(string $class, array $entries): array
    {
        $objects = array();

        foreach ($entries as $entry)
        {
            $objects[] = $this->convert_to_object($class, $entry);
        }

        return $objects;
    }

    /**
     * @param string $class Path to the class of the retrieved object.
     * @param array $entry An associative array mapping column names to field values.
     * @return object The constructed object.
     * @throws ArgumentCountError Thrown when unable to instantiate object due to arguments mismatch.
     */
    private function convert_to_object(string $class, array $entry)
    {
        $object = $this->object_factory->instantiate($class);
        $properties = $this->persistence_resolver->resolve_properties($object);

        $this->object_factory->apply_properties($entry, $properties);

        return $object;
    }

    /**
     * @inheritDoc
     * @throws ArgumentCountError Thrown when unable to instantiate object due to arguments mismatch.
     */
    public function select_individually(string $class, callable $filter): array
    {
        $object = $this->object_factory->instantiate($class);
        $table = $this->choose_table_for($object);
        $entries = $table->select_all();
        $entries = array_filter($entries, $filter);
        $objects = $this->convert_to_objects($class, $entries);

        return $objects;
    }

    /**
     * @inheritDoc
     * @throws ArgumentCountError Thrown when unable to instantiate object due to arguments mismatch.
     */
    public function select_on_condition(string $class, callable $build_condition): array
    {
        $object = $this->object_factory->instantiate($class);
        $column_names = $this->persistence_resolver->resolve_column_names($object);
        $condition_builder = $this->database->create_condition_builder($column_names);

        $build_condition($condition_builder);

        $table = $this->choose_table_for($object);
        $entries = $table->select_where($condition_builder);
        $objects = $this->convert_to_objects($class, $entries);

        return $objects;
    }

    /**
     * @inheritDoc
     */
    public function within_transaction(callable $action): void
    {
        $this->database->within_transaction($action);
    }
}
