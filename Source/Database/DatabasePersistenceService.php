<?php
require("Source/Core/PersistenceService.php");


class DatabasePersistenceService implements PersistenceService
{
    private $database;
    private $persistence_resolver;

    public function __construct($database, $persistence_resolver)
    {
        $this->database = $database;
        $this->persistence_resolver = $persistence_resolver;
    }

    public function insert($object)
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);

        $this->create_table_if_not_exists($table_name, $object);

        $table = $this->choose_table($table_name, $object);
        $entry = $this->persistence_resolver->resolve_fields($object);
        $table->insert($entry);
    }

    private function create_table_if_not_exists($table_name, $object)
    {
        if ( !$this->database->table_exists($table_name))
        {
            $column_definitions = $this->persistence_resolver->resolve_column_definitions($object);
            $this->database->create_table($table_name, $column_definitions);
        }
    }

    private function choose_table($table_name, $object) : DatabaseTable
    {
        $primary_key_column_name = $this->persistence_resolver->resolve_primary_key_column_name($object);
        $table = $this->database->choose_table($table_name, $primary_key_column_name);

        return $table;
    }

    public function update($object)
    {
        $table_name = $this->persistence_resolver->resolve_table_name($object);
        $primary_key_value = $this->persistence_resolver->resolve_primary_key_value($object);
        $entry = $this->persistence_resolver->resolve_fields($object);

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
