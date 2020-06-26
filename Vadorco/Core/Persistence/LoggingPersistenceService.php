<?php
namespace Vadorco\Core\Persistence;

use Exception;

/**
 * Class LoggingPersistenceService
 * @package Vadorco\Core
 *
 * Decorator of a PersistenceService object that is logging its behaviour.
 */
class LoggingPersistenceService implements PersistenceService
{
    /**
     * @var PersistenceService Decorated PersistenceService object.
     */
    private $persistence_service;

    public function __construct(PersistenceService $persistence_service)
    {
        $this->persistence_service = $persistence_service;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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

    /**
     * @inheritDoc
     */
    public function select(string $class, $primary_key_value)
    {
        $object = null;

        try
        {
            $this->log("Selecting object from the database...");

            $object = $this->persistence_service->select($class, $primary_key_value);

            $this->log("Selected successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred while selecting object:", $exception);
        }

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function select_all(string $class): array
    {
        $objects = array();

        try
        {
            $this->log("Selecting objects from the database...");

            $objects = $this->persistence_service->select_all($class);

            $this->log("Selected successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred while selecting objects:", $exception);
        }

        return $objects;
    }

    /**
     * @inheritDoc
     */
    public function select_individually(string $class, callable $filter): array
    {
        $objects = array();

        try
        {
            $this->log("Selecting objects from the database individually...");

            $objects = $this->persistence_service->select_individually($class, $filter);

            $this->log("Selected successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred while selecting objects:", $exception);
        }

        return $objects;
    }

    /**
     * @inheritDoc
     */
    public function select_on_condition(string $class, callable $build_condition): array
    {
        $objects = array();

        try
        {
            $this->log("Selecting objects from the database using condition...");

            $objects = $this->persistence_service->select_on_condition($class, $build_condition);

            $this->log("Selected successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Exception occurred while selecting objects:", $exception);
        }

        return $objects;
    }

    /**
     * @inheritDoc
     */
    public function within_transaction(callable $action): void
    {
        try
        {
            $this->log("Invoking a transaction...");

            $this->persistence_service->within_transaction($action);

            $this->log("Transaction completed successfully");
        }
        catch (Exception $exception)
        {
            $this->log("Transaction interrupted:", $exception);
        }
    }
}
