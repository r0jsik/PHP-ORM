<?php


interface DatabaseTable
{
    public function insert($entry);
    public function update($primary_key_value, $entry);
    public function remove($primary_key_value);
}
