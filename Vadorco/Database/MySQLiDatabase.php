<?php
namespace Vadorco\Database;

use Vadorco\Database\Driver\MySQLiDriver;
use Vadorco\Database\Dialect\SimpleDialect;

class MySQLiDatabase extends SimpleDatabase
{
    /**
     * @param string $host An address of the database host.
     * @param string $username An username of the user that database will be connected as.
     * @param string $password A password of the user connected to the database.
     * @param string $database_name A name of the database from which data will be received.
     * @throws DatabaseConnectionException Thrown when unable to connect to the database.
     */
    public function __construct(string $host, string $username, string $password, string $database_name)
    {
        $driver = new MySQLiDriver($host, $username, $password, $database_name);
        $dialect = new SimpleDialect("AUTO_INCREMENT");

        parent::__construct($driver, $dialect);
    }
}
