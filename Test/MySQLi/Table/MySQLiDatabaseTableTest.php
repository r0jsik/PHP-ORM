<?php
namespace Test\MySQLi\Table;

spl_autoload_register(function ($path) {
    require_once("$path.php");
});

use PHPUnit\Framework\TestCase;
use Source\Database\DatabaseActionException;
use Source\Database\Table\InvalidPrimaryKeyException;
use Source\MySQLi\MySQLiDatabase;

class MySQLiDatabaseTableTest extends TestCase
{
    private $database;
    private $table;
    private $existing_primary_key_value = 50;
    private $not_existing_primary_key_value = -100;
    private $mock_primary_key_value = 10;

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
        $this->assert_entry_exists($this->existing_primary_key_value);
    }

    private function assert_entry_exists(int $primary_key_value)
    {
        $this->assertNotNull($this->table->select($primary_key_value));
    }

    public function test_select_not_existing_entry()
    {
        $this->assert_entry_not_exists($this->not_existing_primary_key_value);
    }

    private function assert_entry_not_exists(int $primary_key_value)
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->select($primary_key_value);
    }

    public function test_insert_mock_entry()
    {
        $entry = ["id" => $this->mock_primary_key_value];

        $this->table->insert($entry);
        $this->assert_entry_exists($this->mock_primary_key_value);
        $this->table->remove($this->mock_primary_key_value);
    }

    public function test_insert_existing_entry()
    {
        $entry = ["id" => $this->existing_primary_key_value];

        $this->expectException(DatabaseActionException::class);
        $this->table->insert($entry);
    }

    public function test_insert_invalid_entry()
    {
        $entry = ["id" => $this->not_existing_primary_key_value, "invalid-column" => 123];

        $this->expectException(DatabaseActionException::class);
        $this->table->insert($entry);
    }

    public function test_update_mock_entry()
    {
        $entry = ["id" => $this->mock_primary_key_value];
        $updated_entry = ["mock" => "updated"];

        $this->table->insert($entry);
        $this->table->update($this->mock_primary_key_value, $updated_entry);

        $result = $this->table->select($this->mock_primary_key_value);
        $result = $result["mock"];
        $this->assertEquals($result, "updated");

        $this->table->remove($this->mock_primary_key_value);
    }

    public function test_update_not_existing_entry()
    {
        $entry = ["id" => 3612];

        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->update($this->not_existing_primary_key_value, $entry);
    }

    public function test_invalid_update_existing_entry()
    {
        $entry = ["id" => $this->not_existing_primary_key_value, "invalid-column" => 8293];

        $this->expectException(DatabaseActionException::class);
        $this->table->update($this->existing_primary_key_value, $entry);
    }

    public function test_remove_mock_entry()
    {
        $entry = ["id" => $this->mock_primary_key_value];

        $this->table->insert($entry);
        $this->table->remove($this->mock_primary_key_value);

        $this->assert_entry_not_exists($this->mock_primary_key_value);
    }

    public function test_remove_not_existing_entry()
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->remove($this->not_existing_primary_key_value);
    }
}
