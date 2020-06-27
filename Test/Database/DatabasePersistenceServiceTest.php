<?php
namespace Test\Database;

use PHPUnit\Framework\TestCase;
use Vadorco\Annotation\Persistence\AnnotationPersistenceResolver;
use Vadorco\Core\InvalidPrimaryKeyException;
use Vadorco\Core\ObjectFactory;
use Vadorco\Core\Persistence\PersistenceService;
use Vadorco\Database\Condition\ConditionBuilder;
use Vadorco\Database\Database;
use Vadorco\Database\MySQLiDatabase;
use Vadorco\Database\PDODatabase;
use Vadorco\Database\Persistence\DatabasePersistenceService;
use Vadorco\User\Client;

class DatabasePersistenceServiceTest extends TestCase
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @var PersistenceService
     */
    private $persistence_service;

    public function setUp(): void
    {
        //$this->database = new MySQLiDatabase("localhost", "orm", "M0xe0MeHwWzl9RMy", "php-orm");
        $this->database = new PDODatabase("mysql", "mysql:dbname=php-orm", "orm", "M0xe0MeHwWzl9RMy");
        //$this->database = new PDODatabase("sqlite", "sqlite:Resources/test.db", "", "");

        $persistence_resolver = new AnnotationPersistenceResolver();
        $object_factory = new ObjectFactory();
        $this->persistence_service = new DatabasePersistenceService($this->database, $persistence_resolver, $object_factory);
    }

    public function tearDown(): void
    {
        $this->database->remove_table("clients");
        $this->database->close();
    }

    public function test_insert()
    {
        $client = $this->create_client();
        $this->persistence_service->insert($client);
        $client_id = $client->getId();

        $this->assert_exists($client_id);
    }

    private function create_client(string $name = "Name", string $surname = "Surname"): Client
    {
        $client = new Client();
        $client->setName($name);
        $client->setSurname($surname);

        return $client;
    }

    private function assert_exists(int $client_id)
    {
        $this->assertNotNull($this->persistence_service->select(Client::class, $client_id));
    }

    public function test_update()
    {
        $client = $this->create_client();
        $client->setEmail("Invalid email");

        $this->persistence_service->insert($client);
        $client_id = $client->getId();

        $client->setEmail("Updated email");

        $this->persistence_service->update($client);
        $selected_client = $this->select_client($client_id);
        $client_email = $selected_client->getEmail();

        $this->assertEquals("Updated email", $client_email);
    }

    private function select_client(int $client_id)
    {
        return $this->persistence_service->select(Client::class, $client_id);
    }

    public function test_remove()
    {
        $client = $this->create_client();
        $this->persistence_service->insert($client);
        $this->persistence_service->remove($client);
        $client_id = $client->getId();

        $this->assert_not_exists($client_id);
    }

    private function assert_not_exists(int $client_id)
    {
        $this->expectException(InvalidPrimaryKeyException::class);
        $this->select_client($client_id);
    }

    public function test_select()
    {
        $client = $this->create_client();
        $this->persistence_service->insert($client);
        $client_id = $client->getId();
        $selected_client = $this->select_client($client_id);

        $this->assertEquals($client, $selected_client);
    }

    public function test_select_all()
    {
        $client_1 = $this->create_client();
        $client_2 = $this->create_client();
        $client_3 = $this->create_client();
        $clients = [$client_1, $client_2, $client_3];

        $this->insert_all($client_1, $client_2, $client_3);
        $selected_clients = $this->persistence_service->select_all(Client::class);

        $this->assertEquals($clients, $selected_clients);
    }

    private function insert_all(...$clients)
    {
        foreach ($clients as $client)
        {
            $this->persistence_service->insert($client);
        }
    }

    public function test_select_individually()
    {
        $client_1 = $this->create_client("Client name A", "Client Surname B");
        $client_2 = $this->create_client("Client name C", "Client Surname D");
        $client_3 = $this->create_client("Client name E", "Client Surname F");
        $clients = [$client_1, $client_3];

        $this->insert_all($client_1, $client_2, $client_3);

        $selected_clients = $this->persistence_service->select_individually(Client::class, function ($entry) {
            return $entry["name"] != "Client name C";
        });

        $this->assertEquals($clients, $selected_clients);
    }

    public function test_select_where()
    {
        $client_1 = $this->create_client("Client name A", "Client Surname B");
        $client_2 = $this->create_client("Client name C", "Client Surname D");
        $client_3 = $this->create_client("Client name E", "Client Surname F");
        $clients = [$client_3];

        $this->insert_all($client_1, $client_2, $client_3);

        $selected_clients = $this->persistence_service->select_on_condition(Client::class, function(ConditionBuilder $where) {
            $where->property("surname")->like("%F");
        });

        $this->assertEquals($clients, $selected_clients);
    }

    public function test_remove_not_existing()
    {
        $client = $this->create_client();
        $this->persistence_service->insert($client);
        $this->persistence_service->remove($client);

        $this->expectException(InvalidPrimaryKeyException::class);
        $this->persistence_service->remove($client);
    }
}
