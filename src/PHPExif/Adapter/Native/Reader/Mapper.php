<?php
/**
 * Mapper: A class which maps data from the native
 * PHP exif functionality to and from
 * something this libary understands
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Adapter\Native\Mapper;

use PHPExif\Adapter\MapperInterface;
use PHPExif\Data\Exif;
use PHPExif\Data\ExifInterface;
use PHPExif\Exception\InterruptException;

/**
 * Mapper class
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Mapper implements MapperInterface
{
    const APERTUREFNUMBER  = 'ApertureFNumber';
    const ARTIST           = 'Artist';
    const CAPTION          = 'caption';
    const COLORSPACE       = 'ColorSpace';
    const COPYRIGHT        = 'copyright';
    const CREDIT           = 'credit';
    const DATETIMEORIGINAL = 'DateTimeOriginal';
    const EXPOSURETIME     = 'ExposureTime';
    const FILESIZE         = 'FileSize';
    const FOCALLENGTH      = 'FocalLength';
    const FOCUSDISTANCE    = 'FocusDistance';
    const GPSLATITUDE      = 'GPSLatitude';
    const GPSLONGITUDE     = 'GPSLongitude';
    const HEADLINE         = 'headline';
    const HEIGHT           = 'Height';
    const ISOSPEEDRATINGS  = 'ISOSpeedRatings';
    const JOBTITLE         = 'jobtitle';
    const KEYWORDS         = 'keywords';
    const MIMETYPE         = 'MimeType';
    const MODEL            = 'Model';
    const ORIENTATION      = 'Orientation';
    const SOFTWARE         = 'Software';
    const SOURCE           = 'source';
    const TITLE            = 'title';
    const WIDTH            = 'Width';
    const XRESOLUTION      = 'XResolution';
    const YRESOLUTION      = 'YResolution';

    const SECTION_ALL       = 'ANY_TAG';
    const SECTION_COMMENT   = 'COMMENT';
    const SECTION_COMPUTED  = 'COMPUTED';
    const SECTION_EXIF      = 'EXIF';
    const SECTION_FILE      = 'FILE';
    const SECTION_IFD0      = 'IFD0';
    const SECTION_IPTC      = 'IPTC';
    const SECTION_THUMBNAIL = 'THUMBNAIL';

    /**
     * A list of section names
     *
     * @var array
     */
    private $sections = array(
        self::SECTION_ALL,
        self::SECTION_COMMENT,
        self::SECTION_COMPUTED,
        self::SECTION_EXIF,
        self::SECTION_FILE,
        self::SECTION_IFD0,
        self::SECTION_IPTC,
        self::SECTION_THUMBNAIL,
    );

    /**
     * Maps the native fields to the fields of
     * the \PHPExif\Data\Exif class
     *
     * @var array
     */
    private $map = array(
        self::APERTUREFNUMBER  => ExifInterface::APERTURE,
        self::ARTIST           => ExifInterface::AUTHOR,
        self::CAPTION          => ExifInterface::CAPTION,
        self::COLORSPACE       => ExifInterface::COLORSPACE,
        self::COPYRIGHT        => ExifInterface::COPYRIGHT,
        self::CREDIT           => ExifInterface::CREDIT,
        self::DATETIMEORIGINAL => ExifInterface::CREATION_DATE,
        self::EXPOSURETIME     => ExifInterface::EXPOSURE,
        self::FILESIZE         => ExifInterface::FILESIZE,
        self::FOCALLENGTH      => ExifInterface::FOCAL_LENGTH,
        self::FOCUSDISTANCE    => ExifInterface::FOCAL_DISTANCE,
        self::GPSLATITUDE      => ExifInterface::GPS,
        self::GPSLONGITUDE     => ExifInterface::GPS,
        self::HEADLINE         => ExifInterface::HEADLINE,
        self::HEIGHT           => ExifInterface::HEIGHT,
        self::ISOSPEEDRATINGS  => ExifInterface::ISO,
        self::JOBTITLE         => ExifInterface::JOB_TITLE,
        self::KEYWORDS         => ExifInterface::KEYWORDS,
        self::MIMETYPE         => ExifInterface::MIMETYPE,
        self::MODEL            => ExifInterface::CAMERA,
        self::ORIENTATION      => ExifInterface::ORIENTATION,
        self::SOFTWARE         => ExifInterface::SOFTWARE,
        self::SOURCE           => ExifInterface::SOURCE,
        self::TITLE            => ExifInterface::TITLE,
        self::WIDTH            => ExifInterface::WIDTH,
        self::XRESOLUTION      => ExifInterface::HORIZONTAL_RESOLUTION,
        self::YRESOLUTION      => ExifInterface::VERTICAL_RESOLUTION,
    );

    /**
     * Maps a Native field to a method to manipulate the data
     * for the \PHPExif\Data\Exif class
     *
     * @var array
     */
    private $manipulators = array(
        self::DATETIMEORIGINAL => 'convertDateTimeOriginal',
        self::EXPOSURETIME     => 'convertExposureTime',
        self::FOCALLENGTH      => 'convertFocalLength',
        self::GPSLATITUDE      => 'extractGPSCoordinate',
        self::GPSLONGITUDE     => 'extractGPSCoordinate',
        self::XRESOLUTION      => 'convertResolution',
        self::YRESOLUTION      => 'convertResolution',
    );

    /**
     * {@inheritDoc}
     */
    public function map(array $data)
    {
        $mappedData = array();
        foreach ($data as $field => $value) {
            try {
                $this->handleSection($field, $value, $mappedData);
                $this->skipUnknownField($field);
                $this->doCustomManipulation($field, $value)
            } catch (InterruptException $e) {
                continue;
            }

            // set end result
            $key = $this->map[$field];
            $mappedData[$key] = $value;
        }

        $mappedData = $this->mapGPSData($data, $mappedData);

        $exif = new Exif($mappedData);

        return $exif;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(ExifInterface $exif)
    {
    }

    /**
     * Maps section data and merges it into the list of mapped data
     *
     * @param string $field
     * @param string $value
     * @param array $mappedData
     * @throws InterruptException
     */
    private function handleSection($field, $value, array &$mappedData)
    {
        if (!($this->isSection($field) && is_array($value))) {
            return;
        }

        $subData = $this->map($value);
        $mappedData = array_merge(
            $mappedData,
            $subData
        );

        throw new InterruptException();
    }

    /**
     * Determines if given field is known for mapping
     *
     * @param string $field
     * @throws InterruptException
     */
    private function skipUnknownField($field)
    {
        if (array_key_exists($field, $this->map)) {
            return;
        }

        throw new InterruptException();
    }

    /**
     * Executes the custom manipulators if necessary
     *
     * @param string $field
     * @param mixed $value
     * @throws InterruptException
     */
    private function doCustomManipulation($field, &$value)
    {
        if (!array_key_exists($field, $this->manipulators)) {
            return;
        }

        $method = $this->manipulators[$field];
        $value = $this->$method($value);

        if (null === $value) {
            throw new InterruptException();
        }
    }

    /**
     * Maps GPS data to the correct key, if such data exists
     *
     * @param array $data
     * @param array $mappedData
     * @return array
     */
    private function mapGPSData(array $data, array $mappedData)
    {
        if (!array_key_exists(self::GPSLATITUDE, $mappedData)) {
            return $mappedData;
        }
        $gpsLocation = sprintf(
            '%s,%s',
            (strtoupper($data['GPSLatitudeRef'][0]) === 'S' ? -1 : 1) * $mappedData[self::GPSLATITUDE],
            (strtoupper($data['GPSLongitudeRef'][0]) === 'W' ? -1 : 1) * $mappedData[self::GPSLONGITUDE]
        );
        unset($mappedData[self::GPSLATITUDE]);
        unset($mappedData[self::GPSLONGITUDE]);
        $mappedData[Exif::GPS] = $gpsLocation;
        return $mappedData;
    }

    /**
     * Determines if given field is a section
     *
     * @param string $field
     * @return bool
     */
    private function isSection($field)
    {
        return (in_array($field, $this->sections));
    }

    /**
     * Converts incoming Native date to a DateTime object
     *
     * @param string $originalValue
     * @return \DateTime
     */
    private function convertDateTimeOriginal($originalValue)
    {
        try {
            $originalValue = new DateTime($originalValue);
        } catch (Exception $exception) {
            return;
        }

        return $originalValue;
    }

    /**
     * Converts incoming exposure time to a sensible format
     *
     * @param string $originalValue
     * @return string
     */
    private function convertExposureTime($originalValue)
    {
        if (!is_float($originalValue)) {
            $originalValue = $this->normalizeComponent($value);
        }

        // Based on the source code of Exiftool (PrintExposureTime subroutine):
        // http://cpansearch.perl.org/src/EXIFTOOL/Image-ExifTool-9.90/lib/Image/ExifTool/Exif.pm
        if ($originalValue < 0.25001 && $originalValue > 0) {
            return sprintf(
                '1/%d',
                intval(0.5 + 1 / $originalValue)
            );
        }

        $originalValue = sprintf('%.1f', $originalValue);
        return preg_replace('/.0$/', '', $originalValue);
    }

    /**
     * Converts focal length to a float value
     *
     * @param string $originalValue
     * @return float
     */
    private function convertFocalLength($originalValue)
    {
        $parts  = explode('/', $originalValue);
        return ((int) reset($parts) / (int) end($parts));
    }

    /**
     * Converts incoming resolution value to a sensible value
     *
     * @param string $originalValue
     * @return int
     */
    private function convertResolution($originalValue)
    {
        $resolutionParts = explode('/', $originalValue);
        return ((int) reset($resolutionParts));
    }

    /**
     * Extract GPS coordinates from components array
     *
     * @param array $components
     * @return float
     */
    private function extractGPSCoordinate(array $components)
    {
        $components = array_map(
            array($this, 'normalizeGPSComponent'),
            $components
        );

        if (count($components) > 2) {
            return intval($components[0]) + (intval($components[1]) / 60) + (floatval($components[2]) / 3600);
        }

        return reset($components);
    }

    /**
     * Normalize GPS coordinates components
     *
     * @param mixed $component
     * @return int|float
     */
    private function normalizeGPSComponent($component)
    {
        $parts  = explode('/', $component);
        return count($parts) === 1 ? $parts[0] : (int) reset($parts) / (int) end($parts);
    }

    /**
     * Normalize component
     *
     * @param mixed $component
     * @return int|float
     */
    private function normalizeComponent($component)
    {
        $parts = explode('/', $component);

        if (count($parts) > 1) {
            if ($parts[1]) {
                return intval($parts[0]) / intval($parts[1]);
            }

            return 0;
        }

        return floatval(reset($parts));
    }
}
