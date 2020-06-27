<?php
declare(strict_types=1);

use Vadorco\Annotation\Persistence\AnnotationPersistenceResolver;
use Vadorco\Core\ObjectFactory;
use Vadorco\Core\Persistence\CachedPersistenceResolver;
use Vadorco\Core\Persistence\LoggingPersistenceService;
use Vadorco\Database\DatabaseConnectionException;
use Vadorco\Database\MySQLiDatabase;
use Vadorco\Database\Persistence\DatabasePersistenceService;
use Vadorco\User\Client;

try
{
    //error_reporting(E_ERROR | E_PARSE);

    $database = new MySQLiDatabase("localhost", "orm", "M0xe0MeHwWzl9RMy", "php-orm");
    $persistence_resolver = new AnnotationPersistenceResolver();
    $persistence_resolver = new CachedPersistenceResolver($persistence_resolver);
    $object_factory = new ObjectFactory();
    $persistence_service = new DatabasePersistenceService($database, $persistence_resolver, $object_factory);
    $persistence_service = new LoggingPersistenceService($persistence_service);

    $client_1 = new Client();
    $client_1->set("Client name", "Client surname", "+48 123 456 789", "first@client.com");

    $client_2 = new Client();
    $client_2->set("Another user", "Another surname", "+11 999 333 666", "second@client.com");

    $persistence_service->insert($client_1);
    $persistence_service->insert($client_2);

    $client_1->setEmail("updated.email@client.com");
    $client_1->setPhone("+47 123 456 789");
    $persistence_service->update($client_1);

    $client_2->setName("Updated name");
    $persistence_service->update($client_2);

    $clients = $persistence_service->select_all(Client::class);

    echo "<pre>";
    var_dump($clients);
    echo "</pre>";

    $persistence_service->remove($client_1);
    $persistence_service->remove($client_2);
}
catch (DatabaseConnectionException $exception)
{
    echo "Unable to connect to the database";
}