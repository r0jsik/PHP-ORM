<?php


interface DatabaseTable
{
    public function insert($entry);
    public function remove($entry);
}
