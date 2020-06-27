<?php
namespace Vadorco\Database;

use Vadorco\Database\Condition\ConditionBuilder;
use Vadorco\Database\Condition\SimpleConditionBuilder;
use Vadorco\Database\Dialect\Dialect;
use Vadorco\Database\Driver\Driver;
use Vadorco\Database\Table\DatabaseTable;
use Vadorco\Database\Table\SimpleDatabaseTable;
use Vadorco\Database\Table\TableNotFoundException;

class SimpleDatabase implements Database
{
    /**
     * @var Driver An object representing a connection with the database.
     */
    private $driver;

    /**
     * @var Column\ColumnDescriptor
     */
    private $column_descriptor;

    /**
     * @param Driver $driver An object representing a connection with the database.
     * @param Dialect $dialect
     */
    public function __construct(Driver $driver, Dialect $dialect)
    {
        $this->driver = $driver;
        $this->column_descriptor = $dialect->get_column_descriptor();
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
    public function create_condition_builder(array $column_names): ConditionBuilder
    {
        return new SimpleConditionBuilder($column_names);
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
