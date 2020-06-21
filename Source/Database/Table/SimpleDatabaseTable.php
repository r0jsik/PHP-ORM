<?php
namespace Source\Database\Table;

use Source\Database\Condition\ConditionBuilder;
use Source\Database\Driver\Driver;

class SimpleDatabaseTable implements DatabaseTable
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
     * @var Driver An object representing database driver.
     */
    private $driver;

    public function __construct(string $name, string $primary_key_name, Driver $driver)
    {
        $this->name = $name;
        $this->primary_key_name = $primary_key_name;
        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    public function insert(array $entry): int
    {
        $columns = array_keys($entry);
        $columns_placeholder = implode("`, `", $columns);

        $values = array_values($entry);
        $values_placeholder = str_repeat("?, ", sizeof($values) - 1) . "?";

        $query = "INSERT INTO `{$this->name}` (`$columns_placeholder`) VALUES ($values_placeholder);";
        $record_id = $this->driver->insert($query, ...$values);

        return $record_id;
    }

    /**
     * @inheritDoc
     */
    public function update($primary_key_value, array $entry): void
    {
        $mapping_placeholder = $this->get_mapping_placeholder($entry);
        $query = "UPDATE `{$this->name}` SET $mapping_placeholder WHERE `{$this->primary_key_name}` = ?;";
        $parameters = array_values($entry);

        array_push($parameters, $primary_key_value);

        $this->driver->execute_prepared($query, ...$parameters);
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
            $mapping_placeholders[] = "`$column` = ?";
        }

        return implode(", ", $mapping_placeholders);
    }

    /**
     * @inheritDoc
     */
    public function remove($primary_key_value): void
    {
        $query = "DELETE FROM `{$this->name}` WHERE `{$this->primary_key_name}` = ?;";
        $this->driver->execute_prepared($query, $primary_key_value);
    }

    /**
     * @inheritDoc
     */
    public function select($primary_key_value): array
    {
        $query = "SELECT * FROM `{$this->name}` WHERE `{$this->primary_key_name}` = ?";
        $record = $this->driver->select($query, $primary_key_value);

        return $record;
    }

    /**
     * @inheritDoc
     */
    public function select_all(): array
    {
        return $this->driver->select_multiple("SELECT * FROM `{$this->name}`;");
    }

    /**
     * @inheritDoc
     */
    public function select_where(ConditionBuilder $condition): array
    {
        return $this->driver->select_multiple_with_parameters("SELECT * FROM `{$this->name}` WHERE $condition;", $condition->get_parameters());
    }
}
