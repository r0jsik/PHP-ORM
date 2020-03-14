<?php
require_once("Source/Database/Table/DatabaseTable.php");


class MySQLiDatabaseTable implements DatabaseTable
{
    private $name;
    private $mysqli;

    public function __construct($name, $mysqli)
    {
        $this->name = $name;
        $this->mysqli = $mysqli;
    }

    public function insert($entry)
    {

    }

    public function remove($entry)
    {

    }
}
