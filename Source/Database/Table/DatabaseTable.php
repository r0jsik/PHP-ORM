<?php
namespace Source\Database\Table;

interface DatabaseTable
{
    public function insert(array $entry);
    public function update($primary_key_value, array $entry);
    public function remove($primary_key_value);
}
