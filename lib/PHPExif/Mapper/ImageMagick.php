<?php

namespace PHPExif\Mapper;

use PHPExif\Exif;
use Safe\DateTime;

use function Safe\preg_match;
use function Safe\preg_replace;
use function Safe\preg_split;

/**
 * PHP Exif Imagick Mapper
 *
 * Maps Imagick raw data to valid data for the \PHPExif\Exif class
 *
 * @category    PHPExif
 * @package     Mapper
 */
class ImageMagick extends AbstractMapper
{
    public const APERTURE                 = 'exif:FNumber';
    public const ARTIST                   = 'exif:Artist';
    public const COLORSPACE               = 'exif:ColorSpace';
    public const COPYRIGHT                = 'exif:Copyright';
    public const DATETIMEORIGINAL         = 'exif:DateTimeOriginal';
    public const DESCRIPTION              = 'exif:ImageDescription';
    public const EXPOSURETIME             = 'exif:ExposureTime';
    public const FILESIZE                 = 'filesize';
    public const FILENAME                 = 'filename';
    public const FOCALLENGTH              = 'exif:FocalLength';
    public const GPSLATITUDE              = 'exif:GPSLatitude';
    public const GPSLONGITUDE             = 'exif:GPSLongitude';
    public const GPSALTITUDE              = 'exif:GPSAltitude';
    public const IMAGEHEIGHT              = 'exif:PixelYDimension';
    public const IMAGEHEIGHT_PNG          = 'png:IHDR.width,height';
    public const HEIGHT                   = 'height';
    public const IMAGEWIDTH               = 'exif:PixelXDimension';
    public const IMAGEWIDTH_PNG           = 'png:IHDR.width,height';
    public const WIDTH                    = 'width';
    public const IMGDIRECTION             = 'exif:GPSImgDirection';
    public const ISO                      = 'exif:PhotographicSensitivity';
    public const LENS                     = 'exif:LensModel';
    public const MAKE                     = 'exif:Make';
    public const MIMETYPE                 = 'MimeType';
    public const MODEL                    = 'exif:Model';
    public const ORIENTATION              = 'exif:Orientation';
    public const SOFTWARE                 = 'exif:Software';
    public const XRESOLUTION              = 'exif:XResolution';
    public const YRESOLUTION              = 'exif:YResolution';
    public const TITLE                    = 'iptc:title';
    public const KEYWORDS                 = 'iptc:keywords';
    public const COPYRIGHT_IPTC           = 'iptc:copyright';
    public const CAPTION                  = 'iptc:caption';
    public const HEADLINE                 = 'iptc:headline';
    public const CREDIT                   = 'iptc:credit';
    public const SOURCE                   = 'iptc:source';
    public const JOBTITLE                 = 'iptc:jobtitle';
    public const CITY                     = 'iptc:city';
    public const SUBLOCATION              = 'iptc:sublocation';
    public const STATE                    = 'iptc:state';
    public const COUNTRY                  = 'iptc:country';


    /**
     * Maps the ExifTool fields to the fields of
     * the \PHPExif\Exif class
     *
     * @var array
     */
    protected array $map = array(
        self::APERTURE                 => Exif::APERTURE,
        self::ARTIST                   => Exif::AUTHOR,
        self::COLORSPACE               => Exif::COLORSPACE,
        self::COPYRIGHT                => Exif::COPYRIGHT,
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
        self::COPYRIGHT_IPTC           => Exif::COPYRIGHT,
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
    public function mapRawData(array $data): array
    {
        $mappedData = array();

        foreach ($data as $field => $value) {
            if (!array_key_exists($field, $this->map)) {
                // silently ignore unknown fields
                continue;
            }

            $key = $this->map[$field];
            $value = $this->trim($value);

            // manipulate the value if necessary
            switch ($field) {
                case self::APERTURE:
                    $value = $this->normalizeComponent($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $value = sprintf('f/%01.1f', $value);
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
            $mappedData[Exif::GPS] =
                sprintf('%s,%s', (string) $mappedData[Exif::LATITUDE], (string) $mappedData[Exif::LONGITUDE]);
        }
        return $mappedData;
    }

    /**
     * Extract GPS coordinates from formatted string
     *
     * @param string $coordinates
     * @return float|false
     */
    protected function extractGPSCoordinates(string $coordinates): float|false
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
            return round($degrees + $minutes / 60 + $seconds / 3600, self::ROUNDING_PRECISION);
        }
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
        // Catch case of one entry not being numeric
        if ($parts[1] === '0' || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
            return false;
        }
        return (float) $parts[0] / $parts[1];
    }
}
