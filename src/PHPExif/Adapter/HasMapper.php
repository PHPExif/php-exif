<?php
/**
 * HasMapper interface
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 * @codeCoverageIgnore
 */

namespace PHPExif\Adapter;

/**
 * HasMapper
 *
 * Defines a public API for a accessing the mapper
 *
 * @category    PHPExif
 * @package     Adapter
 */
interface HasMapper
{
    /**
     * Accessor for the data mapper
     *
     * @return MapperInterface
     */
    public function getMapper();

    /**
     * Mutator for the data mapper
     *
     * @param MapperInterface $mapper
     *
     * @return void
     */
    public function setMapper(MapperInterface $mapper);
}
