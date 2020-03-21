<?php
namespace Source\Database;

use Source\Core\PersistenceResolver;
use Source\Core\PersistenceService;
use Source\Database\Table\DatabaseTable;

class DatabasePersistenceService implements PersistenceService
{
    private $database;
    private $persistence_resolver;

    public function __construct(Database $database, PersistenceResolver $persistence_resolver)
    {
        $this->database = $database;
        $this->persistence_resolver = $persistence_resolver;
    }

    public function insert($object)
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);

        $this->create_table_if_not_exists($table_name, $object);

        $table = $this->choose_table($table_name, $object);
        $entry = $this->persistence_resolver->resolve_as_entry($object);
        $table->insert($entry);
    }

    private function create_table_if_not_exists(string $table_name, $object)
    {
        if ( !$this->database->table_exists($table_name))
        {
            $column_definitions = $this->persistence_resolver->resolve_column_definitions($object);
            $this->database->create_table($table_name, $column_definitions);
        }
    }

    private function choose_table(string $table_name, $object): DatabaseTable
    {
        $primary_key = $this->persistence_resolver->resolve_primary_key_name($object);
        $table = $this->database->choose_table($table_name, $primary_key);

        return $table;
    }

    public function update($object)
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key_value = $this->persistence_resolver->resolve_primary_key_value($object);
        $entry = $this->persistence_resolver->resolve_as_entry($object);

        $table = $this->choose_table($table_name, $object);
        $table->update($primary_key_value, $entry);
    }

    public function remove($object)
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key_value = $this->persistence_resolver->resolve_primary_key_value($object);

        $table = $this->choose_table($table_name, $object);
        $table->remove($primary_key_value);
    }
}
