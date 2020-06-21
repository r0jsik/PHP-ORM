<?php
namespace Source\Database\Table;

use Source\Database\Condition\ConditionBuilder;

/**
 * Interface DatabaseTable
 * @package Source\Database\Table
 *
 * Represents a table of the database. Allows you to invoke actions modifying content of the data structure.
 */
interface DatabaseTable
{
    /**
     * @param array $entry An associative array representing a record that will be inserted to the table.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     * @return int Unique value of inserted record's primary key.
     *             If the table has an autoincrement index, value of the index will be returned.
     */
    public function insert(array $entry): int;

    /**
     * @param mixed $primary_key_value A primary key pointing to the record that will be updated.
     * @param array $entry An associative array representing a record that will replace record
     *                     which primary key's value is equal the $primary_key_value.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function update($primary_key_value, array $entry): void;

    /**
     * @param mixed $primary_key_value A primary key pointing to the record that will be removed.
     */
    public function remove($primary_key_value): void;

    /**
     * @param mixed $primary_key_value A primary key pointing to the record that will be selected.
     * @return array An associative array representing a record stored in the table.
     *               Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function select($primary_key_value): array;

    /**
     * @return array An array of associative arrays representing records stored in the table.
     *               Each element of the associative array is pointing from the column name to value:
     *               "column-name" => "value".
     */
    public function select_all(): array;

    /**
     * @param ConditionBuilder $condition An object building the query that will be appended to the WHERE clause.
     * @return array An array of associative arrays representing records stored in the table.
     *               Each element of the associative array is pointing from the column name to value:
     *               "column-name" => "value".
     */
    public function select_where(ConditionBuilder $condition): array;
}
