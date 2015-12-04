<?php
/**
 * Exif: A container class for EXIF data
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data;

/**
 * Exif class
 *
 * Container for EXIF data
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Exif implements ExifInterface
{
    /**
     * @param array $rawData
     */
    public function __construct(array $data)
    {
    }
}
