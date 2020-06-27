<?php
namespace Vadorco\Database\Dialect;

use Vadorco\Database\Column\ColumnDescriptor;

interface Dialect
{
    public function get_column_descriptor(): ColumnDescriptor;
}
