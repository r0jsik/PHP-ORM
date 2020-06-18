# ORM
An ORM library for the PHP language.

## How to use the library?
All the configuration process is really simple and flexible. Let's import and instantiate a few, the most basic, objects:

    $database = new MySQLiDatabase("localhost", "username", "password", "database");
    $persistence_resolver = new AnnotationPersistenceResolver();
    $object_factory = new ObjectFactory();
    $persistence_service = new DatabasePersistenceService($database, $persistence_resolver, $object_factory);

The most important object here is __$persistence_service__. It's responsible for managing objects in the database using really simple commands, like `insert`, `update`, `remove`, `select`.

## Supported databases
This library supports, for example, the following database drivers:
- MySQL
- PostgreSQL
- Oracle
- Microsoft SQL Server
- ODBC
- Firebird
- SQLite

Most of them are available through the __PDODatabase__ object, whereas the __MySQLiDatabase__ is preferred for MySQL databases.

## Security
This library can work only using minimal set of privileges:
- CREATE
- INSERT
- SELECT
- UPDATE
- DELETE

It is resistant to SQL Injection attacks, but non-mentioned query types should not be able to execute by the database user.
