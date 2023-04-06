<?php
/**
 * @codeCoverageIgnore
 */

namespace PHPExif\Contracts;

use PHPExif\Exif;

/**
 * PHP Exif Reader
 *
 * Defines the interface for reader functionality
 *
 * @category    PHPExif
 * @package     Reader
 */
interface ReaderInterface
{
    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function read(string $file): Exif;
}
