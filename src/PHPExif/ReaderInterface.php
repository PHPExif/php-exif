<?php
/**
 * Reader: Read EXIF data from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif;

use PHPExif\Data\MetadataInterface;
use PHPExif\Exception\UnknownAdapterTypeException;

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
    const TYPE_NATIVE   = 'native';
    const TYPE_EXIFTOOL = 'exiftool';

    /**
     * Getter for the reader adapter
     *
     * @return \PHPExif\Adapter\ReaderInterface
     */
    public function getAdapter();

    /**
     * Factory for the reader
     *
     * @param string $type
     *
     * @throws UnknownAdapterTypeException When given type is invalid
     *
     * @return ReaderInterface
     */
    public static function factory($type);

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     *
     * @return MetadataInterface
     */
    public function getMetadataFromFile($file);
}
