<?php
namespace Vadorco\Core\Persistence;

use Vadorco\Core\PropertyProxy;

/**
 * Class CachedPersistenceResolver
 * @package Vadorco\Core
 *
 * A simple PersistenceResolver's wrapper designed to cache frequently requested information.
 */
class CachedPersistenceResolver implements PersistenceResolver
{
    /**
     * @var PersistenceResolver
     */
    private $persistence_resolver;

    /**
     * @var array An array containing cached table names.
     */
    private $table_names;

    /**
     * @var array An array containing cached column definitions.
     */
    private $column_definitions;

    /**
     * @var array An array containing cached column names.
     */
    private $column_names;

    /**
     * @param PersistenceResolver $persistence_resolver
     */
    public function __construct(PersistenceResolver $persistence_resolver)
    {
        $this->persistence_resolver = $persistence_resolver;
        $this->table_names = [];
        $this->column_definitions = [];
        $this->column_names = [];
    }

    /**
     * @inheritDoc
     */
    public function resolve_table_name($object): string
    {
        return $this->fetch($object, $this->table_names, function($object) {
            return $this->persistence_resolver->resolve_table_name($object);
        });
    }

    /**
     * @param object $object An object which the data will be fetched
     * @param array $cache An array in which the requested data should be stored.
     * @param callable $load A function loading the requested data unless it's already stored in cache.
     * @return mixed The requested value.
     */
    private function fetch($object, array $cache, callable $load)
    {
        $object_type = gettype($object);

        if ( !key_exists($object_type, $cache))
        {
            $cache[$object_type] = $load($object);
        }

        return $cache[$object_type];
    }

    /**
     * @inheritDoc
     */
    public function resolve_column_definitions($object): array
    {
        return $this->fetch($object, $this->column_definitions, function($object) {
            return $this->persistence_resolver->resolve_column_definitions($object);
        });
    }

    /**
     * @inheritDoc
     */
    public function resolve_primary_key($object): PropertyProxy
    {
        return $this->persistence_resolver->resolve_primary_key($object);
    }

    /**
     * @inheritDoc
     */
    public function resolve_properties($object): array
    {
        return $this->persistence_resolver->resolve_properties($object);
    }

    /**
     * @inheritDoc
     */
    public function resolve_column_names($object): array
    {
        return $this->fetch($object, $this->column_names, function($object) {
            return $this->persistence_resolver->resolve_column_names($object);
        });
    }

    /**
     * @inheritDoc
     */
    public function resolve_as_entry($object): array
    {
        return $this->persistence_resolver->resolve_as_entry($object);
    }
}
