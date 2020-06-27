# ORM
An ORM library for the PHP language.

## How to use the library?
All the configuration process is really flexible. Let's import and instantiate a few, the most basic, objects:

    $database = new MySQLiDatabase("localhost", "username", "password", "database");
    $persistence_resolver = new AnnotationPersistenceResolver();
    $object_factory = new ObjectFactory();
    $persistence_service = new DatabasePersistenceService($database, $persistence_resolver, $object_factory);

The most important object here is __$persistence_service__. It's responsible for managing objects in the database using really simple commands, like `insert`, `update`, `remove`, `select`.

## Example snippets

#### Inserting an object into the database

    $object = new MyObject();
    $persistence_service->insert($object);

#### Updating this object in the database

    $object->set_property("updated value");
    $persistence_service->update($object);

#### Removing this object from the database

    $persistence_service->remove($object);

#### Selecting an object with the specified primary key

    // To load an object identified by 7825, just call:
    $object = $persistence_service->select(MyObject::class, 7825);

#### Selecting all objects

    $objects = $persistence_service->select_all(MyObject::class);

#### Selecting all objects matched to the condition

    $objects = $persistence_service->select_on_condition(MyObject::class, function($where) {
        $where->property("name")->like("%S")->and()->property("age")->between(18, 65);
    });

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
This library is resistant to SQL Injection attacks. It can work only using minimal set of privileges:
- CREATE
- INSERT
- SELECT
- UPDATE
- DELETE

Non-mentioned query types should not be able to execute by the database user.
