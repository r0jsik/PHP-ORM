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

        if ( !$this->database->table_exists($table_name))
        {
            $this->create_table($table_name, $object);
        }

        $table = $this->choose_table($table_name, $object);
        $table->insert($object);
    }

    private function create_table($table_name, $object)
    {
        $column_descriptions = $this->persistence_resolver->resolve_column_descriptions($object);
        $this->database->create_table($table_name, $column_descriptions);
    }

    private function choose_table($table_name, $object) : DatabaseTable
    {
        $primary_key_column_name = $this->persistence_resolver->resolve_primary_key_column_name($object);
        $table = $this->database->choose_table($table_name, $primary_key_column_name);

        return $table;
    }

    public function update($object)
    {

    }

    public function remove($object)
    {

    }
}
