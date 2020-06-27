<?php
namespace Vadorco\Database;

use Vadorco\Database\Condition\ConditionBuilder;
use Vadorco\Database\Table\DatabaseTable;

/**
 * Interface Database
 * @package Vadorco\Database
 *
 * Represents a database and allows you to manage content of the data structure.
 */
interface Database
{
    /**
     * @param string $name A name of the table.
     * @return bool A flag checking if the table with specified name exists in the database.
     */
    public function table_exists(string $name): bool;

    /**
     * @param string $name A name of the table that will be created.
     * @param array $column_definitions Array of ColumnDefinition objects describing structure of the table.
     */
    public function create_table(string $name, array $column_definitions): void;

    /**
     * @param string $name A name of the table that will be fetched.
     * @param string $primary_key_name A name of the primary key column.
     * @return DatabaseTable An object representing fetched table.
     */
    public function choose_table(string $name, string $primary_key_name): DatabaseTable;

    /**
     * @param string $name A name of the table that will be removed.
     */
    public function remove_table(string $name): void;

    /**
     * @param array $column_names An associative array mapping object's property names to corresponding column names.
     * @return ConditionBuilder An object used for building the condition which will be appended to the query.
     */
    public function create_condition_builder(array $column_names): ConditionBuilder;

    /**
     * @param callable $action An action that will be invoked within transaction.
     *                         If the action throws an exception, the transaction is interrupted and rolled-back.
     */
    public function within_transaction(callable $action): void;

    /**
     * Closes the connection.
     */
    public function close(): void;
}
