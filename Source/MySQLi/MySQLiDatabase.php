<?php
namespace Source\MySQLi;

use mysqli;
use mysqli_stmt;
use Source\Database\Database;
use Source\Database\DatabaseActionException;
use Source\Database\DatabaseConnectionException;
use Source\Database\Table\DatabaseTable;
use Source\Database\Table\TableNotFoundException;
use Source\MySQLi\Table\MySQLiColumnDescriptor;
use Source\MySQLi\Table\MySQLiDatabaseTable;

/**
 * Class MySQLiDatabase
 * @package Source\MySQLi
 *
 * Represents a MySQLi-based implementation of the Database.
 */
class MySQLiDatabase implements Database
{
    /**
     * @var mysqli An object representing driver of the database.
     */
    private $mysqli;

    /**
     * @var string A name of the database.
     */
    private $database_name;

    /**
     * @var MySQLiColumnDescriptor An object describing column definitions in MySQL dialect.
     */
    private $column_descriptor;

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
        $this->database_name = $database_name;
        $this->column_descriptor = new MySQLiColumnDescriptor();
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
     * @param string $name The name of the table.
     * @return bool A flag checking if the table exists in the database.
     */
    public function table_exists(string $name): bool
    {
        $exists = null;

        $query = $this->table_exists_query($name);
        $query->execute();

        $query->bind_result($exists);
        $query->fetch();

        $query->close();

        return $exists;
    }

    /**
     * @param string $table_name A name of the table.
     * @return mysqli_stmt An object representing prepared query checking if the table with the specified name exists in the database.
     */
    private function table_exists_query(string $table_name): mysqli_stmt
    {
        $query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?;";
        $query = $this->mysqli->prepare($query);
        $query->bind_param("ss", $this->database_name, $table_name);

        return $query;
    }

    /**
     * @param string $name A name of the table.
     * @param array $column_definitions An array of the ColumnDefinition objects defining structure of the table.
     * @throws DatabaseActionException Thrown when unable to execute query creating table.
     */
    public function create_table(string $name, array $column_definitions): void
    {
        $columns_description = $this->describe_columns($column_definitions);
        $query = "CREATE TABLE `$name` ($columns_description);";
        $this->execute_query($query);
    }

    /**
     * @param array $column_definitions An array of the ColumnDefinition objects defining structure of the table.
     * @return string A description of the columns received from parsing column definitions.
     */
    private function describe_columns(array $column_definitions): string
    {
        $column_descriptions = array();

        foreach ($column_definitions as $column_definition)
        {
            $column_descriptions[] = $this->column_descriptor->describe($column_definition);
        }

        return implode(", ", $column_descriptions);
    }

    /**
     * @param string $query A query that will be executed by the database driver.
     * @throws DatabaseActionException Thrown when unable to execute the query.
     */
    private function execute_query(string $query)
    {
        if ( !$this->mysqli->query($query))
        {
            throw new DatabaseActionException();
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
            return new MySQLiDatabaseTable($name, $primary_key_name, $this->mysqli);
        }

        throw new TableNotFoundException();
    }

    /**
     * @param string $name A name of the table that will be removed.
     * @throws DatabaseActionException Thrown when unable to remove table with the specified name.
     */
    public function remove_table(string $name): void
    {
        $this->execute_query("DROP TABLE `$name`;");
    }

    /**
     * Closes the connection.
     */
    public function close(): void
    {
        $this->mysqli->close();
    }
}