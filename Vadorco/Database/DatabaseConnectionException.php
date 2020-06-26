<?php
namespace Vadorco\Database;

use Exception;

/**
 * Class DatabaseConnectionException
 * @package Vadorco\Database
 *
 * The exception is thrown when unable to connect to the database.
 */
class DatabaseConnectionException extends Exception
{
    public function __construct($code)
    {
        parent::__construct("", $code, null);
    }
}
