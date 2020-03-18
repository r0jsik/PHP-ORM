<?php
namespace Source\Core;

use Exception;

class LoggingPersistenceService implements PersistenceService
{
    private $persistence_service;

    public function __construct(PersistenceService $persistence_service)
    {
        $this->persistence_service = $persistence_service;
    }

    public function insert($object)
    {
        try
        {
            $this->log("Inserting object into database...");

            $this->persistence_service->insert($object);

            $this->log("Inserted successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred during inserting object: $exception");
        }
    }

    private function log(string $message)
    {
        echo "$message<br />";
    }

    public function update($object)
    {
        try
        {
            $this->log("Updating object in the database...");

            $this->persistence_service->update($object);

            $this->log("Updated successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred during updating object: $exception");
        }
    }

    public function remove($object)
    {
        try
        {
            $this->log("Removing object from the database...");

            $this->persistence_service->remove($object);

            $this->log("Removed successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred during removing object: $exception");
        }
    }
}
