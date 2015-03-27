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
    const APERTURE              = 'aperture';
    const AUTHOR                = 'author';
    const CAMERA                = 'camera';
    const CAPTION               = 'caption';
    const COLORSPACE            = 'ColorSpace';
    const COPYRIGHT             = 'copyright';
    const CREATION_DATE         = 'creationdate';
    const CREDIT                = 'credit';
    const EXPOSURE              = 'exposure';
    const FILESIZE              = 'FileSize';
    const FOCAL_LENGTH          = 'focalLength';
    const FOCAL_DISTANCE        = 'focalDistance';
    const HEADLINE              = 'headline';
    const HEIGHT                = 'height';
    const HORIZONTAL_RESOLUTION = 'horizontalResolution';
    const ISO                   = 'iso';
    const JOB_TITLE             = 'jobTitle';
    const KEYWORDS              = 'keywords';
    const MIMETYPE              = 'MimeType';
    const ORIENTATION           = 'Orientation';
    const SOFTWARE              = 'software';
    const SOURCE                = 'source';
    const TITLE                 = 'title';
    const VERTICAL_RESOLUTION   = 'verticalResolution';
    const WIDTH                 = 'width';
    const GPS                   = 'gps';

    /**
     * The mapped EXIF data
     *
     * @var array
     */
    protected $data = array();

    /**
     * The raw EXIF data
     *
     * @var array
     */
    protected $rawData = array();

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->setData($data);
    }

    /**
     * Sets the raw EXIF data
     *
     * @param array $data The data to set
     * @return \PHPExif\Exif Current instance for chaining
     */
    public function setRawData(array $data)
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * Returns all EXIF data in the raw original format
     *
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Sets the mapped EXIF data
     *
     * @param array $data The data to set
     * @return \PHPExif\Exif Current instance for chaining
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns the mapped EXIF data
     *
     * @return array
     */
    public function getData()
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
        if (!isset($this->data[self::APERTURE])) {
            return false;
        }

        return $this->data[self::APERTURE];
    }

    /**
     * Returns the Author
     *
     * @return string|boolean
     */
    public function getAuthor()
    {
        if (!isset($this->data[self::AUTHOR])) {
            return false;
        }

        return $this->data[self::AUTHOR];
    }

    /**
     * Returns the Headline
     *
     * @return string|boolean
     */
    public function getHeadline()
    {
        if (!isset($this->data[self::HEADLINE])) {
            return false;
        }

        return $this->data[self::HEADLINE];
    }

    /**
     * Returns the Credit
     *
     * @return string|boolean
     */
    public function getCredit()
    {
        if (!isset($this->data[self::CREDIT])) {
            return false;
        }

        return $this->data[self::CREDIT];
    }

    /**
     * Returns the source
     *
     * @return string|boolean
     */
    public function getSource()
    {
        if (!isset($this->data[self::SOURCE])) {
            return false;
        }

        return $this->data[self::SOURCE];
    }

    /**
     * Returns the Jobtitle
     *
     * @return string|boolean
     */
    public function getJobtitle()
    {
        if (!isset($this->data[self::JOB_TITLE])) {
            return false;
        }

        return $this->data[self::JOB_TITLE];
    }

    /**
     * Returns the ISO speed
     *
     * @return int|boolean
     */
    public function getIso()
    {
        if (!isset($this->data[self::ISO])) {
            return false;
        }

        return $this->data[self::ISO];
    }

    /**
     * Returns the Exposure
     *
     * @return string|boolean
     */
    public function getExposure()
    {
        if (!isset($this->data[self::EXPOSURE])) {
            return false;
        }

        return $this->data[self::EXPOSURE];
    }

    /**
     * Returns the Exposure
     *
     * @return float|boolean
     */
    public function getExposureMilliseconds()
    {
        if (!isset($this->data[self::EXPOSURE])) {
            return false;
        }

        $exposureParts  = explode('/', $this->data[self::EXPOSURE]);

        return (int)reset($exposureParts) / (int)end($exposureParts);
    }

    /**
     * Returns the focus distance, if it exists
     *
     * @return string|boolean
     */
    public function getFocusDistance()
    {
        if (!isset($this->data[self::FOCAL_DISTANCE])) {
            return false;
        }

        return $this->data[self::FOCAL_DISTANCE];
    }

    /**
     * Returns the width in pixels, if it exists
     *
     * @return int|boolean
     */
    public function getWidth()
    {
        if (!isset($this->data[self::WIDTH])) {
            return false;
        }

        return $this->data[self::WIDTH];
    }

    /**
     * Returns the height in pixels, if it exists
     *
     * @return int|boolean
     */
    public function getHeight()
    {
        if (!isset($this->data[self::HEIGHT])) {
            return false;
        }

        return $this->data[self::HEIGHT];
    }

    /**
     * Returns the title, if it exists
     *
     * @return string|boolean
     */
    public function getTitle()
    {
        if (!isset($this->data[self::TITLE])) {
            return false;
        }

        return $this->data[self::TITLE];
    }

    /**
     * Returns the caption, if it exists
     *
     * @return string|boolean
     */
    public function getCaption()
    {
        if (!isset($this->data[self::CAPTION])) {
            return false;
        }

        return $this->data[self::CAPTION];
    }

    /**
     * Returns the copyright, if it exists
     *
     * @return string|boolean
     */
    public function getCopyright()
    {
        if (!isset($this->data[self::COPYRIGHT])) {
            return false;
        }

        return $this->data[self::COPYRIGHT];
    }

    /**
     * Returns the keywords, if they exists
     *
     * @return array|boolean
     */
    public function getKeywords()
    {
        if (!isset($this->data[self::KEYWORDS])) {
            return false;
        }

        return $this->data[self::KEYWORDS];
    }

    /**
     * Returns the camera, if it exists
     *
     * @return string|boolean
     */
    public function getCamera()
    {
        if (!isset($this->data[self::CAMERA])) {
            return false;
        }

        return $this->data[self::CAMERA];
    }

    /**
     * Returns the horizontal resolution in DPI, if it exists
     *
     * @return int|boolean
     */
    public function getHorizontalResolution()
    {
        if (!isset($this->data[self::HORIZONTAL_RESOLUTION])) {
            return false;
        }

        return $this->data[self::HORIZONTAL_RESOLUTION];
    }

    /**
     * Returns the vertical resolution in DPI, if it exists
     *
     * @return int|boolean
     */
    public function getVerticalResolution()
    {
        if (!isset($this->data[self::VERTICAL_RESOLUTION])) {
            return false;
        }

        return $this->data[self::VERTICAL_RESOLUTION];
    }

    /**
     * Returns the software, if it exists
     *
     * @return string|boolean
     */
    public function getSoftware()
    {
        if (!isset($this->data[self::SOFTWARE])) {
            return false;
        }

        return $this->data[self::SOFTWARE];
    }

    /**
     * Returns the focal length in mm, if it exists
     *
     * @return float|boolean
     */
    public function getFocalLength()
    {
        if (!isset($this->data[self::FOCAL_LENGTH])) {
            return false;
        }

        return $this->data[self::FOCAL_LENGTH];
    }

    /**
     * Returns the creation datetime, if it exists
     *
     * @return \DateTime|boolean
     */
    public function getCreationDate()
    {
        if (!isset($this->data[self::CREATION_DATE])) {
            return false;
        }

        return $this->data[self::CREATION_DATE];
    }

    /**
     * Returns the colorspace, if it exists
     *
     * @return string
     */
    public function getColorSpace()
    {
        if (!isset($this->data[self::COLORSPACE])) {
            return false;
        }

        return $this->data[self::COLORSPACE];
    }

    /**
     * Returns the mimetype, if it exists
     *
     * @return string
     */
    public function getMimeType()
    {
        if (!isset($this->data[self::MIMETYPE])) {
            return false;
        }

        return $this->data[self::MIMETYPE];
    }

    /**
     * Returns the filesize, if it exists
     *
     * @return integer
     */
    public function getFileSize()
    {
        if (!isset($this->data[self::FILESIZE])) {
            return false;
        }

        return $this->data[self::FILESIZE];
    }

    /**
     * Returns the orientation, if it exists
     *
     * @return integer
     */
    public function getOrientation()
    {
        if (!isset($this->data[self::ORIENTATION])) {
            return false;
        }

        return $this->data[self::ORIENTATION];
    }

    /**
     * Returns raw GPS coordinates, if it exists
     *
     * @return array|boolean
     */
    public function getGPS()
    {
        if (!isset($this->data[self::GPS])) {
            return false;
        }

        return $this->data[self::GPS];
    }

    /**
     * Returns GPS in degrees, minutes and seconds, if it exists
     *
     * @return string|boolean
     */
    public function getGPSDegMinSec()
    {
        return $this->getFormattedGPS('degrees_minutes_seconds');
    }

    /**
     * Returns GPS in degrees, and decimal minutes, if it exists
     *
     * @return string|boolean
     */
    public function getGPSDecMinutes()
    {
        return $this->getFormattedGPS('decimal_minutes');
    }

    /**
     * Returns GPS in decimal degrees, if it exists
     *
     * @return string|boolean
     */
    public function getGPSDecDegrees()
    {
        return $this->getFormattedGPS('decimal_degrees');
    }

    /**
     * Returns formatted GPS coordinates, if it exists
     *
     * @param string $format
     * @return string|boolean
     */
    public function getFormattedGPS($format = 'decimal_minutes')
    {
        if (!isset($this->data[self::GPS]) || $this->data[self::GPS] === false) {
            return false;
        }

        if ($format === 'degrees_minutes_seconds') {
            $gps = $this->data[self::GPS];

            return sprintf(
                '%d° %d\' %s" %s, %d° %d\' %s" %s',
                $gps['latitude'][0],
                $gps['latitude'][1],
                $gps['latitude'][2],
                $gps['latitude'][3],
                $gps['longitude'][0],
                $gps['longitude'][1],
                $gps['longitude'][2],
                $gps['longitude'][3]
            );
        }

        return $this->getGPSDecimal($format);
    }

    /**
     * Returns decimal formatted GPS coordinates, if it exists
     *
     * @param string $format
     * @return string
     * @throws \InvalidArgumentException If the the format is not valid
     */
    protected function getGPSDecimal($format)
    {
        $gps = $this->data[self::GPS];

        $latMinutes = $gps['latitude'][1] / 60 + $gps['latitude'][2] / 3600;
        $lonMinutes = $gps['longitude'][1] / 60 + $gps['longitude'][2] / 3600;

        switch ($format) {
            case 'decimal_minutes':
                return sprintf(
                    '%d° %f\' %s, %d° %f\' %s',
                    $gps['latitude'][0],
                    $latMinutes,
                    $gps['latitude'][3],
                    $gps['longitude'][0],
                    $lonMinutes,
                    $gps['longitude'][3]
                );
                break;

            case 'decimal_degrees':
                return sprintf(
                    '%f, %f',
                    ($gps['latitude'][3] === 'S' ? -1 : 1) * ($gps['latitude'][0] + $latMinutes),
                    ($gps['longitude'][3] === 'W' ? -1 : 1) * ($gps['longitude'][0] + $lonMinutes)
                );
                break;

            default:
                throw new \InvalidArgumentException(sprintf('GPS format "%s" is not valid', $format));
                break;
        }
    }
}
