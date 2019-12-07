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
    const ALTITUDE              = 'altitude';
    const APERTURE              = 'aperture';
    const AUTHOR                = 'author';
    const CAMERA                = 'camera';
    const CAPTION               = 'caption';
    const CITY                  = 'city';
    const COLORSPACE            = 'ColorSpace';
    const CONTENTIDENTIFIER     = 'contentIdentifier';
    const COPYRIGHT             = 'copyright';
    const COUNTRY               = 'country';
    const CREATION_DATE         = 'creationdate';
    const CREDIT                = 'credit';
    const DESCRIPTION           = 'description';
    const DURATION              = 'duration';
    const EXPOSURE              = 'exposure';
    const FILESIZE              = 'FileSize';
    const FILENAME              = 'FileName';
    const FOCAL_LENGTH          = 'focalLength';
    const FOCAL_DISTANCE        = 'focalDistance';
    const FRAMERATE             = 'framerate';
    const GPS                   = 'gps';
    const HEADLINE              = 'headline';
    const HEIGHT                = 'height';
    const HORIZONTAL_RESOLUTION = 'horizontalResolution';
    const IMGDIRECTION          = 'imgDirection';
    const ISO                   = 'iso';
    const JOB_TITLE             = 'jobTitle';
    const KEYWORDS              = 'keywords';
    const LATITUDE              = 'latitude';
    const LONGITUDE             = 'longitude';
    const LENS                  = 'lens';
    const MAKE                  = 'make';
    const MICROVIDEOOFFSET      = 'MicroVideoOffset';
    const MIMETYPE              = 'MimeType';
    const ORIENTATION           = 'Orientation';
    const SOFTWARE              = 'software';
    const SOURCE                = 'source';
    const STATE                 = 'state';
    const SUBLOCATION           = 'Sublocation';
    const TITLE                 = 'title';
    const VERTICAL_RESOLUTION   = 'verticalResolution';
    const WIDTH                 = 'width';



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
     * Sets the Aperture F-number
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setAperture($value)
    {
        $this->data[self::APERTURE] = $value;

        return $this;
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
     * Sets the Author
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setAuthor($value)
    {
        $this->data[self::AUTHOR] = $value;

        return $this;
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
     * Sets the Headline
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setHeadline($value)
    {
        $this->data[self::HEADLINE] = $value;

        return $this;
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
     * Sets the Credit
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setCredit($value)
    {
        $this->data[self::CREDIT] = $value;

        return $this;
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
     * Sets the Source
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setSource($value)
    {
        $this->data[self::SOURCE] = $value;

        return $this;
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
     * Sets the Jobtitle
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setJobtitle($value)
    {
        $this->data[self::JOB_TITLE] = $value;

        return $this;
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
     * Sets the ISO
     *
     * @param int $value
     * @return \PHPExif\Exif
     */
    public function setIso($value)
    {
        $this->data[self::ISO] = $value;

        return $this;
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
     * Sets the Exposure
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setExposure($value)
    {
        $this->data[self::EXPOSURE] = $value;

        return $this;
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

        if (is_numeric($this->data[self::EXPOSURE])) {
            return $this->data[self::EXPOSURE] + 0;
        }

        $exposureParts = explode('/', $this->data[self::EXPOSURE]);

        return (int) reset($exposureParts) / (int) end($exposureParts);
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
     * Sets the focus distance
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setFocusDistance($value)
    {
        $this->data[self::FOCAL_DISTANCE] = $value;

        return $this;
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
     * Sets the width
     *
     * @param int $value
     * @return \PHPExif\Exif
     */
    public function setWidth($value)
    {
        $this->data[self::WIDTH] = $value;

        return $this;
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
     * Sets the height
     *
     * @param int $value
     * @return \PHPExif\Exif
     */
    public function setHeight($value)
    {
        $this->data[self::HEIGHT] = $value;

        return $this;
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
     * Sets the title
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setTitle($value)
    {
        $this->data[self::TITLE] = $value;

        return $this;
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
     * Sets the caption
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setCaption($value)
    {
        $this->data[self::CAPTION] = $value;

        return $this;
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
     * Sets the copyright
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setCopyright($value)
    {
        $this->data[self::COPYRIGHT] = $value;

        return $this;
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
     * Sets the keywords
     *
     * @param array $value
     * @return \PHPExif\Exif
     */
    public function setKeywords($value)
    {
        $this->data[self::KEYWORDS] = $value;

        return $this;
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
     * Sets the camera
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setCamera($value)
    {
        $this->data[self::CAMERA] = $value;

        return $this;
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
     * Sets the horizontal resolution in DPI
     *
     * @param int $value
     * @return \PHPExif\Exif
     */
    public function setHorizontalResolution($value)
    {
        $this->data[self::HORIZONTAL_RESOLUTION] = $value;

        return $this;
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
     * Sets the vertical resolution in DPI
     *
     * @param int $value
     * @return \PHPExif\Exif
     */
    public function setVerticalResolution($value)
    {
        $this->data[self::VERTICAL_RESOLUTION] = $value;

        return $this;
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
     * Sets the software
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setSoftware($value)
    {
        $this->data[self::SOFTWARE] = trim($value);

        return $this;
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
     * Sets the focal length in mm
     *
     * @param float $value
     * @return \PHPExif\Exif
     */
    public function setFocalLength($value)
    {
        $this->data[self::FOCAL_LENGTH] = $value;

        return $this;
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
     * Sets the creation datetime
     *
     * @param \DateTime $value
     * @return \PHPExif\Exif
     */
    public function setCreationDate(\DateTime $value)
    {
        $this->data[self::CREATION_DATE] = $value;

        return $this;
    }

    /**
     * Returns the colorspace, if it exists
     *
     * @return string|boolean
     */
    public function getColorSpace()
    {
        if (!isset($this->data[self::COLORSPACE])) {
            return false;
        }

        return $this->data[self::COLORSPACE];
    }

    /**
     * Sets the colorspace
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setColorSpace($value)
    {
        $this->data[self::COLORSPACE] = $value;

        return $this;
    }

    /**
     * Returns the mimetype, if it exists
     *
     * @return string|boolean
     */
    public function getMimeType()
    {
        if (!isset($this->data[self::MIMETYPE])) {
            return false;
        }

        return $this->data[self::MIMETYPE];
    }

    /**
     * Sets the mimetype
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setMimeType($value)
    {
        $this->data[self::MIMETYPE] = $value;

        return $this;
    }

    /**
     * Returns the filesize, if it exists
     *
     * @return int|boolean
     */
    public function getFileSize()
    {
        if (!isset($this->data[self::FILESIZE])) {
            return false;
        }

        return $this->data[self::FILESIZE];
    }

    /**
     * Sets the filesize
     *
     * @param int $value
     * @return \PHPExif\Exif
     */
    public function setFileSize($value)
    {
        $this->data[self::FILESIZE] = $value;

        return $this;
    }

    /**
     * Returns the filename, if it exists
     *
     * @return string|boolean
     */
    public function getFileName()
    {
        if (!isset($this->data[self::FILENAME])) {
            return false;
        }

        return $this->data[self::FILENAME];
    }

    /**
     * Sets the filename
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setFileName($value)
    {
        $this->data[self::FILENAME] = $value;

        return $this;
    }

    /**
     * Returns the orientation, if it exists
     *
     * @return int|boolean
     */
    public function getOrientation()
    {
        if (!isset($this->data[self::ORIENTATION])) {
            return false;
        }

        return $this->data[self::ORIENTATION];
    }

    /**
     * Sets the orientation
     *
     * @param int $value
     * @return \PHPExif\Exif
     */
    public function setOrientation($value)
    {
        $this->data[self::ORIENTATION] = $value;

        return $this;
    }

    /**
     * Returns GPS coordinates, if it exists
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
     * Sets the GPS coordinates
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setGPS($value)
    {
        $this->data[self::GPS] = $value;

        return $this;
    }

    /**
     * Sets the description value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setDescription($value)
    {
        $this->data[self::DESCRIPTION] = $value;

        return $this;
    }

    /**
     * Returns description, if it exists
     *
     * @return string|boolean
     */
    public function getDescription()
    {
        if (!isset($this->data[self::DESCRIPTION])) {
            return false;
        }

        return $this->data[self::DESCRIPTION];
    }


    /**
     * Sets the Make value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setMake($value)
    {
        $this->data[self::MAKE] = $value;

        return $this;
    }

    /**
     * Returns make, if it exists
     *
     * @return string|boolean
     */
    public function getMake()
    {
        if (!isset($this->data[self::MAKE])) {
            return false;
        }

        return $this->data[self::MAKE];
    }

    /**
     * Sets the altitude value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setAltitude($value)
    {
        $this->data[self::ALTITUDE] = $value;

        return $this;
    }

    /**
     * Returns altitude, if it exists
     *
     * @return float|boolean
     */
    public function getAltitude()
    {
        if (!isset($this->data[self::ALTITUDE])) {
            return false;
        }

        return $this->data[self::ALTITUDE];
    }

    /**
     * Sets the altitude value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setLongitude($value)
    {
        $this->data[self::LONGITUDE] = $value;

        return $this;
    }

    /**
     * Returns altitude, if it exists
     *
     * @return float|boolean
     */
    public function getLongitude()
    {
        if (!isset($this->data[self::LONGITUDE])) {
            return false;
        }

        return $this->data[self::LONGITUDE];
    }

    /**
     * Sets the latitude value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setLatitude($value)
    {
        $this->data[self::LATITUDE] = $value;

        return $this;
    }

    /**
     * Returns latitude, if it exists
     *
     * @return float|boolean
     */
    public function getLatitude()
    {
        if (!isset($this->data[self::LATITUDE])) {
            return false;
        }

        return $this->data[self::LATITUDE];
    }

    /**
     * Sets the imgDirection value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setImgDirection($value)
    {
        $this->data[self::IMGDIRECTION] = $value;

        return $this;
    }

    /**
     * Returns imgDirection, if it exists
     *
     * @return float|boolean
     */
    public function getImgDirection()
    {
        if (!isset($this->data[self::IMGDIRECTION])) {
            return false;
        }

        return $this->data[self::IMGDIRECTION];
    }


    /**
     * Sets the Make value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setLens($value)
    {
        $this->data[self::LENS] = $value;

        return $this;
    }

    /**
     * Returns make, if it exists
     *
     * @return string|boolean
     */
    public function getLens()
    {
        if (!isset($this->data[self::LENS])) {
            return false;
        }

        return $this->data[self::LENS];
    }

    /**
     * Sets the content identifier value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setContentIdentifier($value)
    {
        $this->data[self::CONTENTIDENTIFIER] = $value;

        return $this;
    }

    /**
     * Returns content identifier, if it exists
     *
     * @return string|boolean
     */
    public function getContentIdentifier()
    {
        if (!isset($this->data[self::CONTENTIDENTIFIER])) {
            return false;
        }

        return $this->data[self::CONTENTIDENTIFIER];
    }


    /**
     * Sets the framerate value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setFramerate($value)
    {
        $this->data[self::FRAMERATE] = $value;

        return $this;
    }

    /**
     * Returns content identifier, if it exists
     *
     * @return string|boolean
     */
    public function getFramerate()
    {
        if (!isset($this->data[self::FRAMERATE])) {
            return false;
        }

        return $this->data[self::FRAMERATE];
    }


    /**
     * Sets the duration value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setDuration($value)
    {
        $this->data[self::DURATION] = $value;

        return $this;
    }

    /**
     * Returns duration, if it exists
     *
     * @return string|boolean
     */
    public function getDuration()
    {
        if (!isset($this->data[self::DURATION])) {
            return false;
        }
        return $this->data[self::DURATION];
    }

    /**
     * Sets the duration value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setMicroVideoOffset($value)
    {
        $this->data[self::MICROVIDEOOFFSET] = $value;

        return $this;
    }

    /**
     * Returns duration, if it exists
     *
     * @return string|boolean
     */
    public function getMicroVideoOffset()
    {
        if (!isset($this->data[self::MICROVIDEOOFFSET])) {
            return false;
        }

        return $this->data[self::MICROVIDEOOFFSET];
    }

    /**
     * Sets the sublocation value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setSublocation($value)
    {
        $this->data[self::SUBLOCATION] = $value;

        return $this;
    }

    /**
     * Returns sublocation, if it exists
     *
     * @return string|boolean
     */
    public function getSublocation()
    {
        if (!isset($this->data[self::SUBLOCATION])) {
            return false;
        }

        return $this->data[self::SUBLOCATION];
    }

    /**
     * Sets the city value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setCity($value)
    {
        $this->data[self::CITY] = $value;

        return $this;
    }

    /**
     * Returns city, if it exists
     *
     * @return string|boolean
     */
    public function getCity()
    {
        if (!isset($this->data[self::CITY])) {
            return false;
        }

        return $this->data[self::CITY];
    }

    /**
     * Sets the state value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setState($value)
    {
        $this->data[self::STATE] = $value;

        return $this;
    }

    /**
     * Returns state, if it exists
     *
     * @return string|boolean
     */
    public function getState()
    {
        if (!isset($this->data[self::STATE])) {
            return false;
        }

        return $this->data[self::STATE];
    }

    /**
     * Sets the country value
     *
     * @param string $value
     * @return \PHPExif\Exif
     */
    public function setCountry($value)
    {
        $this->data[self::COUNTRY] = $value;

        return $this;
    }

    /**
     * Returns country, if it exists
     *
     * @return string|boolean
     */
    public function getCountry()
    {
        if (!isset($this->data[self::COUNTRY])) {
            return false;
        }

        return $this->data[self::COUNTRY];
    }
}
