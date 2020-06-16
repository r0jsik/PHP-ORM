<?php
namespace Source\Database\Column;

/**
 * Interface ColumnDefinition
 * @package Source\Database\Table
 *
 * An interface delivering information about column to the ColumnDescriptor.
 */
interface ColumnDefinition
{
    /**
     * @return string A name of the column.
     */
    public function get_name(): string;

    /**
     * @return string A data type stored in the column.
     */
    public function get_type(): string;

    /**
     * @return bool A flag checking if the definition includes information about length of the column.
     */
    public function has_length(): bool;

    /**
     * @return int An integer informing about length of the column.
     */
    public function get_length(): int;

    /**
     * @return bool A flag checking if the column can contain null values.
     */
    public function is_not_null(): bool;

    /**
     * @return bool A flag checking if the column has to contain unique values.
     */
    public function is_unique(): bool;

    /**
     * @return bool A flag informing if the column has defined default value.
     */
    public function has_default_value(): bool;

    /**
     * @return mixed A default value of the column.
     */
    public function get_default_value();

    /**
     * @return bool A flag checking if the column is a primary key.
     */
    public function is_primary_key(): bool;

    /**
     * @return bool A flag checking if the primary key is auto incrementing.
     */
    public function is_autoincrement(): bool;
}
