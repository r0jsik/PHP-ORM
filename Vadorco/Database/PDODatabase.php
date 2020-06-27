<?php
namespace Vadorco\Database;

use Vadorco\Database\Dialect\SimpleDialect;
use Vadorco\Database\Driver\PDODriver;

class PDODatabase extends SimpleDatabase
{
    /**
     * @param string $dialect A name of the dialect in which the queries will be built.
     * @param string $data_source A data source.
     * @param string $username An username of the user that database will be connected as.
     * @param string $password A password of the user connected to the database.
     */
    public function __construct(string $dialect, string $data_source, string $username, string $password)
    {
        $driver = new PDODriver($data_source, $username, $password);

        switch ($dialect)
        {
            case "sqlite":
                $dialect = new SimpleDialect("AUTOINCREMENT");
                break;

            default:
                $dialect = new SimpleDialect("AUTO_INCREMENT");
                break;
        }

        parent::__construct($driver, $dialect);
    }
}
