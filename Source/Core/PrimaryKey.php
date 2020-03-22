<?php
namespace Source\Core;

/**
 * Interface PrimaryKey
 * @package Source\Core
 */
interface PrimaryKey
{
    /**
     * @return mixed A name of the primary key.
     */
    public function get_name(): string;

    /**
     * @return mixed A value of the primary key.
     */
    public function get_value();

    /**
     * @param mixed $value The value that will be assigned to the primary key.
     */
    public function set_value($value): void;
}
