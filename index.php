<?php
declare(strict_types=1);

spl_autoload_register(function ($path) {
    require_once("$path.php");
});

use Source\Annotation\AnnotationPersistenceResolver;
use Source\Core\LoggingPersistenceService;
use Source\Database\DatabasePersistenceService;
use Source\Database\MySQLiDatabase;
use Source\User\Client;

$database = new MySQLiDatabase("localhost", "orm", "M0xe0MeHwWzl9RMy", "php-orm");
$persistence_resolver = new AnnotationPersistenceResolver();
$persistence_service = new DatabasePersistenceService($database, $persistence_resolver);
$persistence_service = new LoggingPersistenceService($persistence_service);

$client_1 = new Client("Client name", "Client surname", "+48 123 456 789", "first@client.com");
$client_2 = new Client("Another user", "Another surname", "+11 999 333 666", "second@client.com");

// temporary
$persistence_resolver->resolve_column_definitions($client_1);

$persistence_service->insert($client_1);
$persistence_service->insert($client_2);

$client_1->setEmail("updated.email@client.com");
$client_1->setPhone("+47 123 456 789");
$persistence_service->update($client_1);

$client_2->setName("Updated name");
$persistence_service->update($client_2);

$persistence_service->remove($client_1);
$persistence_service->remove($client_2);