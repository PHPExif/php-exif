<?php
/**
 * PHP Exif Reader: Reads EXIF metadata from a file, without having to install additional PHP modules
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif;

/**
 * PHP Exif Reader
 *
 * Responsible for all the read operations on a file's EXIF metadata
 *
 * @category    PHPExif
 * @package     Exif
 * @
 */
class Exif
{
    const SECTION_FILE      = 'FILE';
    const SECTION_COMPUTED  = 'COMPUTED';
    const SECTION_IFD0      = 'IFD0';
    const SECTION_THUMBNAIL = 'THUMBNAIL';
    const SECTION_COMMENT   = 'COMMENT';
    const SECTION_EXIF      = 'EXIF';
    const SECTION_ALL       = 'ANY_TAG';
    const SECTION_IPTC      = 'IPTC';

    /**
     * The EXIF data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->setRawData($data);
    }

    /**
     * Sets the EXIF data
     *
     * @param array $data The data to set
     * @return \PHPExif\Exif Current instance for chaining
     */
    public function setRawData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns all EXIF data in the raw original format
     *
     * @return array
     */
    public function getRawData()
    {
        return $this->data;
    }

    /**
     * Returns the Aperture F-number
     *
     * @return string|boolean
     */
    public function getAperture()
    {
        if (!isset($this->data[self::SECTION_COMPUTED]['ApertureFNumber'])) {
            return false;
        }

        return $this->data[self::SECTION_COMPUTED]['ApertureFNumber'];
    }

    /**
     * Returns the Author
     *
     * @return string|boolean
     */
    public function getAuthor()
    {
        if (!isset($this->data['Artist'])) {
            return false;
        }

        return $this->data['Artist'];
    }

    /**
     * Returns the Headline
     *
     * @return string|boolean
     */
    public function getHeadline()
    {
        if (!isset($this->data[self::SECTION_IPTC]['headline'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['headline'];
    }

    /**
     * Returns the Credit
     *
     * @return string|boolean
     */
    public function getCredit()
    {
        if (!isset($this->data[self::SECTION_IPTC]['credit'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['credit'];
    }

    /**
     * Returns the source
     *
     * @return string|boolean
     */
    public function getSource()
    {
        if (!isset($this->data[self::SECTION_IPTC]['source'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['source'];
    }

    /**
     * Returns the Jobtitle
     *
     * @return string|boolean
     */
    public function getJobtitle()
    {
        if (!isset($this->data[self::SECTION_IPTC]['jobtitle'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['jobtitle'];
    }

    /**
     * Returns the ISO speed
     *
     * @return int|boolean
     */
    public function getIso()
    {
        if (!isset($this->data['ISOSpeedRatings'])) {
            return false;
        }

        return $this->data['ISOSpeedRatings'];
    }

    /**
     * Returns the Exposure
     *
     * @return string|boolean
     */
    public function getExposure()
    {
        if (!isset($this->data['ExposureTime'])) {
            return false;
        }

        return $this->data['ExposureTime'];
    }

    /**
     * Returns the Exposure
     *
     * @return float|boolean
     */
    public function getExposureMilliseconds()
    {
        if (!isset($this->data['ExposureTime'])) {
            return false;
        }

        $exposureParts  = explode('/', $this->data['ExposureTime']);

        return (int)reset($exposureParts) / (int)end($exposureParts);
    }

    /**
     * Returns the focus distance, if it exists
     *
     * @return string|boolean
     */
    public function getFocusDistance()
    {
        if (!isset($this->data[self::SECTION_COMPUTED]['FocusDistance'])) {
            return false;
        }

        return $this->data[self::SECTION_COMPUTED]['FocusDistance'];
    }

    /**
     * Returns the width in pixels, if it exists
     *
     * @return int|boolean
     */
    public function getWidth()
    {
        if (!isset($this->data[self::SECTION_COMPUTED]['Width'])) {
            return false;
        }

        return $this->data[self::SECTION_COMPUTED]['Width'];
    }

    /**
     * Returns the height in pixels, if it exists
     *
     * @return int|boolean
     */
    public function getHeight()
    {
        if (!isset($this->data[self::SECTION_COMPUTED]['Height'])) {
            return false;
        }

        return $this->data[self::SECTION_COMPUTED]['Height'];
    }

    /**
     * Returns the title, if it exists
     *
     * @return string|boolean
     */
    public function getTitle()
    {
        if (!isset($this->data[self::SECTION_IPTC]['title'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['title'];
    }

    /**
     * Returns the caption, if it exists
     *
     * @return string|boolean
     */
    public function getCaption()
    {
        if (!isset($this->data[self::SECTION_IPTC]['caption'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['caption'];
    }

    /**
     * Returns the copyright, if it exists
     *
     * @return string|boolean
     */
    public function getCopyright()
    {
        if (!isset($this->data[self::SECTION_IPTC]['copyright'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['copyright'];
    }

    /**
     * Returns the keywords, if they exists
     *
     * @return array|boolean
     */
    public function getKeywords()
    {
        if (!isset($this->data[self::SECTION_IPTC]['keywords'])) {
            return false;
        }

        return $this->data[self::SECTION_IPTC]['keywords'];
    }

    /**
     * Returns the camera, if it exists
     *
     * @return string|boolean
     */
    public function getCamera()
    {
        if (!isset($this->data['Model'])) {
            return false;
        }

        return $this->data['Model'];
    }

    /**
     * Returns the horizontal resolution in DPI, if it exists
     *
     * @return int|boolean
     */
    public function getHorizontalResolution()
    {
        if (!isset($this->data['XResolution'])) {
            return false;
        }

        $resolutionParts = explode('/', $this->data['XResolution']);
        return (int)reset($resolutionParts);
    }

    /**
     * Returns the vertical resolution in DPI, if it exists
     *
     * @return int|boolean
     */
    public function getVerticalResolution()
    {
        if (!isset($this->data['YResolution'])) {
            return false;
        }

        $resolutionParts = explode('/', $this->data['YResolution']);
        return (int)reset($resolutionParts);
    }

    /**
     * Returns the software, if it exists
     *
     * @return string|boolean
     */
    public function getSoftware()
    {
        if (!isset($this->data['Software'])) {
            return false;
        }

        return $this->data['Software'];
    }

    /**
     * Returns the focal length in mm, if it exists
     *
     * @return float|boolean
     */
    public function getFocalLength()
    {
        if (!isset($this->data['FocalLength'])) {
            return false;
        }

        $parts  = explode('/', $this->data['FocalLength']);
        return (int)reset($parts) / (int)end($parts);
    }

    /**
     * Returns the creation datetime, if it exists
     *
     * @return \DateTime|boolean
     */
    public function getCreationDate()
    {
        if (!isset($this->data['DateTimeOriginal'])) {
            return false;
        }

        $dt = \DateTime::createFromFormat('Y:m:d H:i:s', $this->data['DateTimeOriginal']);

        return $dt;
    }
}
