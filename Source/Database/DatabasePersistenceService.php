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
            $columns = $this->persistence_resolver->resolve_columns();
            $this->database->create_table($table_name, $columns);
        }

        $table = $this->database->choose_table($table_name);
        $table->insert($object);
    }

    public function update($object)
    {

    }

    public function remove($object)
    {

    }
}
