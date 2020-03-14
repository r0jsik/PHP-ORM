<?php
interface PersistenceService
{
    public function insert($object);
    public function update($object);
    public function remove($object);
}
