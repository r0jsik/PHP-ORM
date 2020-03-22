<?php
namespace Source\Database\Table;

use Source\Core\PrimaryKey;

/**
 * Interface DatabaseTable
 * @package Source\Database\Table
 *
 * Represents a table of the database. Allows you to invoke actions modifying content of the data structure.
 */
interface DatabaseTable
{
    /**
     * @param PrimaryKey $primary_key A primary key that will be updated after successful insert.
     * @param array $entry An associative array representing a record that will be inserted to the table.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function insert(PrimaryKey $primary_key, array $entry): void;

    /**
     * @param PrimaryKey $primary_key A primary key pointing to the record that will be updated.
     * @param array $entry An associative array representing a record that will replace record
     *                     which primary key's value is equal the $primary_key_value.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function update(PrimaryKey $primary_key, array $entry): void;

    /**
     * @param PrimaryKey $primary_key A primary key pointing to the record that will be removed.
     */
    public function remove(PrimaryKey $primary_key): void;

    /**
     * @param PrimaryKey $primary_key A primary key pointing to the record that will be selected.
     * @return array An associative array representing a record stored in the table.
     *               Each element of the array is pointing from the column name to value: "column-name" => "value".
     */
    public function select(PrimaryKey $primary_key): array;
}
