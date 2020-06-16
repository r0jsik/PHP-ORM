<?php
namespace Source\PDO;

use Exception;
use PDO;
use Source\Database\Database;
use Source\Database\DatabaseActionException;
use Source\Database\Table\DatabaseTable;
use Source\Database\Table\TableNotFoundException;
use Source\Database\Column\SQLColumnDescriptor;
use Source\PDO\Table\PDODatabaseTable;

class PDODatabase implements Database
{
    private $pdo;
    private $column_descriptor;

    public function __construct(string $driver, string $data_source, string $username, string $password)
    {
        $this->pdo = new PDO("$driver:$data_source", $username, $password);
        $this->column_descriptor = new SQLColumnDescriptor();
        $this->column_descriptor->configure($driver);
    }

    /**
     * @param string $name The name of the table.
     * @return bool A flag checking if the table exists in the database.
     */
    public function table_exists(string $name): bool
    {
        return $this->pdo->query("SELECT * FROM `$name` LIMIT 0;") == true;
    }

    /**
     * @param string $name A name of the table.
     * @param array $column_definitions An array of the ColumnDefinition objects defining structure of the table.
     * @throws DatabaseActionException Thrown when unable to execute the query.
     */
    public function create_table(string $name, array $column_definitions): void
    {
        $columns_description = $this->column_descriptor->describe_all($column_definitions);
        $query = "CREATE TABLE `$name` ($columns_description);";
        $this->execute_query($query);
    }

    /**
     * @param string $query A query that will be executed by the database driver.
     * @throws DatabaseActionException Thrown when unable to execute the query.
     */
    private function execute_query(string $query)
    {
        if ( !$this->pdo->query($query))
        {
            throw new DatabaseActionException("Unable to execute the query");
        }
    }

    /**
     * @param string $name A name of the table that will be fetched.
     * @param string $primary_key_name A name of the primary key column.
     * @return DatabaseTable An object representing fetched table.
     * @throws TableNotFoundException Thrown when table with the specified name doesn't exist.
     */
    public function choose_table(string $name, string $primary_key_name): DatabaseTable
    {
        if ($this->table_exists($name))
        {
            return new PDODatabaseTable($name, $primary_key_name, $this->pdo);
        }

        throw new TableNotFoundException();
    }

    /**
     * @param string $name A name of the table that will be removed.
     * @throws DatabaseActionException Thrown when unable to execute the query.
     */
    public function remove_table(string $name): void
    {
        $this->execute_query("DROP TABLE `$name`;");
    }

    /**
     * @param callable $action An action that will be invoked within transaction.
     *                         If the action throws an exception, the transaction will be interrupted
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
     * Closes the connection.
     */
    public function close(): void
    {
        $this->pdo = null;
    }
}
