<?php
namespace Source\Database\Table;

use mysqli;

class MySQLiDatabaseTable implements DatabaseTable
{
    private $name;
    private $primary_key_name;
    private $mysqli;

    public function __construct(string $name, string $primary_key_name, mysqli $mysqli)
    {
        $this->name = $name;
        $this->primary_key_name = $primary_key_name;
        $this->mysqli = $mysqli;
    }

    public function insert(array $entry)
    {
        $columns = array_keys($entry);
        $columns_placeholder = implode(", ", $columns);

        $values = array_values($entry);
        $values_placeholder = str_repeat("?, ", sizeof($values) - 1) . "?";

        $query = "INSERT INTO {$this->name} ($columns_placeholder) VALUES ($values_placeholder);";
        $parameter_types = $this->get_mysql_types_of($values);

        $this->execute_query($query, $parameter_types, $values);
    }

    private function get_mysql_types_of($values)
    {
        $types = array();

        foreach ($values as $value)
        {
            $types[] = $this->get_mysql_type_of($value);
        }

        return $types;
    }

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

    private function execute_query(string $query, array $parameter_types, array $parameters)
    {
        $parameter_types = implode($parameter_types);

        $query = $this->mysqli->prepare($query);
        $query->bind_param($parameter_types, ...$parameters);
        $query->execute();
        $query->close();
    }

    public function update($primary_key_value, array $entry)
    {
        $mapping_placeholder = $this->get_mapping_placeholder($entry);
        $query = "UPDATE {$this->name} SET $mapping_placeholder WHERE {$this->primary_key_name} = ?;";
        $values = array_values($entry);

        $parameter_types = $this->get_mysql_types_of($values);
        $primary_key_identifier_type = $this->get_mysql_type_of($primary_key_value);

        $parameter_types = array_merge($parameter_types, [$primary_key_identifier_type]);
        $parameters = array_merge($values, [$primary_key_value]);

        $this->execute_query($query, $parameter_types, $parameters);
    }

    private function get_mapping_placeholder($entry)
    {
        $mapping_placeholders = array();

        foreach ($entry as $column => $value)
        {
            $mapping_placeholders[] = "$column = ?";
        }

        return implode(", ", $mapping_placeholders);
    }

    public function remove($primary_key_value)
    {
        $query = "DELETE FROM {$this->name} WHERE {$this->primary_key_name} = ?;";
        $primary_key_type = $this->get_mysql_type_of($primary_key_value);

        $this->execute_query($query, [$primary_key_type], [$primary_key_value]);
    }
}
