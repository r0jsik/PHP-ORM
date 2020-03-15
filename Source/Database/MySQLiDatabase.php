<?php
require_once("Source/Database/Database.php");
require_once("Source/Database/Table/MySQLiDatabaseTable.php");


class MySQLiDatabase implements Database
{
    private $mysqli;
    private $database_name;

    public function __construct($host, $username, $password, $database_name)
    {
        $this->mysqli = new mysqli($host, $username, $password, $database_name);
        $this->database_name = $database_name;
    }

    public function table_exists($name) : bool
    {
        $exists = false;

        $query = $this->table_exists_query($name);
        $query->execute();

        if ($result = $query->get_result()->fetch_assoc())
        {
            $exists = $result["flag"];
        }

        $query->close();

        return $exists;
    }

    private function table_exists_query($table_name) : mysqli_stmt
    {
        $query = "SELECT COUNT(*) AS flag FROM information_schema.tables WHERE table_schema=? AND table_name=?";
        $query = $this->mysqli->prepare($query);
        $query->bind_param("ss", $this->database_name, $table_name);

        return $query;
    }

    public function create_table($name, $columns)
    {

    }

    public function choose_table($name, $primary_key_column_name) : DatabaseTable
    {
        return new MySQLiDatabaseTable($name, $primary_key_column_name, $this->mysqli);
    }
}