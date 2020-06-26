<?php
namespace Test\Database\Condition;

use PHPUnit\Framework\TestCase;
use Vadorco\Database\Condition\SimpleConditionBuilder;
use TypeError;

class ConditionBuilderTest extends TestCase
{
    private $where;

    protected function setUp(): void
    {
        $this->where = new SimpleConditionBuilder([
            "name" => "column_name"
        ]);
    }

    public function test_like()
    {
        $this->assertEquals(
            $this->where->property("name")->like("%F__"),
            "`column_name` LIKE ?"
        );
    }

    public function test_in()
    {
        $this->assertEquals(
            $this->where->property("name")->in("simple", "quote's", "double \"quotes\"", "specials !@#$%^&*()\;"),
            "`column_name` IN (?, ?, ?, ?)"
        );
    }

    public function test_between()
    {
        $this->assertEquals(
            $this->where->property("name")->between(45, 90),
            "`column_name` BETWEEN ? TO ?"
        );
    }

    public function test_between_strings()
    {
        $this->expectException(TypeError::class);
        $this->where->property("name")->between("it's not", "a number!");
    }

    public function test_lt()
    {
        $this->assertEquals(
            $this->where->property("name")->lt(88),
            "`column_name` < ?"
        );
    }

    public function test_lt_string()
    {
        $this->expectException(TypeError::class);
        $this->where->property("name")->lt("it's not a number!");
    }

    public function test_le()
    {
        $this->assertEquals(
            $this->where->property("name")->le(88),
            "`column_name` <= ?"
        );
    }

    public function test_le_string()
    {
        $this->expectException(TypeError::class);
        $this->where->property("name")->le("it's not a number!");
    }

    public function test_gt()
    {
        $this->assertEquals(
            $this->where->property("name")->gt(88),
            "`column_name` > ?"
        );
    }

    public function test_gt_string()
    {
        $this->expectException(TypeError::class);
        $this->where->property("name")->gt("it's not a number!");
    }

    public function test_ge()
    {
        $this->assertEquals(
            $this->where->property("name")->ge(88),
            "`column_name` >= ?"
        );
    }

    public function test_ge_string()
    {
        $this->expectException(TypeError::class);
        $this->where->property("name")->ge("it's not a number!");
    }

    public function test_eq()
    {
        $this->assertEquals(
            $this->where->property("name")->eq(88),
            "`column_name` = ?"
        );
    }

    public function test_ne()
    {
        $this->assertEquals(
            $this->where->property("name")->ne(88),
            "`column_name` <> ?"
        );
    }
}
