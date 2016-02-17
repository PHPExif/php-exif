<?php
/**
 * ReaderInterface: Defines a public interface for reading EXIF data
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Adapter;

use PHPExif\Data\MetadataInterface;

/**
 * ReaderInterface
 *
 * Public API for reading EXIF data
 *
 * @category    PHPExif
 * @package     Exif
 */
interface ReaderInterface
{
    /**
     * Read the available metadata of given file
     *
     * @param string $filePath
     *
     * @return MetadataInterface
     */
    public function getMetadataFromFile($filePath);
}
