<?php
namespace Source\Database\Table;

interface ColumnDescriptor
{
    public function describe(ColumnDefinition $column_definition): string;
}
