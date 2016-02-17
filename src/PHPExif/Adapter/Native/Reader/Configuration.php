<?php
/**
 * Configuration: A class which uses the native PHP functionality
 * to read EXIF data from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Adapter\Native\Reader;

/**
 * Configuration class
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Configuration
{
    const INCLUDE_THUMBNAIL = true;
    const NO_THUMBNAIL      = false;

    const SECTIONS_AS_ARRAYS = true;
    const SECTIONS_FLAT      = false;

    const SECTIONS = 'ANY_TAG';

    const IPTC_EXIF_ONLY = false;
    const IPTC_ADD_RAW = true;

    /**
     * Classname of the Mapper to use when mapping raw data
     * to an Exif object
     *
     * @var string
     */
    public $mapperClass = '\\PHPExif\\Adapter\\Native\\Mapper';

    /**
     * Parse IPTC data from binary data and add it to the
     * data read from Exif?
     *
     * @var bool
     */
    public $parseRawIptcData = self::IPTC_ADD_RAW;
}
