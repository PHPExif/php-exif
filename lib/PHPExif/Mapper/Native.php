<?php
/**
 * PHP Exif Native Mapper
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Mapper
 */

namespace PHPExif\Mapper;

use PHPExif\Exif;

/**
 * PHP Exif Native Mapper
 *
 * Maps native raw data to valid data for the \PHPExif\Exif class
 *
 * @category    PHPExif
 * @package     Mapper
 */
class Native implements MapperInterface
{
    const APERTUREFNUMBER  = 'ApertureFNumber';
    const ARTIST           = 'Artist';
    const CAPTION          = 'caption';
    const COLORSPACE       = 'ColorSpace';
    const COPYRIGHT        = 'copyright';
    const DATETIMEORIGINAL = 'DateTimeOriginal';
    const CREDIT           = 'credit';
    const EXPOSURETIME     = 'ExposureTime';
    const FILESIZE         = 'FileSize';
    const FILENAME         = 'FileName';
    const FOCALLENGTH      = 'FocalLength';
    const FOCUSDISTANCE    = 'FocusDistance';
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
    const GPSLATITUDE      = 'GPSLatitude';
    const GPSLONGITUDE     = 'GPSLongitude';
    const GPSALTITUDE      = 'GPSAltitude';
    const IMGDIRECTION     = 'GPSImgDirection';
    const MAKE             = 'Make';
    const LENS             = 'LensInfo';
    const LENS_LR          = 'UndefinedTag:0xA434';
    const LENS_TYPE        = 'LensType';
    const DESCRIPTION      = 'caption';
    const SUBJECT          = 'subject';
    const FRAMERATE        = 'framerate';
    const DURATION         = 'duration';
    const CITY             = 'city';
    const SUBLOCATION      = 'sublocation';
    const STATE            = 'state';
    const COUNTRY          = 'country';

    const SECTION_FILE      = 'FILE';
    const SECTION_COMPUTED  = 'COMPUTED';
    const SECTION_IFD0      = 'IFD0';
    const SECTION_THUMBNAIL = 'THUMBNAIL';
    const SECTION_COMMENT   = 'COMMENT';
    const SECTION_EXIF      = 'EXIF';
    const SECTION_ALL       = 'ANY_TAG';
    const SECTION_IPTC      = 'IPTC';

    /**
     * A list of section names
     *
     * @var array
     */
    protected array $sections = array(
        self::SECTION_FILE,
        self::SECTION_COMPUTED,
        self::SECTION_IFD0,
        self::SECTION_THUMBNAIL,
        self::SECTION_COMMENT,
        self::SECTION_EXIF,
        self::SECTION_ALL,
        self::SECTION_IPTC,
    );

    /**
     * Maps the ExifTool fields to the fields of
     * the \PHPExif\Exif class
     *
     * @var array
     */
    protected array $map = array(
        self::APERTUREFNUMBER  => Exif::APERTURE,
        self::FOCUSDISTANCE    => Exif::FOCAL_DISTANCE,
        self::HEIGHT           => Exif::HEIGHT,
        self::WIDTH            => Exif::WIDTH,
        self::CAPTION          => Exif::CAPTION,
        self::COPYRIGHT        => Exif::COPYRIGHT,
        self::CREDIT           => Exif::CREDIT,
        self::HEADLINE         => Exif::HEADLINE,
        self::JOBTITLE         => Exif::JOB_TITLE,
        self::KEYWORDS         => Exif::KEYWORDS,
        self::SOURCE           => Exif::SOURCE,
        self::TITLE            => Exif::TITLE,
        self::ARTIST           => Exif::AUTHOR,
        self::MODEL            => Exif::CAMERA,
        self::COLORSPACE       => Exif::COLORSPACE,
        self::DATETIMEORIGINAL => Exif::CREATION_DATE,
        self::EXPOSURETIME     => Exif::EXPOSURE,
        self::FILESIZE         => Exif::FILESIZE,
        self::FILENAME         => Exif::FILENAME,
        self::FOCALLENGTH      => Exif::FOCAL_LENGTH,
        self::ISOSPEEDRATINGS  => Exif::ISO,
        self::MIMETYPE         => Exif::MIMETYPE,
        self::ORIENTATION      => Exif::ORIENTATION,
        self::SOFTWARE         => Exif::SOFTWARE,
        self::XRESOLUTION      => Exif::HORIZONTAL_RESOLUTION,
        self::YRESOLUTION      => Exif::VERTICAL_RESOLUTION,
        self::GPSLATITUDE      => Exif::LATITUDE,
        self::GPSLONGITUDE     => Exif::LONGITUDE,
        self::GPSALTITUDE      => Exif::ALTITUDE,
        self::IMGDIRECTION     => Exif::IMGDIRECTION,
        self::MAKE             => Exif::MAKE,
        self::LENS             => Exif::LENS,
        self::LENS_LR          => Exif::LENS,
        self::LENS_TYPE        => Exif::LENS,
        self::DESCRIPTION      => Exif::DESCRIPTION,
        self::SUBJECT          => Exif::KEYWORDS,
        self::FRAMERATE        => Exif::FRAMERATE,
        self::DURATION         => Exif::DURATION,
        self::SUBLOCATION      => Exif::SUBLOCATION,
        self::CITY             => Exif::CITY,
        self::STATE            => Exif::STATE,
        self::COUNTRY          => Exif::COUNTRY

    );

    /**
     * Maps the array of raw source data to the correct
     * fields for the \PHPExif\Exif class
     *
     * @param array $data
     * @return array
     */
    public function mapRawData(array $data) : array
    {
        $mappedData = array();

        foreach ($data as $field => $value) {
            if ($this->isSection($field) && is_array($value)) {
                $subData = $this->mapRawData($value);

                $mappedData = array_merge($mappedData, $subData);
                continue;
            }

            if (!$this->isFieldKnown($field)) {
                // silently ignore unknown fields
                continue;
            }

            $key = $this->map[$field];
            if (is_string($value)) {
                $value = trim($value);
            }

            // manipulate the value if necessary
            switch ($field) {
                case self::DATETIMEORIGINAL:
                    if (preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 1) {
                        continue 2;
                    }
                    // Check if OffsetTimeOriginal (0x9011) is available
                    try {
                        if (isset($data['UndefinedTag:0x9011'])) {
                            try {
                                $timezone = new \DateTimeZone($data['UndefinedTag:0x9011']);
                            } catch (\Exception $e) {
                                $timezone = null;
                            }
                            $value = new \DateTime($value, $timezone);
                        } else {
                            $value = new \DateTime($value);
                        }
                    } catch (\Exception $e) {
                        // Provided DateTimeOriginal or OffsetTimeOriginal invalid
                        continue 2;
                    }
                    break;
                case self::EXPOSURETIME:
                    if (!is_float($value)) {
                        $value = $this->normalizeComponent($value);
                    }

                    // Based on the source code of Exiftool (PrintExposureTime subroutine):
                    // http://cpansearch.perl.org/src/EXIFTOOL/Image-ExifTool-9.90/lib/Image/ExifTool/Exif.pm
                    if ($value < 0.25001 && $value > 0) {
                        $value = sprintf('1/%d', intval(0.5 + 1 / $value));
                    } else {
                        $value = sprintf('%.1f', $value);
                        $value = preg_replace('/.0$/', '', $value);
                    }
                    break;
                case self::FOCALLENGTH:
                    $parts = explode('/', $value);
                    // Avoid division by zero if focal length is invalid
                    if (end($parts) == '0') {
                        $value = 0;
                    } else {
                        $value = (int) reset($parts) / (int) end($parts);
                    }
                    break;
                case self::ISOSPEEDRATINGS:
                    $value = explode(" ", (is_array($value) ? $value[0] : $value))[0];
                    break;
                case self::XRESOLUTION:
                case self::YRESOLUTION:
                    $resolutionParts = explode('/', $value);
                    $value = (int) reset($resolutionParts);
                    break;
                case self::GPSLATITUDE:
                    $GPSLatitudeRef = (!(empty($data['GPSLatitudeRef'][0]))) ? $data['GPSLatitudeRef'][0] : '';
                    $value = $this->extractGPSCoordinate((array)$value, $GPSLatitudeRef);
                    break;
                case self::GPSLONGITUDE:
                    $GPSLongitudeRef = (!(empty($data['GPSLongitudeRef'][0]))) ? $data['GPSLongitudeRef'][0] : '';
                    $value = $this->extractGPSCoordinate((array)$value, $GPSLongitudeRef);
                    break;
                case self::GPSALTITUDE:
                    $flp = 1;
                    if (!(empty($data['GPSAltitudeRef'][0]))) {
                        $flp = ($data['GPSAltitudeRef'][0] == '1' || $data['GPSAltitudeRef'][0] == "\u{0001}") ? -1 : 1;
                    }
                    $value = $flp * $this->normalizeComponent($value);
                    break;
                case self::IMGDIRECTION:
                    $value = $this->normalizeComponent($value);
                    break;
                case self::LENS_LR:
                    if (empty($mappedData[Exif::LENS])) {
                        $mappedData[Exif::LENS] = $value;
                    }
                    continue 2;
                    break;
                case self::LENS_TYPE:
                    if (empty($mappedData[Exif::LENS])) {
                        $mappedData[Exif::LENS] = $value;
                    }
                    continue 2;
                    break;
            }

            // set end result
            $mappedData[$key] = $value;
        }

        // add GPS coordinates, if available
        if ((isset($mappedData[Exif::LATITUDE])) && (isset($mappedData[Exif::LONGITUDE]))) {
            $mappedData[Exif::GPS] = sprintf('%s,%s', $mappedData[Exif::LATITUDE], $mappedData[Exif::LONGITUDE]);
        } else {
            unset($mappedData[Exif::GPS]);
        }

        return $mappedData;
    }

    /**
     * Determines if given field is a section
     *
     * @param string $field
     * @return bool
     */
    protected function isSection(string $field) : bool
    {
        return (in_array($field, $this->sections));
    }

    /**
     * Determines if the given field is known,
     * in a case insensitive way for its first letter.
     * Also update $field to keep it valid against the known fields.
     *
     * @param  string  &$field
     * @return bool
     */
    protected function isFieldKnown(string &$field) : bool
    {
        $lcfField = lcfirst($field);
        if (array_key_exists($lcfField, $this->map)) {
            $field = $lcfField;

            return true;
        }

        $ucfField = ucfirst($field);
        if (array_key_exists($ucfField, $this->map)) {
            $field = $ucfField;

            return true;
        }

        return false;
    }

    /**
     * Extract GPS coordinates from components array
     *
     * @param array $coordinate
     * @param string $ref
     * @return float
     */
    protected function extractGPSCoordinate(array $coordinate, string $ref) : float
    {
        $degrees = count($coordinate) > 0 ? $this->normalizeComponent($coordinate[0]) : 0;
        $minutes = count($coordinate) > 1 ? $this->normalizeComponent($coordinate[1]) : 0;
        $seconds = count($coordinate) > 2 ? $this->normalizeComponent($coordinate[2]) : 0;
        $flip = ($ref == 'W' || $ref == 'S') ? -1 : 1;
        return $flip * ($degrees + (float) $minutes / 60 + (float) $seconds / 3600);
    }

    /**
     * Normalize component
     *
     * @param string $component
     * @return float
     */
    protected function normalizeComponent(string $rational) : float
    {
        $parts = explode('/', $rational, 2);
        if (count($parts) == 1) {
            return (float) $parts[0];
        }
        // case part[1] is 0, div by 0 is forbidden.
        if ($parts[1] == 0 || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
            return (float) 0;
        }
        return (float) $parts[0] / $parts[1];
    }
}
