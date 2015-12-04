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

    const SECTION_FILE      = 'FILE';
    const SECTION_COMPUTED  = 'COMPUTED';
    const SECTION_IFD0      = 'IFD0';
    const SECTION_THUMBNAIL = 'THUMBNAIL';
    const SECTION_COMMENT   = 'COMMENT';
    const SECTION_EXIF      = 'EXIF';
    const SECTION_ALL       = 'ANY_TAG';
    const SECTION_IPTC      = 'IPTC';

    /**
     * List of EXIF sections
     *
     * @var array
     */
    private $requiredSections = array();

    /**
     * Include the thumbnail in the EXIF data?
     *
     * @var boolean
     */
    public $includeThumbnail = self::NO_THUMBNAIL;

    /**
     * Parse the sections as arrays?
     *
     * @var boolean
     */
    public $sectionsAsArrays = self::SECTIONS_FLAT;

    /**
     * Classname of the Mapper to use when mapping raw data
     * to an Exif object
     *
     * @var string
     */
    public $mapperClass = '\\PHPExif\\Adapter\\Native\\Mapper';

    /**
     * Getter for the EXIF sections
     *
     * @return array
     */
    public function getRequiredSections()
    {
        return $this->requiredSections;
    }

    /**
     * Returns the required EXIF sections as
     * a comma-separated string
     *
     * @return string
     */
    public function getRequiredSectionsAsString()
    {
        if (empty($this->requiredSections)) {
            return null;
        }

        return implode(',', $this->requiredSections);
    }

    /**
     * Setter for the EXIF sections
     *
     * @param array $sections List of EXIF sections
     * @return Configuration
     */
    public function setRequiredSections(array $sections)
    {
        $this->requiredSections = $sections;

        return $this;
    }

    /**
     * Adds an EXIF section to the list
     *
     * @param string $section
     * @return Configuration
     */
    public function addRequiredSection($section)
    {
        if (!in_array($section, $this->requiredSections)) {
            array_push($this->requiredSections, $section);
        }

        return $this;
    }
}
