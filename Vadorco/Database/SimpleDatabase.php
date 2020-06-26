<?php
namespace Vadorco\Database;

use Vadorco\Database\Column\SQLColumnDescriptor;
use Vadorco\Database\Driver\Driver;
use Vadorco\Database\Table\DatabaseTable;
use Vadorco\Database\Table\SimpleDatabaseTable;
use Vadorco\Database\Table\TableNotFoundException;

class SimpleDatabase implements Database
{
    private $driver;
    private $column_descriptor;

    public function __construct(Driver $driver, $dialect = "")
    {
        $this->driver = $driver;
        $this->column_descriptor = new SQLColumnDescriptor();
        $this->column_descriptor->configure($dialect);
    }

    /**
     * @inheritDoc
     */
    public function table_exists(string $name): bool
    {
        $query = "SELECT * FROM `$name` LIMIT 0;";

        try
        {
            $this->driver->execute($query);
        }
        catch (DatabaseActionException $exception)
        {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function create_table(string $name, array $column_definitions): void
    {
        $column_description = $this->column_descriptor->describe_all($column_definitions);
        $query = "CREATE TABLE `$name` ($column_description);";

        $this->driver->execute($query);
    }

    /**
     * @inheritDoc
     * @throws TableNotFoundException Thrown when table with the specified name doesn't exist.
     */
    public function choose_table(string $name, string $primary_key_name): DatabaseTable
    {
        if ($this->table_exists($name))
        {
            return new SimpleDatabaseTable($name, $primary_key_name, $this->driver);
        }

        throw new TableNotFoundException();
    }

    /**
     * @inheritDoc
     */
    public function remove_table(string $name): void
    {
        $this->driver->execute("DROP TABLE `$name`;");
    }

    /**
     * @inheritDoc
     */
    public function within_transaction(callable $action): void
    {
        $this->driver->within_transaction($action);
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->driver->close();
    }
}
