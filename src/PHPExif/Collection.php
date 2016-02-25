<?php
/**
 * Collection interface defines public api for a collection
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif;

use PHPExif\Exception\Collection\ElementAlreadyExistsException;
use PHPExif\Exception\Collection\ElementNotExistsException;

/**
 * Collection interface
 *
 * Public API for a collection of elements
 *
 * @category    PHPExif
 * @package     Exif
 */
interface Collection
{
    /**
     * Adds a $value to the elements with key $key
     *
     * @param string $key
     * @param mixed $value
     *
     * @throws ElementAlreadyExistsException When element with $name is
     * already present in the collection
     *
     * @return Collection
     */
    public function add($key, $value);

    /**
     * Determines if element with given key exists in the collection
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key);

    /**
     * Retrieves element with given key
     *
     * @throws ElementNotExistsException When requested key is not present
     *
     * @return mixed
     */
    public function get($key);
}
