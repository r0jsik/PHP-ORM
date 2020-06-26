<?php
namespace Test\Database\Table;

use PHPUnit\Framework\TestCase;
use Vadorco\Database\DatabaseActionException;
use Vadorco\Core\InvalidPrimaryKeyException;
use Vadorco\Database\Driver\MySQLiDriver;
use Vadorco\Database\Driver\PDODriver;
use Vadorco\Database\SimpleDatabase;
use Test\Database\Column\MockColumnDefinition;

class SimpleDatabaseTableTest extends TestCase
{
    private $database;
    private $table_name = "test-table";
    private $table;
    private $record_id = 3456;

    public function setUp(): void
    {
        $driver = new MySQLiDriver("localhost", "orm", "M0xe0MeHwWzl9RMy", "php-orm");
        $driver = new PDODriver("mysql:dbname=php-orm", "orm", "M0xe0MeHwWzl9RMy");
        $this->database = new SimpleDatabase($driver);
        $this->create_test_table();

        $this->table = $this->database->choose_table($this->table_name, "id");
    }

    private function create_test_table()
    {
        $column_definitions = [
            new MockColumnDefinition("id", "integer", true, true, false),
            new MockColumnDefinition("col_1", "varchar(64)", false, false, true),
            new MockColumnDefinition("col_2", "varchar(64)", false, true, false)
        ];

        $this->database->create_table($this->table_name, $column_definitions);
    }

    public function tearDown(): void
    {
        $this->database->remove_table($this->table_name);
        $this->database->close();
    }

    public function test_insert()
    {
        $this->insert_record();
        $this->assert_record_exists();
    }

    private function insert_record()
    {
        $record = [
            "id" => $this->record_id,
            "col_1" => "value",
            "col_2" => "value"
        ];

        $this->table->insert($record);
    }

    private function assert_record_exists()
    {
        $this->assertNotNull($this->table->select($this->record_id));
    }

    public function test_update()
    {
        $this->insert_record();

        $record = [
            "id" => $this->record_id,
            "col_1" => "updated",
            "col_2" => "updated"
        ];

        $this->table->update($this->record_id, $record);
        $selected_record = $this->table->select($this->record_id);
        $this->assertEquals($record, $selected_record);
    }

    public function test_remove()
    {
        $this->insert_record();
        $this->remove_record();
        $this->assert_record_not_exists();
    }

    private function remove_record()
    {
        $this->table->remove($this->record_id);
    }

    private function assert_record_not_exists()
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->select($this->record_id);
    }

    public function test_select()
    {
        $record = ["id" => $this->record_id, "col_1" => "value", "col_2" => "value"];

        $this->table->insert($record);
        $selected_record = $this->table->select($this->record_id);
        $this->assertEquals($record, $selected_record);
    }

    public function test_select_all()
    {
        $record_1 = ["id" => $this->record_id + 1, "col_1" => "value A", "col_2" => "value B"];
        $record_2 = ["id" => $this->record_id + 2, "col_1" => "value C", "col_2" => "value D"];
        $record_3 = ["id" => $this->record_id + 3, "col_1" => "value E", "col_2" => "value F"];
        $records = [$record_1, $record_2, $record_3];

        $this->table->insert($record_1);
        $this->table->insert($record_2);
        $this->table->insert($record_3);

        $selected_records = $this->table->select_all();
        $this->assertEquals($records, $selected_records);
    }

    public function test_insert_unique_value_twice()
    {
        $this->expectException(DatabaseActionException::class);
        $this->insert_record();
        $this->insert_record();
    }

    public function test_update_to_duplicated_unique_value()
    {
        $record_1 = ["id" => $this->record_id + 1, "col_1" => "value A", "col_2" => "unique"];
        $record_2 = ["id" => $this->record_id + 2, "col_1" => "value B", "col_2" => ""];

        $this->table->insert($record_1);
        $this->table->insert($record_2);

        $record_2["col_2"] = "unique";

        $this->expectException(DatabaseActionException::class);
        $this->table->update($this->record_id + 2, $record_2);
    }

    public function test_remove_not_existing_record()
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->remove_record();
    }

    public function test_select_not_existing_record()
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->table->select($this->record_id);
    }

    public function test_insert_null_to_not_null_column()
    {
        $record = ["id" => $this->record_id, "col_1" => null, "col_2" => "value"];

        $this->expectException(DatabaseActionException::class);
        $this->table->insert($record);
    }

    public function test_update_to_null_on_not_null_column()
    {
        $record = ["id" => $this->record_id, "col_1" => "value A", "col_2" => "value B"];

        $this->table->insert($record);

        $record["col_1"] = null;

        try
        {
            $this->table->update($this->record_id, $record);

            $selected_record = $this->table->select($this->record_id);
            $selected_value = $selected_record["col_1"];

            $this->assertNotNull($selected_value);
        }
        catch (DatabaseActionException $exception)
        {
            $this->expectNotToPerformAssertions();
        }
    }

    public function test_record_id_returned_from_insert()
    {
        $record = ["id" => 621826, "col_1" => "value", "col_2" => "value"];

        $record_id = $this->table->insert($record);
        $this->assertEquals(621826, $record_id);
    }

    public function test_autoincrement_id_returned_from_insert()
    {
        $record_1 = ["id" => null, "col_1" => "value A", "col_2" => "value B"];
        $record_2 = ["id" => null, "col_1" => "value C", "col_2" => "value D"];

        $record_1_id = $this->table->insert($record_1);
        $record_2_id = $this->table->insert($record_2);

        $this->assertGreaterThan($record_1_id, $record_2_id);
    }

    public function test_insert_invalid_record()
    {
        $record = ["name" => "unknown", "number" => 21];

        $this->expectException(DatabaseActionException::class);
        $this->table->insert($record);
    }

    public function test_commit_transaction()
    {
        $this->database->within_transaction(function () {
            $this->insert_record();
        });

        $this->assert_record_exists();
    }

    public function test_rollback_transaction()
    {
        $record = ["id" => $this->record_id, "col_1" => "value A", "col_2" => "value B"];

        try
        {
            $this->database->within_transaction(function () use ($record) {
                $this->table->insert($record);
                $this->table->update("invalid-primary-key-value", $record);
            });
        }
        catch (InvalidPrimaryKeyException $exception)
        {
            // Record's insertion should has been rolled-back due to exception thrown during transaction
            $this->assert_record_not_exists();
        }
    }
}
