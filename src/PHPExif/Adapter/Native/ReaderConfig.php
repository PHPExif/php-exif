<?php
/**
 * ReaderConfig: A class which uses the native PHP functionality
 * to read EXIF data from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 */

namespace PHPExif\Adapter\Native;

/**
 * ReaderConfig class
 *
 * @category    PHPExif
 * @package     Adapter
 */
final class ReaderConfig
{
    const CONFIG_PARSE_RAW_IPTC_DATA = 'parseRawIptcData';
    const CONFIG_SECTIONS = 'sections';

    /**
     * Parse raw IPTC data and augment the metadata with it
     *
     * @var bool
     */
    private $parseRawIptcData;

    /**
     * List of sections to retrieve
     *
     * @var string
     */
    private $sections;

    /**
     * @param bool $parseRawIptcData
     * @param string $sections
     */
    public function __construct($parseRawIptcData, $sections)
    {
        $this->parseRawIptcData = (bool) $parseRawIptcData;
        $this->sections = $sections;
    }

    /**
     * Returns if the raw IPTC data should be retrieved
     *
     * @return bool
     */
    public function isParseRawIptcData()
    {
        return $this->parseRawIptcData;
    }

    /**
     * Returns the list of wanted sections
     *
     * @return string
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Creates new instance from given array of config values
     *
     * @param array $config
     *
     * @return ReaderConfig
     */
    public static function fromArray(array $config)
    {
        return new self(
            $config[self::CONFIG_PARSE_RAW_IPTC_DATA],
            $config[self::CONFIG_SECTIONS]
        );
    }
}
