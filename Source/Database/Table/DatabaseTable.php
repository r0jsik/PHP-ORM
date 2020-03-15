<?php


interface DatabaseTable
{
    public function insert($entry);
    public function update($entry_id, $entry);
    public function remove($entry_id, $entry);
}
