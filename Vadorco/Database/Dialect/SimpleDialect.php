<?php
namespace Vadorco\Database\Dialect;

use Vadorco\Database\Column\ColumnDescriptor;
use Vadorco\Database\Column\SimpleColumnDescriptor;

class SimpleDialect implements Dialect
{
    private $autoincrement_clause;

    /**
     * @param string $autoincrement_clause
     */
    public function __construct($autoincrement_clause)
    {
        $this->autoincrement_clause = $autoincrement_clause;
    }

    public function get_column_descriptor(): ColumnDescriptor
    {
        return new SimpleColumnDescriptor($this->autoincrement_clause);
    }
}
