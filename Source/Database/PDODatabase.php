<?php
require_once("Database.php");


class PDODatabase implements Database
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    function insert($entry)
    {

    }

    public function __toString()
    {
        return sprintf("PDO-DATABASE(file: %s)<br />", $this->file);
    }
}
