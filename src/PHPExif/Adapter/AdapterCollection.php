<?php
/**
 * AdapterCollection class
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 * @codeCoverageIgnore
 */

namespace PHPExif\Adapter;

use PHPExif\Collection;
use PHPExif\Exception\Adapter\AdapterAlreadyRegisteredException;
use PHPExif\Exception\Collection\InvalidElementTypeException;

/**
 * AdapterCollection
 *
 * List of adapters with their names & configurations
 *
 * @category    PHPExif
 * @package     Adapter
 */
final class AdapterCollection implements Collection
{
    /**
     * Holds the entries of the collection
     *
     * @var array
     */
    private $elements;

    /**
     * AdapterCollection constructor
     *
     * @param array $data
     */
    public function __construct(array $elements = array())
    {
        $this->elements = array();

        foreach ($elements as $name => $config) {
            $this->add($name, $config);
        }
    }

    /**
     * {@inheritDoc}
     *
     * Adds an AdapterConfig to the collection
     *
     * @throws AdapterAlreadyExistsException When adapter with $name is
     * already present in the collection
     * @throws InvalidElementTypeException When not a valid AdapterConfig object is added
     */
    public function add($key, $value)
    {
        if ($this->exists($key)) {
            throw AdapterAlreadyExistsException::withName($key);
        }

        if (!($value instanceof AdapterConfig)) {
            throw InvalidElementTypeException::withExpectedType(
                'AdapterConfig'
            );
        }

        $this->elements[$key] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->elements);
    }

    /**
     * {@inheritDoc}
     *
     * @throws AdapterNotRegisteredException When requested adapter is not in the collection
     */
    public function get($key)
    {
        if (!$this->exists($key)) {
            throw AdapterNotRegistered::withName($key);
        }

        return $this->element[$key];
    }
}
