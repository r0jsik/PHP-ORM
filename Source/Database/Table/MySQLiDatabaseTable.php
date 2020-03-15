<?php
require_once("Source/Database/Table/DatabaseTable.php");


class MySQLiDatabaseTable implements DatabaseTable
{
    private $name;
    private $primary_key_column_name;
    private $mysqli;

    public function __construct($name, $primary_key_column_name, $mysqli)
    {
        $this->name = $name;
        $this->primary_key_column_name = $primary_key_column_name;
        $this->mysqli = $mysqli;
    }

    public function insert($entry)
    {

    }

    public function update($entry_id, $entry)
    {

    }

    public function remove($entry_id, $entry)
    {

    }
}
