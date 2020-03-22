<?php
namespace Test\Database\Table;

spl_autoload_register(function ($path) {
    require_once("$path.php");
});

use PHPUnit\Framework\TestCase;
use Source\Database\DatabaseActionException;
use Source\Database\MySQLiDatabase;
use Source\Database\Table\InvalidPrimaryKeyException;

class MySQLiDatabaseTableTest extends TestCase
{
    private $database;
    private $table;
    private $existing_primary_key = 50;
    private $not_existing_primary_key = -100;
    private $mock_primary_key = 10;

    public function setUp(): void
    {
        $this->database = new MySQLiDatabase("localhost", "orm", "M0xe0MeHwWzl9RMy", "php-orm");
        $this->table = $this->database->choose_table("existing-table", "id");
    }

    public function tearDown(): void
    {
        $this->database->close();
    }

    public function test_select_existing_entry()
    {
        $this->assert_exists($this->existing_primary_key);
    }

    private function assert_exists(int $primary_key)
    {
        $this->assertNotNull($this->table->select($primary_key));
    }

    public function test_select_not_existing_entry()
    {
        $this->assert_not_exists($this->not_existing_primary_key);
    }

    private function assert_not_exists($primary_key)
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->select($primary_key);
    }

    public function test_insert_mock_entry()
    {
        $entry = ["id" => $this->mock_primary_key];

        $this->table->insert($entry);
        $this->assert_exists($this->mock_primary_key);
        $this->table->remove($this->mock_primary_key);
    }

    public function test_insert_existing_entry()
    {
        $entry = ["id" => $this->existing_primary_key];

        $this->expectException(DatabaseActionException::class);
        $this->table->insert($entry);
    }

    public function test_insert_invalid_entry()
    {
        $entry = ["id" => $this->not_existing_primary_key, "invalid-column" => 123];

        $this->expectException(DatabaseActionException::class);
        $this->table->insert($entry);
    }

    public function test_update_mock_entry()
    {
        $entry = ["id" => $this->mock_primary_key];
        $updated_entry = ["mock" => "updated"];

        $this->table->insert($entry);
        $this->table->update($this->mock_primary_key, $updated_entry);

        $result = $this->table->select($this->mock_primary_key);
        $this->assertEquals($result["mock"], "updated");

        $this->table->remove($this->mock_primary_key);
    }

    public function test_update_not_existing_entry()
    {
        $entry = ["id" => 3612];

        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->update($this->not_existing_primary_key, $entry);
    }

    public function test_invalid_update_existing_entry()
    {
        $entry = ["id" => $this->not_existing_primary_key, "invalid-column" => 8293];

        $this->expectException(DatabaseActionException::class);
        $this->table->update($this->existing_primary_key, $entry);
    }

    public function test_remove_mock_entry()
    {
        $entry = ["id" => $this->mock_primary_key];

        $this->table->insert($entry);
        $this->table->remove($this->mock_primary_key);

        $this->assert_not_exists($this->mock_primary_key);
    }

    public function test_remove_not_existing_entry()
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->remove($this->not_existing_primary_key);
    }
}
