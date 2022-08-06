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
use Safe\DateTime;

use function Safe\preg_match;
use function Safe\preg_replace;
use function Safe\preg_split;
use function Safe\sprintf;

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
    const HEIGHT                   = 'height';
    const IMAGEWIDTH               = 'exif:PixelXDimension';
    const IMAGEWIDTH_PNG           = 'png:IHDR.width,height';
    const WIDTH                    = 'width';
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
    const TITLE                    = 'iptc:title';
    const KEYWORDS                 = 'iptc:keywords';
    const COPYRIGHT                = 'iptc:copyright';
    const CAPTION                  = 'iptc:caption';
    const HEADLINE                 = 'iptc:headline';
    const CREDIT                   = 'iptc:credit';
    const SOURCE                   = 'iptc:source';
    const JOBTITLE                 = 'iptc:jobtitle';
    const CITY                     = 'iptc:city';
    const SUBLOCATION              = 'iptc:sublocation';
    const STATE                    = 'iptc:state';
    const COUNTRY                  = 'iptc:country';


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
        self::HEIGHT                   => Exif::HEIGHT,
        self::IMAGEWIDTH               => Exif::WIDTH,
        self::IMAGEWIDTH_PNG           => Exif::WIDTH,
        self::WIDTH                    => Exif::WIDTH,
        self::ISO                      => Exif::ISO,
        self::LENS                     => Exif::LENS,
        self::MAKE                     => Exif::MAKE,
        self::MIMETYPE                 => Exif::MIMETYPE,
        self::MODEL                    => Exif::CAMERA,
        self::ORIENTATION              => Exif::ORIENTATION,
        self::SOFTWARE                 => Exif::SOFTWARE,
        self::XRESOLUTION              => Exif::HORIZONTAL_RESOLUTION,
        self::YRESOLUTION              => Exif::VERTICAL_RESOLUTION,
        self::TITLE                    => Exif::TITLE,
        self::KEYWORDS                 => Exif::KEYWORDS,
        self::COPYRIGHT                => Exif::COPYRIGHT,
        self::CAPTION                  => Exif::CAPTION,
        self::HEADLINE                 => Exif::HEADLINE,
        self::CREDIT                   => Exif::CREDIT,
        self::SOURCE                   => Exif::SOURCE,
        self::JOBTITLE                 => EXIF::JOB_TITLE,
        self::CITY                     => Exif::CITY,
        self::SUBLOCATION              => Exif::SUBLOCATION,
        self::STATE                    => Exif::STATE,
        self::COUNTRY                  => Exif::COUNTRY

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
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $value = sprintf('f/%01.1f', $value);
                    break;
                case self::CREATION_DATE:
                    if (!isset($mappedData[Exif::CREATION_DATE])
                            && preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 0) {
                        try {
                            $value = new DateTime($value);
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
                            $value = new DateTime($value, $timezone);
                        } else {
                            $value = new DateTime($value);
                        }
                    } catch (\Exception $e) {
                        continue 2;
                    }
                    break;
                case self::EXPOSURETIME:
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
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
                    if (strpos($value, ' ') !== false) {
                        $focalLengthParts = explode(' ', $value);
                        $value = reset($focalLengthParts);
                    }
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
                    }
                    break;
                case self::ISO:
                    $value = preg_split('/([\s,]+)/', $value)[0];
                    break;
                case self::XRESOLUTION:
                case self::YRESOLUTION:
                    $resolutionParts = explode('/', $value);
                    $value = (int) reset($resolutionParts);
                    break;
                case self::GPSLATITUDE:
                    $value = $this->extractGPSCoordinates($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $latitudeRef = !array_key_exists('exif:GPSLatitudeRef', $data)
                        || $data['exif:GPSLatitudeRef'] === null || $data['exif:GPSLatitudeRef'] === '' ?
                        'N' : $data['exif:GPSLatitudeRef'][0];
                    $value *= strtoupper($latitudeRef) === 'S' ? -1 : 1;
                    break;
                case self::GPSLONGITUDE:
                    $value  = $this->extractGPSCoordinates($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $longitudeRef = !array_key_exists('exif:GPSLongitudeRef', $data)
                        || $data['exif:GPSLongitudeRef'] === null || $data['exif:GPSLongitudeRef'] === '' ?
                        'E' : $data['exif:GPSLongitudeRef'][0];
                    $value *= strtoupper($longitudeRef) === 'W' ? -1 : 1;
                    break;
                case self::GPSALTITUDE:
                    $flip = 1;
                    if (array_key_exists('exif:GPSAltitudeRef', $data)) {
                        $flip = ($data['exif:GPSAltitudeRef'] === '1') ? -1 : 1;
                    }
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $value *= $flip;
                    break;
                case self::IMAGEHEIGHT_PNG:
                case self::IMAGEWIDTH_PNG:
                    $value_split = explode(",", $value);

                    $mappedData[Exif::WIDTH]  = intval($value_split[0]);
                    $mappedData[Exif::HEIGHT] = intval($value_split[1]);
                    continue 2;
                case self::IMGDIRECTION:
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
                    }
                    break;
                case self::KEYWORDS:
                    if (!is_array($value)) {
                        $value = [$value];
                    }
                    break;
            }
            // set end result
            $mappedData[$key] = $value;
        }

        // add GPS coordinates, if available
        if ((isset($mappedData[Exif::LATITUDE])) && (isset($mappedData[Exif::LONGITUDE]))) {
            $mappedData[Exif::GPS] = sprintf('%s,%s', $mappedData[Exif::LATITUDE], $mappedData[Exif::LONGITUDE]);
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
            $m = '!^([0-9]+\/[1-9][0-9]*)(?:, ([0-9]+\/[1-9][0-9]*))?(?:, ([0-9]+\/[1-9][0-9]*))?$!';
            if (preg_match($m, $coordinates, $matches) === 0) {
                return false;
            }
            $degrees = $this->normalizeComponent($matches[1]);
            $minutes = $this->normalizeComponent($matches[2] ?? 0);
            $seconds = $this->normalizeComponent($matches[3] ?? 0);
            if ($degrees === false || $minutes === false || $seconds === false) {
                return false;
            }
            return $degrees + $minutes / 60 + $seconds / 3600;
        }
    }

    /**
     * Normalize component
     *
     * @param string $rational
     * @return float|false
     */
    protected function normalizeComponent(string $rational) : float|false
    {
        $parts = explode('/', $rational, 2);
        if (count($parts) === 1) {
            return (float) $parts[0];
        }
        // case part[1] is 0, div by 0 is forbidden.
        // Catch case of one entry not being numeric
        if ($parts[1] === '0' || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
            return false;
        }
        return (float) $parts[0] / $parts[1];
    }
}
