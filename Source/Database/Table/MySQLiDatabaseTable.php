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

    public function update($primary_key_value, $entry)
    {

    }

    public function remove($primary_key_value)
    {
        $query = $this->remove_query($primary_key_value);
        $query->execute();
        $query->close();
    }

    private function remove_query($primary_key_value)
    {
        $query = "DELETE FROM {$this->name} WHERE ? = ?;";
        $query = $this->mysqli->prepare($query);
        $query->bind_param("ss", $this->primary_key_column_name, $primary_key_value);

        return $query;
    }
}
