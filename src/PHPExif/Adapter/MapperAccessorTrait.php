<?php
/**
 * Accessor & Mutator for Mapper
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 */

namespace PHPExif\Adapter;

use PHPExif\Exception\Adapter\NoMapperSetException;

/**
 * MapperAccessorTrait
 *
 * @category    PHPExif
 * @package     Adapter
 */
trait MapperAccessorTrait
{
    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * {@inheritDoc}
     */
    public function getMapper()
    {
        if (null !== $this->mapper) {
            return $this->mapper;
        }

        throw new NoAdapterSetException;
    }

    /**
     * {@inheritDoc}
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }
}
