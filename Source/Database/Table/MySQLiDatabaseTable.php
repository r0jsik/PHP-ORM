<?php
namespace Source\Database\Table;

use mysqli;

class MySQLiDatabaseTable implements DatabaseTable
{
    private $name;
    private $primary_key_column_name;
    private $mysqli;

    public function __construct(string $name, string $primary_key_column_name, mysqli $mysqli)
    {
        $this->name = $name;
        $this->primary_key_column_name = $primary_key_column_name;
        $this->mysqli = $mysqli;
    }

    public function insert(array $entry)
    {

    }

    public function update($primary_key_value, array $entry)
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
