<?php
/**
 * PHP Exif Mapper Interface: Defines the interface for data mappers
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Mapper
 * @codeCoverageIgnore
 */

namespace PHPExif\Adapter;

use PHPExif\Data\ExifInterface;

/**
 * MapperInterface
 *
 * Public API for mapping raw EXIF data
 * to and from Exif
 *
 * @category    PHPExif
 * @package     Mapper
 */
interface MapperInterface
{
    /**
     * Maps the array of raw source data to the correct
     * fields for the \PHPExif\Exif class
     *
     * @param array $data
     * @return ExifInterface
     */
    public function map(array $data);

    /**
     * Maps the data of given Exif object
     * to an array of raw data
     *
     * @param ExifInterface $exif
     * @return array
     */
    public function serialize(ExifInterface $exif);
}
