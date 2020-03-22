<?php
namespace Test\Core;

use Source\Core\PrimaryKey;

class IdPrimaryKey implements PrimaryKey
{
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function get_name(): string
    {
        return "id";
    }

    public function get_value()
    {
        return $this->value;
    }

    public function set_value($value): void
    {
        // ignore
    }
}
