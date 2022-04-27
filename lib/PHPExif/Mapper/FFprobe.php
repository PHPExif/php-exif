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
use DateTime;
use Exception;

/**
 * PHP Exif Native Mapper
 *
 * Maps native raw data to valid data for the \PHPExif\Exif class
 *
 * @category    PHPExif
 * @package     Mapper
 */
class FFprobe implements MapperInterface
{
    const HEIGHT           = 'height';
    const WIDTH            = 'width';
    const FILESIZE         = 'size';
    const FILENAME         = 'filename';
    const FRAMERATE        = 'avg_frame_rate';
    const DURATION         = 'duration';
    const DATETIMEORIGINAL = 'creation_time';
    const GPSLATITUDE      = 'location';
    const GPSLONGITUDE     = 'location';
    const MIMETYPE         = 'MimeType';

    const QUICKTIME_GPSLATITUDE       = 'com.apple.quicktime.location.ISO6709';
    const QUICKTIME_GPSLONGITUDE      = 'com.apple.quicktime.location.ISO6709';
    const QUICKTIME_GPSALTITUDE       = 'com.apple.quicktime.location.ISO6709';
    const QUICKTIME_DATE              = 'com.apple.quicktime.creationdate';
    const QUICKTIME_DESCRIPTION       = 'com.apple.quicktime.description';
    const QUICKTIME_TITLE             = 'com.apple.quicktime.title';
    const QUICKTIME_KEYWORDS          = 'com.apple.quicktime.keywords';
    const QUICKTIME_MAKE              = 'com.apple.quicktime.make';
    const QUICKTIME_MODEL             = 'com.apple.quicktime.model';
    const QUICKTIME_CONTENTIDENTIFIER = 'com.apple.quicktime.content.identifier';


    /**
     * Maps the ExifTool fields to the fields of
     * the \PHPExif\Exif class
     *
     * @var array
     */
    protected $map = array(
        self::HEIGHT           => Exif::HEIGHT,
        self::WIDTH            => Exif::WIDTH,
        self::DATETIMEORIGINAL => Exif::CREATION_DATE,
        self::FILESIZE         => Exif::FILESIZE,
        self::FILENAME         => Exif::FILENAME,
        self::MIMETYPE         => Exif::MIMETYPE,
        self::GPSLATITUDE      => Exif::LATITUDE,
        self::GPSLONGITUDE     => Exif::LONGITUDE,
        self::FRAMERATE        => Exif::FRAMERATE,
        self::DURATION         => Exif::DURATION,

        self::QUICKTIME_DATE      => Exif::CREATION_DATE,
        self::QUICKTIME_DESCRIPTION       => Exif::DESCRIPTION,
        self::QUICKTIME_MAKE              => Exif::MAKE,
        self::QUICKTIME_TITLE             => Exif::TITLE,
        self::QUICKTIME_MODEL             => Exif::CAMERA,
        self::QUICKTIME_KEYWORDS          => Exif::KEYWORDS,
        self::QUICKTIME_GPSLATITUDE       => Exif::LATITUDE,
        self::QUICKTIME_GPSLONGITUDE      => Exif::LONGITUDE,
        self::QUICKTIME_GPSALTITUDE       => Exif::ALTITUDE,
        self::QUICKTIME_CONTENTIDENTIFIER => Exif::CONTENTIDENTIFIER,
    );

    const SECTION_TAGS      = 'tags';

    /**
     * A list of section names
     *
     * @var array
     */
    protected $sections = array(
        self::SECTION_TAGS
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

            // manipulate the value if necessary
            switch ($field) {
                case self::DATETIMEORIGINAL:
                    // QUICKTIME_DATE contains data on timezone
                    // only set value if QUICKTIME_DATE has not been used
                    if (!isset($mappedData[Exif::CREATION_DATE])
                            && preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 0) {
                        try {
                            // Some cameras add a '/' between date and time
                            // we need to remove it
                            $value = str_replace('/', '', $value);
                            $value = new DateTime($value);
                        } catch (\Exception $e) {
                            continue 2;
                        }
                    } else {
                        continue 2;
                    }

                    break;
                case self::QUICKTIME_DATE:
                    if (preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 1) {
                        continue 2;
                    }
                    try {
                        $value = new DateTime($value);
                    } catch (\Exception $e) {
                        continue 2;
                    }

                    break;
                case self::FRAMERATE:
                    $value = $this->normalizeComponent($value);
                    break;
                case self::GPSLATITUDE:
                case self::GPSLONGITUDE:
                    $matches = [];
                    preg_match('/^([+-][0-9\.]+)([+-][0-9\.]+)\/$/', $value, $matches);
                    if (count($matches) == 3 &&
                        !preg_match('/^\+0+\.0+$/', $matches[1]) &&
                        !preg_match('/^\+0+\.0+$/', $matches[2])) {
                        $mappedData[Exif::LATITUDE] = $matches[1];
                        $mappedData[Exif::LONGITUDE] = $matches[2];
                    }
                    continue 2;
                case self::QUICKTIME_GPSALTITUDE:
                case self::QUICKTIME_GPSLATITUDE:
                case self::QUICKTIME_GPSLONGITUDE:
                    $location_data = $this->readISO6709($value);
                    $mappedData[Exif::LATITUDE]  = $location_data['latitude'];
                    $mappedData[Exif::LONGITUDE] = $location_data['longitude'];
                    $mappedData[Exif::ALTITUDE]  = $location_data['altitude'];
                    //$value = $this->normalizeComponent($value);
                    continue 2;
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

        // Swap width and height if needed
        if (isset($data['tags']) && isset($data['tags']['rotate'])
            && ($data['tags']['rotate'] === '90' || $data['tags']['rotate'] === '270')) {
            $tmp = $mappedData[Exif::WIDTH];
            $mappedData[Exif::WIDTH] = $mappedData[Exif::HEIGHT];
            $mappedData[Exif::HEIGHT] = $tmp;
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
        // Catch case of one entry not being numeric
        if ($parts[1] == 0 || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
            return (float) 0;
        }
        return (float) $parts[0] / $parts[1];
    }


    /**

     * Converts results of ISO6709 parsing
     * to decimal format for latitude and longitude
     * See https://github.com/seanson/python-iso6709.git.
     *
     * @param string sign
     * @param string degrees
     * @param string minutes
     * @param string seconds
     * @param string fraction
     *
     * @return float
     */
    public function convertDMStoDecimal(
        string $sign,
        string $degrees,
        string $minutes,
        string $seconds,
        string $fraction
    ) : float {
        if ($fraction !== '') {
            if ($seconds !== '') {
                $seconds = $seconds . $fraction;
            } elseif ($minutes !== '') {
                $minutes = $minutes . $fraction;
            } else {
                $degrees = $degrees . $fraction;
            }
        }
        $decimal = floatval($degrees) + floatval($minutes) / 60.0 + floatval($seconds) / 3600.0;
        if ($sign == '-') {
            $decimal = -1.0 * $decimal;
        }
        return $decimal;
    }

    /**
     * Returns the latitude, longitude and altitude
     * of a GPS coordiante formattet with ISO6709
     * See https://github.com/seanson/python-iso6709.git.
     *
     * @param string val_ISO6709
     *
     * @return array
     */
    public function readISO6709(string $val_ISO6709) : array
    {
        $return = [
            'latitude' => null,
            'longitude' => null,
            'altitude' => null,
        ];
        $matches = [];
        // Adjustment compared to https://github.com/seanson/python-iso6709.git
        // Altitude have format +XX.XXXX -> Adjustment for decimal

        preg_match(
            '/^(?<lat_sign>\+|-)' .
            '(?<lat_degrees>[0,1]?\d{2})' .
            '(?<lat_minutes>\d{2}?)?' .
            '(?<lat_seconds>\d{2}?)?' .
            '(?<lat_fraction>\.\d+)?' .
            '(?<lng_sign>\+|-)' .
            '(?<lng_degrees>[0,1]?\d{2})' .
            '(?<lng_minutes>\d{2}?)?' .
            '(?<lng_seconds>\d{2}?)?' .
            '(?<lng_fraction>\.\d+)?' .
            '(?<alt>[\+\-][0-9]\d*(\.\d+)?)?\/$/',
            $val_ISO6709,
            $matches
        );

        $return['latitude'] =
            $this->convertDMStoDecimal(
                $matches['lat_sign'],
                $matches['lat_degrees'],
                $matches['lat_minutes'],
                $matches['lat_seconds'],
                $matches['lat_fraction']
            );

        $return['longitude'] =
            $this->convertDMStoDecimal(
                $matches['lng_sign'],
                $matches['lng_degrees'],
                $matches['lng_minutes'],
                $matches['lng_seconds'],
                $matches['lng_fraction']
            );
        if (isset($matches['alt'])) {
            $return['altitude'] = doubleval($matches['alt']);
        }
        return $return;
    }
}
