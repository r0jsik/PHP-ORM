<?php
namespace Source\Database\Table;

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
     */
    public function insert(array $entry): void;

    /**
     * @param $primary_key_value
     * @param array $entry An associative array representing a record that will replace record
     *                     which primary key's value is equal the $primary_key_value.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function update($primary_key_value, array $entry): void;

    /**
     * @param mixed $primary_key_value Value of the primary key pointing to the record that will be removed.
     */
    public function remove($primary_key_value): void;

    /**
     * @param mixed $primary_key_value A value of the primary key pointing to the record that will be selected.
     * @return array An associative array representing a record stored in the table.
     *               Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function select($primary_key_value): array;
}
