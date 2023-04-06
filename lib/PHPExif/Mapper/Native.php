<?php

namespace PHPExif\Mapper;

use PHPExif\Exif;
use Safe\DateTime;

use function Safe\preg_match;
use function Safe\preg_replace;

/**
 * PHP Exif Native Mapper
 *
 * Maps native raw data to valid data for the \PHPExif\Exif class
 *
 * @category    PHPExif
 * @package     Mapper
 */
class Native extends AbstractMapper
{
    public const APERTUREFNUMBER  = 'ApertureFNumber';
    public const ARTIST           = 'Artist';
    public const CAPTION          = 'caption';
    public const COLORSPACE       = 'ColorSpace';
    public const COPYRIGHT        = 'copyright';
    public const DATETIMEORIGINAL = 'DateTimeOriginal';
    public const CREDIT           = 'credit';
    public const EXPOSURETIME     = 'ExposureTime';
    public const FILESIZE         = 'FileSize';
    public const FILENAME         = 'FileName';
    public const FOCALLENGTH      = 'FocalLength';
    public const FOCUSDISTANCE    = 'FocusDistance';
    public const HEADLINE         = 'headline';
    public const HEIGHT           = 'Height';
    public const ISOSPEEDRATINGS  = 'ISOSpeedRatings';
    public const JOBTITLE         = 'jobtitle';
    public const KEYWORDS         = 'keywords';
    public const MIMETYPE         = 'MimeType';
    public const MODEL            = 'Model';
    public const ORIENTATION      = 'Orientation';
    public const SOFTWARE         = 'Software';
    public const SOURCE           = 'source';
    public const TITLE            = 'title';
    public const WIDTH            = 'Width';
    public const XRESOLUTION      = 'XResolution';
    public const YRESOLUTION      = 'YResolution';
    public const GPSLATITUDE      = 'GPSLatitude';
    public const GPSLONGITUDE     = 'GPSLongitude';
    public const GPSALTITUDE      = 'GPSAltitude';
    public const IMGDIRECTION     = 'GPSImgDirection';
    public const MAKE             = 'Make';
    public const LENS             = 'LensInfo';
    public const LENS_LR          = 'UndefinedTag:0xA434';
    public const LENS_TYPE        = 'LensType';
    public const DESCRIPTION      = 'ImageDescription';
    public const SUBJECT          = 'subject';
    public const FRAMERATE        = 'framerate';
    public const DURATION         = 'duration';
    public const CITY             = 'city';
    public const SUBLOCATION      = 'sublocation';
    public const STATE            = 'state';
    public const COUNTRY          = 'country';

    public const SECTION_FILE      = 'FILE';
    public const SECTION_COMPUTED  = 'COMPUTED';
    public const SECTION_IFD0      = 'IFD0';
    public const SECTION_THUMBNAIL = 'THUMBNAIL';
    public const SECTION_COMMENT   = 'COMMENT';
    public const SECTION_EXIF      = 'EXIF';
    public const SECTION_ALL       = 'ANY_TAG';
    public const SECTION_IPTC      = 'IPTC';

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
    public function mapRawData(array $data): array
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
            $value = $this->trim($value);

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
                            $value = new DateTime($value, $timezone);
                        } else {
                            $value = new DateTime($value);
                        }
                    } catch (\Exception $e) {
                        // Provided DateTimeOriginal or OffsetTimeOriginal invalid
                        continue 2;
                    }
                    break;
                case self::EXPOSURETIME:
                    if (!is_float($value)) {
                        $value = $this->normalizeComponent($value);
                        if ($value === false) {
                            continue 2;
                        }
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
                    if (end($parts) === '0') {
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
                    $GPSLatitudeRef = 'N';
                    if (array_key_exists('GPSLatitudeRef', $data)
                        && $data['GPSLatitudeRef'] !== null && $data['GPSLatitudeRef'][0] !== '') {
                        $GPSLatitudeRef = $data['GPSLatitudeRef'][0];
                    }
                    $value = $this->extractGPSCoordinate((array)$value, $GPSLatitudeRef);
                    if ($value === false) {
                        continue 2;
                    }
                    break;
                case self::GPSLONGITUDE:
                    $GPSLongitudeRef = 'E';
                    if (array_key_exists('GPSLongitudeRef', $data)
                        && $data['GPSLongitudeRef'] !== null && $data['GPSLongitudeRef'][0] !== '') {
                        $GPSLongitudeRef = $data['GPSLongitudeRef'][0];
                    }
                    $value = $this->extractGPSCoordinate((array)$value, $GPSLongitudeRef);
                    if ($value === false) {
                        continue 2;
                    }
                    break;
                case self::GPSALTITUDE:
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $flp = 1;
                    if (array_key_exists('GPSAltitudeRef', $data) && $data['GPSAltitudeRef'][0] !== '') {
                        $flp = (
                            $data['GPSAltitudeRef'][0] === '1'
                            || $data['GPSAltitudeRef'][0] === "\u{0001}"
                        ) ? -1 : 1;
                    }
                    $value *= $flp;
                    break;
                case self::IMGDIRECTION:
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
                    }
                    break;
                    // Merge sources of keywords
                case self::KEYWORDS:
                case self::SUBJECT:
                    $xval = is_array($value) ? $value : [$value];
                    if (!array_key_exists(Exif::KEYWORDS, $mappedData)) {
                        $mappedData[Exif::KEYWORDS] = $xval;
                    } else {
                        $tmp = array_values(array_unique(array_merge($mappedData[Exif::KEYWORDS], $xval)));
                        $mappedData[Exif::KEYWORDS] = $tmp;
                    }

                    continue 2;
                case self::LENS_LR:
                    if (!array_key_exists(Exif::LENS, $mappedData)) {
                        $mappedData[Exif::LENS] = $value;
                    }
                    continue 2;
                case self::LENS_TYPE:
                    if (!array_key_exists(Exif::LENS, $mappedData)) {
                        $mappedData[Exif::LENS] = $value;
                    }
                    continue 2;
            }

            // set end result
            $mappedData[$key] = $value;
        }

        // add GPS coordinates, if available
        if ((isset($mappedData[Exif::LATITUDE])) && (isset($mappedData[Exif::LONGITUDE]))) {
            $mappedData[Exif::GPS] =
                sprintf('%s,%s', (string) $mappedData[Exif::LATITUDE], (string) $mappedData[Exif::LONGITUDE]);
        }

        return $mappedData;
    }

    /**
     * Determines if given field is a section
     *
     * @param string $field
     * @return bool
     */
    protected function isSection(string $field): bool
    {
        return (in_array($field, $this->sections, true));
    }

    /**
     * Determines if the given field is known,
     * in a case insensitive way for its first letter.
     * Also update $field to keep it valid against the known fields.
     *
     * @param  string  &$field
     * @return bool
     */
    protected function isFieldKnown(string &$field): bool
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
     * @return float|false
     */
    protected function extractGPSCoordinate(array $coordinate, string $ref): float|false
    {
        $degrees = count($coordinate) > 0 ? $this->normalizeComponent($coordinate[0]) : 0;
        $minutes = count($coordinate) > 1 ? $this->normalizeComponent($coordinate[1]) : 0;
        $seconds = count($coordinate) > 2 ? $this->normalizeComponent($coordinate[2]) : 0;
        if ($degrees === false || $minutes === false || $seconds === false) {
            return false;
        }
        $flip = ($ref === 'W' || $ref === 'S') ? -1 : 1;
        return round($flip * ($degrees + (float) $minutes / 60 + (float) $seconds / 3600), self::ROUNDING_PRECISION);
    }

    /**
     * Normalize component
     *
     * @param string $rational
     * @return float|false
     */
    protected function normalizeComponent(string $rational): float|false
    {
        $parts = explode('/', $rational, 2);
        if (count($parts) === 1) {
            return (float) $parts[0];
        }
        // case part[1] is 0, div by 0 is forbidden.
        if ($parts[1] === '0' || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
            return false;
        }
        return (float) $parts[0] / $parts[1];
    }
}
