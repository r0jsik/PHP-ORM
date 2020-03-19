<?php
namespace Source\Database;

use mysqli;
use mysqli_stmt;
use Source\Database\Table\DatabaseTable;
use Source\Database\Table\MySQLiDatabaseTable;
use Source\Database\Table\TableNotFoundException;

class MySQLiDatabase implements Database
{
    private $mysqli;
    private $database_name;

    public function __construct(string $host, string $username, string $password, string $database_name)
    {
        $this->mysqli = new mysqli($host, $username, $password, $database_name);
        $this->database_name = $database_name;
    }

    public function table_exists(string $name): bool
    {
        $exists = null;

        $query = $this->table_exists_query($name);
        $query->execute();

        $query->bind_result($exists);
        $query->fetch();

        $query->close();

        return $exists;
    }

    private function table_exists_query(string $table_name): mysqli_stmt
    {
        $query = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?;";
        $query = $this->mysqli->prepare($query);
        $query->bind_param("ss", $this->database_name, $table_name);

        return $query;
    }

    public function create_table(string $name, array $column_definitions)
    {
        $definition_strings = array();

        foreach ($column_definitions as $column_name => $definition)
        {
            $definition_string = $column_name . " " . $definition["Type"];

            if (key_exists("Length", $definition))
            {
                $definition_string .= "(" . $definition["Length"] . ")";
            }

            if (key_exists("Unique", $definition))
            {
                $definition_string .= " UNIQUE";
            }

            if (key_exists("NotNull", $definition))
            {
                $definition_string .= " NOT NULL";
            }

            if (key_exists("Default", $definition))
            {
                $definition_string .= " DEFAULT \"" . $definition["Default"] . "\"";
            }

            if (key_exists("PrimaryKey", $definition))
            {
                $definition_string .= " PRIMARY KEY";

                if (key_exists("Autoincrement", $definition))
                {
                    $definition_string .= " AUTO_INCREMENT";
                }
            }

            $definition_strings[] = $definition_string;
        }

        $definition_string = implode(", ", $definition_strings);
        $query = "CREATE TABLE `$name` ($definition_string);";

        $this->mysqli->query($query);
    }

    public function choose_table(string $name, string $primary_key_name): DatabaseTable
    {
        if ($this->table_exists($name))
        {
            return new MySQLiDatabaseTable($name, $primary_key_name, $this->mysqli);
        }

        throw new TableNotFoundException();
    }
}