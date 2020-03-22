<?php
namespace Source\Core;

use Exception;

/**
 * Class LoggingPersistenceService
 * @package Source\Core
 *
 * Decorator of a PersistenceService object that is logging its behaviour.
 */
class LoggingPersistenceService implements PersistenceService
{
    /**
     * @var PersistenceService Decorated PersistenceService object.
     */
    private $persistence_service;

    /**
     * @param PersistenceService $persistence_service An object that will be decorated.
     */
    public function __construct(PersistenceService $persistence_service)
    {
        $this->persistence_service = $persistence_service;
    }

    /**
     * @param mixed $object An object that is inserted into the PersistenceService.
     */
    public function insert($object): void
    {
        try
        {
            $this->log("Inserting object into database...");

            $this->persistence_service->insert($object);

            $this->log("Inserted successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred while inserting object:", $exception);
        }
    }

    /**
     * @param string $message The message that will be printed.
     * @param Exception|null $exception
     */
    private function log(string $message, Exception $exception = null)
    {
        echo "$message";

        if ($exception == null)
        {
            echo "<br />";
        }
        else
        {
            echo "<pre>$exception</pre>";
        }
    }

    /**
     * @param mixed $object An object that will be updated by the PersistenceService.
     */
    public function update($object): void
    {
        try
        {
            $this->log("Updating object in the database...");

            $this->persistence_service->update($object);

            $this->log("Updated successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred while updating object", $exception);
        }
    }

    /**
     * @param mixed $object An object that will be removed from the PersistenceService.
     */
    public function remove($object): void
    {
        try
        {
            $this->log("Removing object from the database...");

            $this->persistence_service->remove($object);

            $this->log("Removed successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred while removing object:", $exception);
        }
    }
}
