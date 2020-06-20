<?php
namespace Source\Database\Column;

/**
 * Class SQLColumnDescriptor
 * @package Source\Database\Table
 *
 * An implementation of the mechanism describing column definition in SQL dialect.
 */
class SQLColumnDescriptor implements ColumnDescriptor
{
    private $autoincrement_clause;

    public function __construct()
    {
        $this->autoincrement_clause = "AUTO_INCREMENT";
    }

    /**
     * @param ColumnDefinition $column_definition The definition of the column that will be described.
     * @return string The SQL description of the column.
     */
    public function describe(ColumnDefinition $column_definition): string
    {
        $description = "`" . $column_definition->get_name() . "` " . $column_definition->get_type();

        if ($column_definition->has_length())
        {
            $description .= "(" . $column_definition->get_length() . ")";
        }

        if ($column_definition->is_unique())
        {
            $description .= " UNIQUE";
        }

        if ($column_definition->is_not_null())
        {
            $description .= " NOT NULL";
        }

        if ($column_definition->has_default_value())
        {
            $description .= " DEFAULT \"" . $column_definition->get_default_value() . "\"";
        }

        if ($column_definition->is_primary_key())
        {
            $description .= " PRIMARY KEY";
        }

        if ($column_definition->is_autoincrement())
        {
            $description .= " " . $this->autoincrement_clause;
        }

        return $description;
    }

    /**
     * @param array $column_definitions An array of the ColumnDefinition objects defining structure of the table.
     * @return string A description of the columns received from parsing column definitions.
     */
    public function describe_all(array $column_definitions): string
    {
        $column_descriptions = array();

        foreach ($column_definitions as $column_definition)
        {
            $column_descriptions[] = $this->describe($column_definition);
        }

        return implode(", ", $column_descriptions);
    }

    /**
     * @param string $dialect The name of the queries dialect.
     */
    public function configure(string $dialect)
    {
        switch ($dialect)
        {
            case "sqlite":
                $this->autoincrement_clause = "AUTOINCREMENT";
                break;
        }
    }
}
