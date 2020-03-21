<?php
namespace Test\Database;

spl_autoload_register(function ($path) {
    require_once("$path.php");
});

use PHPUnit\Framework\TestCase;
use Source\Database\DatabaseActionException;
use Source\Database\MySQLiDatabase;
use Source\Database\Table\TableNotFoundException;
use Test\Database\Table\InvalidColumnMockDefinition;
use Test\Database\Table\ValidColumnMockDefinition;

class MySQLiDatabaseTest extends TestCase
{
    private $database;
    private $existing_table_name = "existing-table";
    private $not_existing_table_name = "not-existing-table";
    private $mock_table_name = "mock-table";

    public function setUp(): void
    {
        $this->database = new MySQLiDatabase("localhost", "orm", "M0xe0MeHwWzl9RMy", "php-orm");
    }

    public function tearDown(): void
    {
        $this->database->close();
    }

    public function test_table_exists()
    {
        $this->assert_table_exists($this->existing_table_name);
    }

    private function assert_table_exists(string $table_name)
    {
        $this->assertTrue($this->database->table_exists($table_name));
    }

    public function test_table_not_exists()
    {
        $this->assert_table_not_exists($this->not_existing_table_name);
    }

    private function assert_table_not_exists(string $table_name)
    {
        $this->assertFalse($this->database->table_exists($table_name));
    }

    public function test_create_table_with_valid_column()
    {
        $this->assert_table_not_exists($this->mock_table_name);
        $this->database->create_table($this->mock_table_name, array(new ValidColumnMockDefinition()));
        $this->assert_table_exists($this->mock_table_name);
        $this->database->remove_table($this->mock_table_name);
        $this->assert_table_not_exists($this->mock_table_name);
    }

    public function test_create_table_with_invalid_column()
    {
        $this->expectException(DatabaseActionException::class);
        $this->assert_table_not_exists($this->mock_table_name);
        $this->database->create_table($this->mock_table_name, array(new InvalidColumnMockDefinition()));
    }

    public function test_create_existing_table()
    {
        $this->expectException(DatabaseActionException::class);
        $this->database->create_table($this->existing_table_name, array(new ValidColumnMockDefinition()));
    }

    public function test_choose_valid_table()
    {
        $table = $this->database->choose_table($this->existing_table_name, "id");
        $this->assertNotNull($table);
    }

    public function test_choose_invalid_table()
    {
        $this->expectException(TableNotFoundException::class);
        $this->database->choose_table($this->not_existing_table_name, "id");
    }
}
