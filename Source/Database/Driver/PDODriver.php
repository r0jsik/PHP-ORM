<?php
namespace Source\Database\Driver;

use Exception;
use PDO;
use Source\Core\InvalidPrimaryKeyException;
use Source\Database\DatabaseActionException;
use Source\Database\DatabaseConnectionException;

/**
 * Class PDODriver
 * @package Source\Database\Driver
 *
 * Represents a PDO-based implementation of the database Driver.
 */
class PDODriver implements Driver
{
    /**
     * @var PDO An object representing connection with the database.
     */
    private $pdo;

    /**
     * @param string $data_source A data source.
     * @param string $username An username of the user that database will be connected as.
     * @param string $password A password of the user connected to the database.
     */
    public function __construct(string $data_source, string $username, string $password)
    {
        $this->pdo = new PDO($data_source, $username, $password);
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     */
    public function execute(string $query): void
    {
        if ( !$this->pdo->query($query))
        {
            throw new DatabaseActionException("Unable to execute the query");
        }
    }

    /**
     * @inheritDoc
     * @throws InvalidPrimaryKeyException
     * @throws DatabaseActionException
     */
    public function execute_prepared(string $query, ...$parameters): void
    {
        $prepared_query = $this->pdo->prepare($query);
        $prepared_query->execute($parameters);

        try
        {
            if ($prepared_query->rowCount() == 0)
            {
                if ($prepared_query->errorCode() == 23000)
                {
                    throw new DatabaseActionException();
                }

                throw new InvalidPrimaryKeyException();
            }
        }
        finally
        {
            $prepared_query->closeCursor();
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     */
    public function insert(string $query, ...$values): int
    {
        $prepared_query = $this->pdo->prepare($query);
        $prepared_query->execute($values);

        try
        {
            if ($prepared_query->rowCount() > 0)
            {
                return $this->pdo->lastInsertId();
            }

            throw new DatabaseActionException();
        }
        finally
        {
            $prepared_query->closeCursor();
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     */
    public function select_multiple(string $query): array
    {
        if ($statement = $this->pdo->query($query))
        {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }

        throw new DatabaseActionException();
    }

    /**
     * @inheritDoc
     * @throws InvalidPrimaryKeyException
     */
    public function select(string $query, $primary_key_value): array
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute([$primary_key_value]);

        if ($result = $statement->fetch(PDO::FETCH_ASSOC))
        {
            return $result;
        }

        throw new InvalidPrimaryKeyException();
    }

    /**
     * @inheritDoc
     * @throws Exception Thrown from the action.
     */
    public function within_transaction(callable $action): void
    {
        $this->pdo->beginTransaction();

        try
        {
            $action();
        }
        catch (Exception $exception)
        {
            $this->pdo->rollBack();

            throw $exception;
        }

        $this->pdo->commit();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->pdo = null;
    }
}
