<?php
/**
 * Abstract implementation of a Collection
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
use PHPExif\Exception\Collection\InvalidElementTypeException;

/**
 * AbstractCollection class
 *
 * @category    PHPExif
 * @package     Exif
 */
class ReaderCollection extends AbstractCollection
{
    /**
     * {@inheritDoc}
     */
    public function add($key, $value)
    {
        if ($this->exists($key)) {
            throw ElementAlreadyExistsException::withKey($key);
        }

        if (!($value instanceof ReaderInterface)) {
            throw InvalidElementTypeException::withExpectedType(
                'ReaderInterface'
            );
        }

        $this->elements[$key] = $value;

        return $this;
    }
}
