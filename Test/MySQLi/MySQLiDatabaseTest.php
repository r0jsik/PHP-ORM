<?php
namespace Test\MySQLi;

spl_autoload_register(function ($path) {
    require_once("$path.php");
});

use PHPUnit\Framework\TestCase;
use Source\Database\DatabaseActionException;
use Source\Database\Table\TableNotFoundException;
use Source\MySQLi\MySQLiDatabase;
use Test\Database\Table\InvalidMockColumnDefinition;
use Test\Database\Table\ValidMockColumnDefinition;

class MySQLiDatabaseTest extends TestCase
{
    private $database;
    private $table_name = "mock-table";

    public function setUp(): void
    {
        $this->database = new MySQLiDatabase("localhost", "orm", "M0xe0MeHwWzl9RMy", "php-orm");
        $this->clear_database();
    }

    private function clear_database()
    {
        if ($this->database->table_exists($this->table_name))
        {
            $this->database->remove_table($this->table_name);
        }
    }

    public function tearDown(): void
    {
        $this->clear_database();
        $this->database->close();
    }

    public function test_create_table()
    {
        $this->create_table();
        $this->assert_table_exists();
    }

    private function create_table(array $column_definitions = null)
    {
        if ($column_definitions == null)
        {
            $column_definitions = [
                new ValidMockColumnDefinition()
            ];
        }

        $this->database->create_table($this->table_name, $column_definitions);
    }

    private function assert_table_exists()
    {
        $this->assertTrue($this->database->table_exists($this->table_name));
    }

    public function test_remove_table()
    {
        $this->create_table();
        $this->remove_table();
        $this->assert_table_not_exists();
    }

    private function remove_table()
    {
        $this->database->remove_table($this->table_name);
    }

    private function assert_table_not_exists()
    {
        $this->assertFalse($this->database->table_exists($this->table_name));
    }

    public function test_create_table_while_already_exists()
    {
        $this->expectException(DatabaseActionException::class);
        $this->create_table();
        $this->create_table();
    }

    public function test_remove_not_existing_table()
    {
        $this->expectException(DatabaseActionException::class);
        $this->remove_table();
    }

    public function test_create_table_with_invalid_column_definition()
    {
        $column_definitions = [
            new InvalidMockColumnDefinition()
        ];

        $this->expectException(DatabaseActionException::class);
        $this->create_table($column_definitions);
    }

    public function test_choose_existing_table()
    {
        $this->create_table();
        $table = $this->database->choose_table($this->table_name, ValidMockColumnDefinition::$primary_key_name);
        $this->assertNotNull($table);
    }

    public function test_choose_not_existing_table()
    {
        $this->expectException(TableNotFoundException::class);
        $this->database->choose_table($this->table_name, ValidMockColumnDefinition::$primary_key_name);
    }
}
