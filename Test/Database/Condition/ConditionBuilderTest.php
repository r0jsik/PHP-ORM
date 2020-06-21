<?php
namespace Test\Database\Condition;

spl_autoload_register(function ($path) {
    $path = str_replace("\\", "/", $path);
    require_once("$path.php");
});

use PHPUnit\Framework\TestCase;
use Source\Database\Condition\SimpleConditionBuilder;
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
            "`column_name` LIKE '%F__'"
        );
    }

    public function test_encoded_like()
    {
        $this->assertEquals(
            $this->where->property("name")->like("'&^*[]\"#"),
            "`column_name` LIKE '\'&^*[]\\\"#'"
        );
    }

    public function test_in()
    {
        $this->assertEquals(
            $this->where->property("name")->in("simple", "quote's", "double \"quotes\"", "specials !@#$%^&*()\;"),
            "`column_name` IN ('simple', 'quote\'s', 'double \\\"quotes\\\"', 'specials !@#$%^&*()\\\\;')"
        );
    }

    public function test_between()
    {
        $this->assertEquals(
            $this->where->property("name")->between(45, 90),
            "`column_name` BETWEEN '45' TO '90'"
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
            "`column_name` < '88'"
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
            "`column_name` <= '88'"
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
            "`column_name` > '88'"
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
            "`column_name` >= '88'"
        );
    }

    public function test_ge_string()
    {
        $this->expectException(TypeError::class);
        $this->where->property("name")->ge("it's not a number!");
    }

    public function test_eq_number()
    {
        $this->assertEquals(
            $this->where->property("name")->eq(88),
            "`column_name` = '88'"
        );
    }

    public function test_eq_string()
    {
        $this->assertEquals(
            $this->where->property("name")->eq("McDonald's"),
            "`column_name` = 'McDonald\\'s'"
        );
    }

    public function test_ne_number()
    {
        $this->assertEquals(
            $this->where->property("name")->ne(88),
            "`column_name` <> '88'"
        );
    }

    public function test_ne_string()
    {
        $this->assertEquals(
            $this->where->property("name")->ne("McDonald's"),
            "`column_name` <> 'McDonald\\'s'"
        );
    }
}
