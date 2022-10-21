<?php
/**
 * PHP Exif Exiftool Mapper
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

/**
 * PHP Exif Exiftool Mapper
 *
 * Maps Exiftool raw data to valid data for the \PHPExif\Exif class
 *
 * @category    PHPExif
 * @package     Mapper
 */
class Exiftool implements MapperInterface
{
    public const APERTURE                 = 'Composite:Aperture';
    public const APPROXIMATEFOCUSDISTANCE = 'XMP-aux:ApproximateFocusDistance';
    public const ARTIST                   = 'IFD0:Artist';
    public const CAPTIONABSTRACT          = 'IPTC:Caption-Abstract';
    public const COLORSPACE               = 'ExifIFD:ColorSpace';
    public const COPYRIGHT                = 'IFD0:Copyright';
    public const DATETIMEORIGINAL         = 'ExifIFD:DateTimeOriginal';
    public const CREDIT                   = 'IPTC:Credit';
    public const EXPOSURETIME             = 'ExifIFD:ExposureTime';
    public const FILESIZE                 = 'System:FileSize';
    public const FILENAME                 = 'System:FileName';
    public const FOCALLENGTH              = 'ExifIFD:FocalLength';
    public const HEADLINE                 = 'IPTC:Headline';
    public const IMAGEHEIGHT              = 'File:ImageHeight';
    public const IMAGEWIDTH               = 'File:ImageWidth';
    public const ISO                      = 'ExifIFD:ISO';
    public const JOBTITLE                 = 'IPTC:By-lineTitle';
    public const KEYWORDS                 = 'IPTC:Keywords';
    public const MIMETYPE                 = 'File:MIMEType';
    public const MODEL                    = 'IFD0:Model';
    public const ORIENTATION              = 'IFD0:Orientation';
    public const SOFTWARE                 = 'IFD0:Software';
    public const SOURCE                   = 'IPTC:Source';
    public const TITLE                    = 'IPTC:ObjectName';
    public const TITLE_XMP                = 'XMP-dc:Title';
    public const XRESOLUTION              = 'IFD0:XResolution';
    public const YRESOLUTION              = 'IFD0:YResolution';
    public const GPSLATITUDE              = 'GPS:GPSLatitude';
    public const GPSLONGITUDE             = 'GPS:GPSLongitude';
    public const GPSALTITUDE              = 'GPS:GPSAltitude';
    public const IMGDIRECTION             = 'GPS:GPSImgDirection';
    public const DESCRIPTION              = 'IFD0:ImageDescription';
    public const DESCRIPTION_XMP          = 'XMP-dc:Description';
    public const MAKE                     = 'IFD0:Make';
    public const LENS                     = 'ExifIFD:LensModel';
    public const LENS_ID                  = 'Composite:LensID';
    public const SUBJECT                  = 'XMP-dc:Subject';
    public const CONTENTIDENTIFIER        = 'Apple:ContentIdentifier';
    public const MEDIA_GROUP_UUID         = 'Apple:MediaGroupUUID';
    public const MICROVIDEOOFFSET         = 'XMP-GCamera:MicroVideoOffset';
    public const SUBLOCATION              = 'IPTC2:Sublocation';
    public const CITY                     = 'IPTC2:City';
    public const STATE                    = 'IPTC2:Province-State';
    public const COUNTRY                  = 'IPTC2:Country-PrimaryLocationName';

    public const DATETIMEORIGINAL_QUICKTIME  = 'QuickTime:CreateDate';
    public const DATETIMEORIGINAL_AVI        = 'RIFF:DateTimeOriginal';
    public const DATETIMEORIGINAL_WEBM       = 'Matroska:DateTimeOriginal';
    public const DATETIMEORIGINAL_OGG        = 'Theora:CreationTime';
    public const DATETIMEORIGINAL_WMV        = 'ASF:CreationDate';
    public const DATETIMEORIGINAL_APPLE      = 'Keys:CreationDate';
    public const IMAGEHEIGHT_VIDEO           = 'Composite:ImageSize';
    public const IMAGEWIDTH_VIDEO            = 'Composite:ImageSize';
    public const MAKE_QUICKTIME              = 'QuickTime:Make';
    public const MODEL_QUICKTIME             = 'QuickTime:Model';
    public const CONTENTIDENTIFIER_QUICKTIME = 'QuickTime:ContentIdentifier';
    public const CONTENTIDENTIFIER_KEYS      = 'Keys:ContentIdentifier';
    public const GPSLATITUDE_QUICKTIME       = 'Composite:GPSLatitude';
    public const GPSLONGITUDE_QUICKTIME      = 'Composite:GPSLongitude';
    public const GPSALTITUDE_QUICKTIME       = 'Composite:GPSAltitude';
    public const FRAMERATE                   = 'MPEG:FrameRate';
    public const FRAMERATE_QUICKTIME_1       = 'Track1:VideoFrameRate';
    public const FRAMERATE_QUICKTIME_2       = 'Track2:VideoFrameRate';
    public const FRAMERATE_QUICKTIME_3       = 'Track3:VideoFrameRate';
    public const FRAMERATE_AVI               = 'RIFF:VideoFrameRate';
    public const FRAMERATE_OGG               = 'Theora:FrameRate';
    public const DURATION                    = 'Composite:Duration';
    public const DURATION_QUICKTIME          = 'QuickTime:Duration';
    public const DURATION_WEBM               = 'Matroska:Duration';
    public const DURATION_WMV                = 'ASF:SendDuration';
    public const DATETIMEORIGINAL_PNG        = 'PNG:CreationTime';

    /**
     * Maps the ExifTool fields to the fields of
     * the \PHPExif\Exif class
     */
    protected array $map = array(
        self::APERTURE                 => Exif::APERTURE,
        self::ARTIST                   => Exif::AUTHOR,
        self::MODEL                    => Exif::CAMERA,
        self::COLORSPACE               => Exif::COLORSPACE,
        self::COPYRIGHT                => Exif::COPYRIGHT,
        self::DATETIMEORIGINAL         => Exif::CREATION_DATE,
        self::CREDIT                   => Exif::CREDIT,
        self::EXPOSURETIME             => Exif::EXPOSURE,
        self::FILESIZE                 => Exif::FILESIZE,
        self::FILENAME                 => Exif::FILENAME,
        self::FOCALLENGTH              => Exif::FOCAL_LENGTH,
        self::APPROXIMATEFOCUSDISTANCE => Exif::FOCAL_DISTANCE,
        self::HEADLINE                 => Exif::HEADLINE,
        self::IMAGEHEIGHT              => Exif::HEIGHT,
        self::XRESOLUTION              => Exif::HORIZONTAL_RESOLUTION,
        self::ISO                      => Exif::ISO,
        self::JOBTITLE                 => Exif::JOB_TITLE,
        self::KEYWORDS                 => Exif::KEYWORDS,
        self::MIMETYPE                 => Exif::MIMETYPE,
        self::ORIENTATION              => Exif::ORIENTATION,
        self::SOFTWARE                 => Exif::SOFTWARE,
        self::SOURCE                   => Exif::SOURCE,
        self::TITLE                    => Exif::TITLE,
        self::TITLE_XMP                => Exif::TITLE,
        self::YRESOLUTION              => Exif::VERTICAL_RESOLUTION,
        self::IMAGEWIDTH               => Exif::WIDTH,
        self::CAPTIONABSTRACT          => Exif::CAPTION,
        self::GPSLATITUDE              => Exif::LATITUDE,
        self::GPSLONGITUDE             => Exif::LONGITUDE,
        self::GPSALTITUDE              => Exif::ALTITUDE,
        self::MAKE                     => Exif::MAKE,
        self::IMGDIRECTION             => Exif::IMGDIRECTION,
        self::LENS                     => Exif::LENS,
        self::LENS_ID                  => Exif::LENS,
        self::DESCRIPTION              => Exif::DESCRIPTION,
        self::DESCRIPTION_XMP          => Exif::DESCRIPTION,
        self::SUBJECT                  => Exif::KEYWORDS,
        self::CONTENTIDENTIFIER        => Exif::CONTENTIDENTIFIER,
        self::MEDIA_GROUP_UUID         => Exif::CONTENTIDENTIFIER,
        self::DATETIMEORIGINAL_QUICKTIME  => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_AVI        => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_WEBM       => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_OGG        => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_WMV        => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_APPLE      => Exif::CREATION_DATE,
        self::MAKE_QUICKTIME              => Exif::MAKE,
        self::MODEL_QUICKTIME             => Exif::CAMERA,
        self::CONTENTIDENTIFIER_QUICKTIME => Exif::CONTENTIDENTIFIER,
        self::CONTENTIDENTIFIER_KEYS      => Exif::CONTENTIDENTIFIER,
        self::GPSLATITUDE_QUICKTIME       => Exif::LATITUDE,
        self::GPSLONGITUDE_QUICKTIME      => Exif::LONGITUDE,
        self::GPSALTITUDE_QUICKTIME       => Exif::ALTITUDE,
        self::IMAGEHEIGHT_VIDEO           => Exif::HEIGHT,
        self::IMAGEWIDTH_VIDEO            => Exif::WIDTH,
        self::FRAMERATE                   => Exif::FRAMERATE,
        self::FRAMERATE_QUICKTIME_1       => Exif::FRAMERATE,
        self::FRAMERATE_QUICKTIME_2       => Exif::FRAMERATE,
        self::FRAMERATE_QUICKTIME_3       => Exif::FRAMERATE,
        self::FRAMERATE_AVI               => Exif::FRAMERATE,
        self::FRAMERATE_OGG               => Exif::FRAMERATE,
        self::DURATION                    => Exif::DURATION,
        self::DURATION_QUICKTIME          => Exif::DURATION,
        self::DURATION_WEBM               => Exif::DURATION,
        self::DURATION_WMV                => Exif::DURATION,
        self::MICROVIDEOOFFSET            => Exif::MICROVIDEOOFFSET,
        self::SUBLOCATION                 => Exif::SUBLOCATION,
        self::CITY                        => Exif::CITY,
        self::STATE                       => Exif::STATE,
        self::COUNTRY                     => Exif::COUNTRY,
        self::DATETIMEORIGINAL_PNG        => Exif::CREATION_DATE
    );

    protected bool $numeric = true;

    /**
     * Mutator method for the numeric property
     *
     * @param bool $numeric
     * @return \PHPExif\Mapper\Exiftool
     */
    public function setNumeric(bool $numeric): Exiftool
    {
        $this->numeric = $numeric;

        return $this;
    }

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

            // manipulate the value if necessary
            switch ($field) {
                case self::APERTURE:
                    $value = sprintf('f/%01.1f', $value);
                    break;
                case self::APPROXIMATEFOCUSDISTANCE:
                    $value = sprintf('%1$sm', $value);
                    break;
                case self::DATETIMEORIGINAL:
                case self::DATETIMEORIGINAL_PNG:
                case self::DATETIMEORIGINAL_QUICKTIME:
                case self::DATETIMEORIGINAL_AVI:
                case self::DATETIMEORIGINAL_WEBM:
                case self::DATETIMEORIGINAL_OGG:
                case self::DATETIMEORIGINAL_WMV:
                    // DATETIMEORIGINAL_APPLE contains data on timezone
                    // only set value if DATETIMEORIGINAL_APPLE has not been used
                    if (!isset($mappedData[Exif::CREATION_DATE])
                            && preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 0) {
                        try {
                            if (isset($data['ExifIFD:OffsetTimeOriginal'])) {
                                try {
                                    $timezone = new \DateTimeZone($data['ExifIFD:OffsetTimeOriginal']);
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
                    } else {
                        continue 2;
                    }

                    break;
                case self::DATETIMEORIGINAL_APPLE:
                    if (preg_match('/^0000[-:]00[-:]00.00:00:00/', $value) === 1) {
                        continue 2;
                    }
                    try {
                        $value = new DateTime($value);
                    } catch (\Exception $e) {
                        continue 2;
                    }

                    break;
                case self::EXPOSURETIME:
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
                    if (!$this->numeric || strpos($value, ' ') !== false) {
                        $focalLengthParts = explode(' ', $value);
                        $value = reset($focalLengthParts);
                    }
                    break;
                case self::ISO:
                    $value = explode(" ", $value)[0];
                    break;
                case self::GPSLATITUDE_QUICKTIME:
                    $value  = $this->extractGPSCoordinates($value);
                    if ($value === false) {
                        continue 2;
                    }
                    break;
                case self::GPSLATITUDE:
                    $value = $this->extractGPSCoordinates($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $latitudeRef = !array_key_exists('GPS:GPSLatitudeRef', $data)
                        || $data['GPS:GPSLatitudeRef'] === null || $data['GPS:GPSLatitudeRef'] === '' ?
                        'N' : $data['GPS:GPSLatitudeRef'][0];
                    $value *= strtoupper($latitudeRef) === 'S' ? -1 : 1;
                    break;
                case self::GPSLONGITUDE_QUICKTIME:
                    $value  = $this->extractGPSCoordinates($value);
                    if ($value === false) {
                        continue 2;
                    }
                    break;
                case self::GPSLONGITUDE:
                    $value = $this->extractGPSCoordinates($value);
                    if ($value === false) {
                        continue 2;
                    }
                    $longitudeRef = !array_key_exists('GPS:GPSLongitudeRef', $data)
                        || $data['GPS:GPSLongitudeRef'] === null || $data['GPS:GPSLongitudeRef'] === '' ?
                        'E' : $data['GPS:GPSLongitudeRef'][0];
                    $value *= strtoupper($longitudeRef) === 'W' ? -1 : 1;
                    break;
                case self::GPSALTITUDE:
                    $flip = 1;
                    if (array_key_exists('GPS:GPSAltitudeRef', $data)) {
                        $flip = ($data['GPS:GPSAltitudeRef'] === '1') ? -1 : 1;
                    }
                    $value = $flip * (float) $value;
                    break;
                case self::GPSALTITUDE_QUICKTIME:
                    $flip = 1;
                    if (array_key_exists('Composite:GPSAltitudeRef', $data)) {
                        $flip = ($data['Composite:GPSAltitudeRef'] === '1') ? -1 : 1;
                    }
                    $value = $flip * (float) $value;
                    break;
                case self::IMAGEHEIGHT_VIDEO:
                case self::IMAGEWIDTH_VIDEO:
                    preg_match("#^(\d+)[^\d]+(\d+)$#", $value, $matches);
                    $value_split = array_slice($matches, 1);
                    $rotate = false;
                    if (array_key_exists('Composite:Rotation', $data)) {
                        if ($data['Composite:Rotation'] === '90' || $data['Composite:Rotation'] === '270') {
                            $rotate = true;
                        }
                    }
                    if (!array_key_exists(Exif::WIDTH, $mappedData)) {
                        if (!($rotate)) {
                            $mappedData[Exif::WIDTH]  = intval($value_split[0]);
                        } else {
                            $mappedData[Exif::WIDTH]  = intval($value_split[1]);
                        }
                    }
                    if (!array_key_exists(Exif::HEIGHT, $mappedData)) {
                        if (!($rotate)) {
                            $mappedData[Exif::HEIGHT] = intval($value_split[1]);
                        } else {
                            $mappedData[Exif::HEIGHT] = intval($value_split[0]);
                        }
                    }
                    continue 2;
                case self::IMGDIRECTION:
                    // Skip cases if image direction is not numeric
                    if (!(is_numeric($value))) {
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
                case self::LENS_ID:
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
    protected function extractGPSCoordinates(string $coordinates): float|false
    {
        if (is_numeric($coordinates) === true || $this->numeric === true) {
            return ((float) $coordinates);
        } else {
            if (preg_match('!^([0-9.]+) deg ([0-9.]+)\' ([0-9.]+)"!', $coordinates, $matches) === 0) {
                return false;
            }

            return round(
                floatval($matches[1]) + (floatval($matches[2]) / 60) + (floatval($matches[3]) / 3600),
                self::ROUNDING_PRECISION
            );
        }
    }
}
