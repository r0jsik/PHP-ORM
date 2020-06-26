<?php
namespace Vadorco\Database\Column;

/**
 * Interface ColumnDescriptor
 * @package Vadorco\Database\Table
 *
 * An interface of the mechanism describing column definition as a string.
 * This mechanism is being used by the database that is creating new column.
 * Description of the column is used in the query, for example:
 * - id INTEGER(32) NOT NULL
 * - name VARCHAR DEFAULT 'default name'
 */
interface ColumnDescriptor
{
    /**
     * @param ColumnDefinition $column_definition A definition of the column that will be described.
     * @return string The description of the column.
     */
    public function describe(ColumnDefinition $column_definition): string;

    /**
     * @param array $column_definitions An array of the ColumnDefinition objects defining structure of the table.
     * @return string The description of the columns.
     */
    public function describe_all(array $column_definitions): string;
}
