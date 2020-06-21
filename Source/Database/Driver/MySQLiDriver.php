<?php
namespace Source\Database\Driver;

use Exception;
use mysqli;
use mysqli_stmt;
use Source\Core\InvalidPrimaryKeyException;
use Source\Database\DatabaseActionException;
use Source\Database\DatabaseConnectionException;

/**
 * Class MySQLiDriver
 * @package Source\Database\Driver
 *
 * Represents a MySQLi-based implementation of the database Driver.
 */
class MySQLiDriver implements Driver
{
    /**
     * @var mysqli An object representing connection with the database.
     */
    private $mysqli;

    /**
     * @param string $host An address of the database host.
     * @param string $username An username of the user that database will be connected as.
     * @param string $password A password of the user connected to the database.
     * @param string $database_name A name of the database from which data will be received.
     * @throws DatabaseConnectionException Thrown when unable to connect to the database.
     */
    public function __construct(string $host, string $username, string $password, string $database_name)
    {
        $this->mysqli = $this->open_connection($host, $username, $password, $database_name);
    }

    /**
     * @param string $host An address of the database host.
     * @param string $username An username of the user that database will be connected as.
     * @param string $password A password of the user connected to the database.
     * @param string $database_name A name of the database from which data will be received.
     * @return mysqli A connection.
     * @throws DatabaseConnectionException Thrown when unable to connect to the database.
     */
    private function open_connection(string $host, string $username, string $password, string $database_name): mysqli
    {
        $mysqli = new mysqli($host, $username, $password, $database_name);

        if ($mysqli->connect_errno)
        {
            throw new DatabaseConnectionException($mysqli->connect_errno);
        }

        return $mysqli;
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     */
    public function execute(string $query): void
    {
        if ( !$this->mysqli->query($query))
        {
            throw new DatabaseActionException("Unable to execute the query");
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     * @throws InvalidPrimaryKeyException
     */
    public function execute_prepared(string $query, ...$parameters): void
    {
        $prepared_query = $this->prepare_query($query, $parameters);
        $prepared_query->execute();

        try
        {
            if ($prepared_query->affected_rows == -1)
            {
                throw new DatabaseActionException();
            }

            if ($prepared_query->affected_rows == 0)
            {
                throw new InvalidPrimaryKeyException();
            }
        }
        finally
        {
            $prepared_query->close();
        }
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     */
    public function insert(string $query, ...$values): int
    {
        $prepared_query = $this->prepare_query($query, $values);
        $prepared_query->execute();

        try
        {
            if ($prepared_query->affected_rows > 0)
            {
                return $prepared_query->insert_id;
            }

            throw new DatabaseActionException();
        }
        finally
        {
            $prepared_query->close();
        }
    }

    /**
     * @param string $query The plaintext query that will be prepared.
     * @param array $parameters An array containing values of each parameter that will be used in the query.
     * @return mysqli_stmt The prepared query.
     * @throws DatabaseActionException Thrown when unable to prepare query, for example due to invalid syntax.
     */
    private function prepare_query(string $query, array $parameters): mysqli_stmt
    {
        $parameter_types = $this->get_mysql_types_of($parameters);
        $parameter_types = implode($parameter_types);

        if ($query = $this->mysqli->prepare($query))
        {
            if ($query->bind_param($parameter_types, ...$parameters))
            {
                return $query;
            }
        }

        throw new DatabaseActionException();
    }

    /**
     * @param mixed $values An array which element types will be resolved as mysql types.
     * @return array An array of mysql types corresponding to each $value's array element type.
     */
    private function get_mysql_types_of($values)
    {
        $types = [];

        foreach ($values as $value)
        {
            $types[] = $this->get_mysql_type_of($value);
        }

        return $types;
    }

    /**
     * @param mixed $value A value which type will be resolved as a mysql type.
     * @return string The mysql type of $value.
     */
    private function get_mysql_type_of($value): string
    {
        switch (true)
        {
            case is_integer($value):
                return "d";

            case is_float($value) || is_double($value):
                return "f";

            case is_object($value):
                return "b";

            default:
                return "s";
        }
    }

    /**
     * @inheritDoc
     * @throws InvalidPrimaryKeyException
     */
    public function select(string $query, $primary_key_value): array
    {
        $primary_key_type = $this->get_mysql_type_of($primary_key_value);

        $statement = $this->mysqli->prepare($query);
        $statement->bind_param($primary_key_type, $primary_key_value);
        $statement->execute();

        if ($result = $statement->get_result())
        {
            if ($result = $result->fetch_assoc())
            {
                return $result;
            }
        }

        throw new InvalidPrimaryKeyException();
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     */
    public function select_multiple(string $query): array
    {
        if ($statement = $this->mysqli->query($query))
        {
            return $statement->fetch_all(MYSQLI_ASSOC);
        }

        throw new DatabaseActionException();
    }

    /**
     * @inheritDoc
     * @throws DatabaseActionException
     */
    public function select_multiple_with_parameters(string $query, array $parameters = []): array
    {
        if ($statement = $this->prepare_query($query, $parameters))
        {
            $statement->execute();

            if ($result = $statement->get_result())
            {
                return $result->fetch_all(MYSQLI_ASSOC);
            }
        }

        throw new DatabaseActionException();
    }


    /**
     * @inheritDoc
     * @throws DatabaseActionException
     * @throws Exception
     */
    public function within_transaction(callable $action): void
    {
        $this->execute("BEGIN");

        try
        {
            $action();
        }
        catch (Exception $exception)
        {
            $this->execute("ROLLBACK");

            throw $exception;
        }

        $this->execute("COMMIT");
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->mysqli->close();
    }
}
