<?php
namespace Source\Database\Driver;

/**
 * Interface Driver
 * @package Source\Database\Driver
 *
 * Represents a database driver.
 */
interface Driver
{
    /**
     * @param string $query A query that will be executed by the database driver.
     */
    public function execute(string $query): void;

    /**
     * @param string $query A query that will be executed by the database driver.
     * @param mixed ...$parameters A list of parameters that the query will be executed with.
     */
    public function execute_prepared(string $query, ...$parameters): void;

    /**
     * @param string $query A query that will be executed by the database driver.
     * @param mixed ...$values A list of associations representing a record stored in the table.
     *                         Each element of the list is pointing from the column name to value: "column-name" => "value".
     * @return int Index of inserted record. If the table has an autoincrement index, value of the index will be returned.
     */
    public function insert(string $query, ...$values): int;

    /**
     * @param string $query A query that will be executed by the database driver.
     * @param mixed $primary_key_value A primary key pointing to the record that will be selected.
     * @return array An associative array representing a record stored in the table.
     *               Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function select(string $query, $primary_key_value): array;

    /**
     * @param string $query A query that will be executed by the database driver.
     * @return array An array of associative arrays representing records stored in the table.
     *               Each element of the associative array is pointing from the column name to value:
     *               "column-name" => "value".
     */
    public function select_multiple(string $query): array;

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
