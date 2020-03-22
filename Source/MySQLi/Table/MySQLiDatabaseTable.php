<?php
namespace Source\MySQLi\Table;

use mysqli;
use mysqli_stmt;
use Source\Core\PrimaryKey;
use Source\Database\DatabaseActionException;
use Source\Database\Table\DatabaseTable;
use Source\Database\Table\InvalidPrimaryKeyException;

/**
 * Class MySQLiDatabaseTable
 * @package Source\MySQLi\Table
 *
 * A MySQLi based implementation of the DatabaseTable.
 */
class MySQLiDatabaseTable implements DatabaseTable
{
    /**
     * @var string A name of the table.
     */
    private $name;

    /**
     * @var string A name of a column that is a primary key of the table.
     */
    private $primary_key_name;

    /**
     * @var mysqli An object representing database driver.
     */
    private $mysqli;

    /**
     * @param string $name A name of the table.
     * @param string $primary_key_name A name of a column that is a primary key of the table.
     * @param mysqli $mysqli An object representing database driver.
     */
    public function __construct(string $name, string $primary_key_name, mysqli $mysqli)
    {
        $this->name = $name;
        $this->primary_key_name = $primary_key_name;
        $this->mysqli = $mysqli;
    }

    /**
     * @param PrimaryKey $primary_key A primary key that will be updated after successful insert.
     * @param mixed $entry An associative array representing a record stored in the table.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     * @throws DatabaseActionException Thrown when unable to execute inserting query.
     * @throws InvalidPrimaryKeyException Thrown when the record could not be inserted into the table due to invalid key.
     */
    public function insert(PrimaryKey $primary_key, array $entry): void
    {
        $columns = array_keys($entry);
        $columns_placeholder = implode(", ", $columns);

        $values = array_values($entry);
        $values_placeholder = str_repeat("?, ", sizeof($values) - 1) . "?";

        $query = "INSERT INTO `{$this->name}` ($columns_placeholder) VALUES ($values_placeholder);";
        $parameter_types = $this->get_mysql_types_of($values);

        $this->execute_query($query, $parameter_types, $values);

        $primary_key->set_value($this->mysqli->insert_id);
    }

    /**
     * @param mixed $values An array which element types will be resolved as mysql types.
     * @return array An array of mysql types corresponding to each $value's array element type.
     */
    private function get_mysql_types_of($values)
    {
        $types = array();

        foreach ($values as $value)
        {
            $types[] = $this->get_mysql_type_of($value);
        }

        return $types;
    }

    /**
     * @param mixed $value A value which type will be resolved as a mysql type.
     * @return string The mysql type of $value.
     */
    private function get_mysql_type_of($value): string
    {
        switch (true)
        {
            case is_integer($value):
                return "d";

            case is_float($value) || is_double($value):
                return "f";

            case is_object($value):
                return "b";

            default:
                return "s";
        }
    }

    /**
     * @param string $query The query that will be prepared.
     * @param array $parameter_types An array containing mysqli types of each parameter to describe parameters of the query.
     * @param array $parameters An array containing values of each parameter that will be used in the query.
     * @throws DatabaseActionException Thrown when unable to execute the query, for example is not prepared well.
     * @throws InvalidPrimaryKeyException Thrown when the query changed nothing, for example:
     *                                    - none of the record requested to remove was present in the table before executing query
     *                                    - none of the record has been updated due to invalid primary key
     */
    private function execute_query(string $query, array $parameter_types, array $parameters)
    {
        $parameter_types = implode($parameter_types);

        if ($query = $this->mysqli->prepare($query))
        {
            $query->bind_param($parameter_types, ...$parameters);

            try
            {
                $this->execute_prepared_query($query);
            }
            finally
            {
                $query->close();
            }
        }
        else
        {
            throw new DatabaseActionException();
        }
    }

    /**
     * @param mysqli_stmt $query A prepared query that will be executed.
     * @throws DatabaseActionException Thrown when unable to execute the query, for example is not prepared well.
     * @throws InvalidPrimaryKeyException Thrown when the query changed nothing, for example:
     *                                    - none of the record requested to remove was present in the table before executing query
     *                                    - none of the record has been updated due to invalid primary key
     */
    private function execute_prepared_query(mysqli_stmt $query)
    {
        if ($result = $query->execute())
        {
            if ($query->affected_rows == 0)
            {
                throw new InvalidPrimaryKeyException();
            }
        }
        else
        {
            throw new DatabaseActionException();
        }
    }

    /**
     * @param PrimaryKey $primary_key A primary key pointing to the record that will be updated.
     * @param mixed $entry An associative array representing a record stored in the table.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     * @throws DatabaseActionException Thrown when unable to execute query updating the table.
     * @throws InvalidPrimaryKeyException Thrown when none of the record has been updated due to invalid primary key.
     */
    public function update(PrimaryKey $primary_key, array $entry): void
    {
        $mapping_placeholder = $this->get_mapping_placeholder($entry);
        $query = "UPDATE `{$this->name}` SET $mapping_placeholder WHERE {$this->primary_key_name} = ?;";
        $values = array_values($entry);

        $parameter_types = $this->get_mysql_types_of($values);
        $primary_key_value = $primary_key->get_value();
        $primary_key_identifier_type = $this->get_mysql_type_of($primary_key_value);

        $parameter_types = array_merge($parameter_types, [$primary_key_identifier_type]);
        $parameters = array_merge($values, [$primary_key_value]);

        $this->execute_query($query, $parameter_types, $parameters);
    }

    /**
     * @param mixed $entry An associative array representing a record stored in the table.
     *                     Each element of the array is pointing from the column name to value: "column-name" => "value".
     * @return string A placeholder used by query-preparing mechanism.
     *                Each column name of the entry will be assigned to the question mark and imploded with a comma, for example:
     *                "column_name_1 = ?, column_name_2 = ?, column_name_3 = ?".
     *                This placeholder is used to build prepared statement taking into account three columns.
     */
    private function get_mapping_placeholder($entry)
    {
        $mapping_placeholders = array();

        foreach ($entry as $column => $value)
        {
            $mapping_placeholders[] = "$column = ?";
        }

        return implode(", ", $mapping_placeholders);
    }

    /**
     * @param PrimaryKey $primary_key A primary key pointing to the record that will be removed.
     * @throws DatabaseActionException Thrown when unable to execute query removing record identified by $primary_key_value.
     * @throws InvalidPrimaryKeyException Thrown when the query removed nothing.
     */
    public function remove(PrimaryKey $primary_key): void
    {
        $query = "DELETE FROM `{$this->name}` WHERE {$this->primary_key_name} = ?;";
        $primary_key_value = $primary_key->get_value();
        $primary_key_type = $this->get_mysql_type_of($primary_key_value);

        $this->execute_query($query, [$primary_key_type], [$primary_key_value]);
    }

    /**
     * @param PrimaryKey $primary_key A primary key pointing to the record that will be selected.
     * @return array An associative array representing a record stored in the table.
     *               Each element of the array is pointing from the column name to value: "column-name" => "value".
     * @throws InvalidPrimaryKeyException Thrown when $primary_key_value doesn't match any record in the database.
     */
    public function select(PrimaryKey $primary_key): array
    {
        $query = $this->select_query($primary_key);
        $query->execute();

        if ($result = $query->get_result())
        {
            if ($result = $result->fetch_assoc())
            {
                return $result;
            }
        }

        throw new InvalidPrimaryKeyException();
    }

    /**
     * @param PrimaryKey $primary_key A primary key identifying record which data will be selected.
     * @return mysqli_stmt An object representing prepared query responsible for selecting data from the database.
     */
    private function select_query(PrimaryKey $primary_key): mysqli_stmt
    {
        $primary_key_value = $primary_key->get_value();
        $primary_key_type = $this->get_mysql_type_of($primary_key_value);

        $query = "SELECT * FROM `{$this->name}` WHERE {$this->primary_key_name} = ?";
        $query = $this->mysqli->prepare($query);
        $query->bind_param($primary_key_type, $primary_key_value);

        return $query;
    }
}
