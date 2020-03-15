<?php
interface Database
{
    public function table_exists($name) : bool;
    public function create_table($name, $column_definitions);
    public function choose_table($name, $primary_key_column_name) : DatabaseTable;
}
