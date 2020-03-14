<?php
require("Source/Core/PersistenceService.php");


class DatabasePersistenceService implements PersistenceService
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function insert($object)
    {
        echo "Saving $object in database: ".$this->database;
    }

    public function update($object)
    {
        echo "Updating $object in database: ".$this->database;
    }

    public function remove($object)
    {
        echo "Removing $object from database: ".$this->database;
    }
}
