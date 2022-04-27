<?php
/**
 * PHP Imagick Exiftool Mapper
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
 * PHP Exif Imagick Mapper
 *
 * Maps Imagick raw data to valid data for the \PHPExif\Exif class
 *
 * @category    PHPExif
 * @package     Mapper
 */
class ImageMagick implements MapperInterface
{
    const APERTURE                 = 'exif:FNumber';
    const COLORSPACE               = 'exif:ColorSpace';
    const CREATION_DATE            = 'date:create';
    const DATETIMEORIGINAL         = 'exif:DateTimeOriginal';
    const DESCRIPTION              = 'exif:ImageDescription ';
    const EXPOSURETIME             = 'exif:ExposureTime';
    const FILESIZE                 = 'filesize';
    const FILENAME                 = 'filename';
    const FOCALLENGTH              = 'exif:FocalLength';
    const GPSLATITUDE              = 'exif:GPSLatitude';
    const GPSLONGITUDE             = 'exif:GPSLongitude';
    const GPSALTITUDE              = 'exif:GPSAltitude';
    const IMAGEHEIGHT              = 'exif:PixelYDimension';
    const IMAGEHEIGHT_PNG          = 'png:IHDR.width,height';
    const IMAGEWIDTH               = 'exif:PixelXDimension';
    const IMAGEWIDTH_PNG           = 'png:IHDR.width,height';
    const IMGDIRECTION             = 'exif:GPSImgDirection';
    const ISO                      = 'exif:PhotographicSensitivity';
    const LENS                     = 'exif:LensModel';
    const MAKE                     = 'exif:Make';
    const MIMETYPE                 = 'MimeType';
    const MODEL                    = 'exif:Model';
    const ORIENTATION              = 'exif:Orientation';
    const SOFTWARE                 = 'exif:Software';
    const XRESOLUTION              = 'exif:XResolution';
    const YRESOLUTION              = 'exif:YResolution';

    /**
     * Maps the ExifTool fields to the fields of
     * the \PHPExif\Exif class
     *
     * @var array
     */
    protected array $map = array(
        self::APERTURE                 => Exif::APERTURE,
        self::COLORSPACE               => Exif::COLORSPACE,
        self::CREATION_DATE            => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL         => Exif::CREATION_DATE,
        self::DESCRIPTION              => Exif::DESCRIPTION,
        self::EXPOSURETIME             => Exif::EXPOSURE,
        self::FILESIZE                 => Exif::FILESIZE,
        self::FILENAME                 => Exif::FILENAME,
        self::FOCALLENGTH              => Exif::FOCAL_LENGTH,
        self::GPSLATITUDE              => Exif::LATITUDE,
        self::GPSLONGITUDE             => Exif::LONGITUDE,
        self::GPSALTITUDE              => Exif::ALTITUDE,
        self::IMGDIRECTION             => Exif::IMGDIRECTION,
        self::IMAGEHEIGHT              => Exif::HEIGHT,
        self::IMAGEHEIGHT_PNG          => Exif::HEIGHT,
        self::IMAGEWIDTH               => Exif::WIDTH,
        self::IMAGEWIDTH_PNG           => Exif::WIDTH,
        self::ISO                      => Exif::ISO,
        self::LENS                     => Exif::LENS,
        self::MAKE                     => Exif::MAKE,
        self::MIMETYPE                 => Exif::MIMETYPE,
        self::MODEL                    => Exif::CAMERA,
        self::ORIENTATION              => Exif::ORIENTATION,
        self::SOFTWARE                 => Exif::SOFTWARE,
        self::YRESOLUTION              => Exif::VERTICAL_RESOLUTION,


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
            if (!array_key_exists($field, $this->map)) {
                // silently ignore unknown fields
                continue;
            }

            $key = $this->map[$field];

            // manipulate the value if necessary
            switch ($field) {
                case self::APERTURE:
                    $value = sprintf('f/%01.1f', $this->normalizeComponent($value));
                    break;
                case self::CREATION_DATE:
                    if (!isset($mappedData[Exif::CREATION_DATE])
                            && preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 0) {
                        try {
                            $value = new \DateTime($value);
                        } catch (\Exception $e) {
                            continue 2;
                        }
                    } else {
                        continue 2;
                    }
                    break;
                case self::DATETIMEORIGINAL:
                    if (preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 1) {
                        continue 2;
                    }
                    try {
                        if (isset($data['exif:OffsetTimeOriginal'])) {
                            try {
                                $timezone = new \DateTimeZone($data['exif:OffsetTimeOriginal']);
                            } catch (\Exception $e) {
                                $timezone = null;
                            }
                            $value = new \DateTime($value, $timezone);
                        } else {
                            $value = new \DateTime($value);
                        }
                    } catch (\Exception $e) {
                        continue 2;
                    }
                    break;
                case self::EXPOSURETIME:
                    $value = $this->normalizeComponent($value);
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
                    if (strpos($value, ' ') !== false) {
                        $focalLengthParts = explode(' ', $value);
                        $value = reset($focalLengthParts);
                    }
                    $value = $this->normalizeComponent($value);
                    break;
                case self::ISO:
                    $value = preg_split('/([\s,]+)/', $value)[0];
                    break;
                case self::GPSLATITUDE:
                    $latitudeRef = empty($data['exif:GPSLatitudeRef']) ? 'N' : $data['exif:GPSLatitudeRef'][0];
                    $value = $this->extractGPSCoordinates($value);
                    if ($value !== false) {
                        $value = (strtoupper($latitudeRef) === 'S' ? -1.0 : 1.0) * $value;
                    } else {
                        $value = false;
                    }

                    break;
                case self::GPSLONGITUDE:
                    $longitudeRef = empty($data['exif:GPSLongitudeRef']) ? 'E' : $data['exif:GPSLongitudeRef'][0];
                    $value  = $this->extractGPSCoordinates($value);
                    if ($value !== false) {
                        $value  = (strtoupper($longitudeRef) === 'W' ? -1 : 1) * $value;
                    }

                    break;
                case self::GPSALTITUDE:
                    $flip = 1;
                    if (!(empty($data['exif:GPSAltitudeRef']))) {
                        $flip = ($data['exif:GPSAltitudeRef'] == '1') ? -1 : 1;
                    }
                    $value = $flip * (float) $this->normalizeComponent($value);
                    break;
                case self::IMAGEHEIGHT_PNG:
                case self::IMAGEWIDTH_PNG:
                    $value_splitted = explode(",", $value);

                    $mappedData[Exif::WIDTH]  = intval($value_splitted[0]);
                    $mappedData[Exif::HEIGHT] = intval($value_splitted[1]);
                    continue 2;
                    break;
                case self::IMGDIRECTION:
                    $value = $this->normalizeComponent($value);
                    break;
            }
            // set end result
            $mappedData[$key] = $value;
        }

        // add GPS coordinates, if available
        if ((isset($mappedData[Exif::LATITUDE])) && (isset($mappedData[Exif::LONGITUDE]))) {
            if (($mappedData[Exif::LATITUDE]!==false) && $mappedData[Exif::LONGITUDE]!==false) {
                $mappedData[Exif::GPS] = sprintf('%s,%s', $mappedData[Exif::LATITUDE], $mappedData[Exif::LONGITUDE]);
            } else {
                $mappedData[Exif::GPS] = false;
            }
        } else {
            unset($mappedData[Exif::GPS]);
        }
        return $mappedData;
    }

    /**
     * Extract GPS coordinates from formatted string
     *
     * @param string $coordinates
     * @return float|false
     */
    protected function extractGPSCoordinates(string $coordinates) : float|false
    {
        if (is_numeric($coordinates) === true) {
            return ((float) $coordinates);
        } else {
            $m = '!^([1-9][0-9]*\/[1-9][0-9]*), ([1-9][0-9]*\/[1-9][0-9]*), ([1-9][0-9]*\/[1-9][0-9]*)!';
            if (!preg_match($m, $coordinates, $matches)) {
                return false;
            }
            $degree = floatval($this->normalizeComponent($matches[1]));
            $minutes = floatval($this->normalizeComponent($matches[2]));
            $seconds = floatval($this->normalizeComponent($matches[3]));
            return $degree + $minutes / 60 + $seconds / 3600;
        }
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
}
