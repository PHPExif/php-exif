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
use DateTime;

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
    const APERTURE                 = 'Composite:Aperture';
    const APPROXIMATEFOCUSDISTANCE = 'XMP-aux:ApproximateFocusDistance';
    const ARTIST                   = 'IFD0:Artist';
    const CAPTION                  = 'XMP-acdsee';
    const CAPTIONABSTRACT          = 'IPTC:Caption-Abstract';
    const COLORSPACE               = 'ExifIFD:ColorSpace';
    const COPYRIGHT                = 'IFD0:Copyright';
    const DATETIMEORIGINAL         = 'ExifIFD:DateTimeOriginal';
    const CREDIT                   = 'IPTC:Credit';
    const EXPOSURETIME             = 'ExifIFD:ExposureTime';
    const FILESIZE                 = 'System:FileSize';
    const FILENAME                 = 'System:FileName';
    const FOCALLENGTH              = 'ExifIFD:FocalLength';
    const HEADLINE                 = 'IPTC:Headline';
    const IMAGEHEIGHT              = 'File:ImageHeight';
    const IMAGEWIDTH               = 'File:ImageWidth';
    const ISO                      = 'ExifIFD:ISO';
    const JOBTITLE                 = 'IPTC:By-lineTitle';
    const KEYWORDS                 = 'IPTC:Keywords';
    const MIMETYPE                 = 'File:MIMEType';
    const MODEL                    = 'IFD0:Model';
    const ORIENTATION              = 'IFD0:Orientation';
    const SOFTWARE                 = 'IFD0:Software';
    const SOURCE                   = 'IPTC:Source';
    const TITLE                    = 'IPTC:ObjectName';
    const XRESOLUTION              = 'IFD0:XResolution';
    const YRESOLUTION              = 'IFD0:YResolution';
    const GPSLATITUDE              = 'GPS:GPSLatitude';
    const GPSLONGITUDE             = 'GPS:GPSLongitude';
    const GPSALTITUDE              = 'GPS:GPSAltitude';
    const IMGDIRECTION             = 'GPS:GPSImgDirection';
    const DESCRIPTION              = 'ExifIFD:ImageDescription';
    const MAKE                     = 'IFD0:Make';
    const LENS                     = 'ExifIFD:LensModel';
    const SUBJECT                  = 'XMP-dc:Subject';
    const CONTENTIDENTIFIER        = 'Apple:ContentIdentifier';
    const MICROVIDEOOFFSET         = 'XMP-GCamera:MicroVideoOffset';
    const SUBLOCATION              = 'IPTC2:Sublocation';
    const CITY                     = 'IPTC2:City';
    const STATE                    = 'IPTC2:Province-State';
    const COUNTRY                  = 'IPTC2:Country-PrimaryLocationName';

    const DATETIMEORIGINAL_QUICKTIME  = 'QuickTime:CreateDate';
    const DATETIMEORIGINAL_AVI        = 'RIFF:DateTimeOriginal';
    const DATETIMEORIGINAL_WEBM       = 'Matroska:DateTimeOriginal';
    const DATETIMEORIGINAL_OGG        = 'Theora:CreationTime';
    const DATETIMEORIGINAL_WMV        = 'ASF:CreationDate';
    const DATETIMEORIGINAL_APPLE      = 'Keys:CreationDate';
    const IMAGEHEIGHT_VIDEO           = 'Composite:ImageSize';
    const IMAGEWIDTH_VIDEO            = 'Composite:ImageSize';
    const MAKE_QUICKTIME              = 'QuickTime:Make';
    const MODEL_QUICKTIME             = 'QuickTime:Model';
    const CONTENTIDENTIFIER_QUICKTIME = 'QuickTime:ContentIdentifier';
    const GPSLATITUDE_QUICKTIME       = 'Composite:GPSLatitude';
    const GPSLONGITUDE_QUICKTIME      = 'Composite:GPSLongitude';
    const GPSALTITUDE_QUICKTIME       = 'Composite:GPSAltitude';
    const FRAMERATE                   = 'MPEG:FrameRate';
    const FRAMERATE_QUICKTIME_1       = 'Track1:VideoFrameRate';
    const FRAMERATE_QUICKTIME_2       = 'Track2:VideoFrameRate';
    const FRAMERATE_QUICKTIME_3       = 'Track3:VideoFrameRate';
    const FRAMERATE_AVI               = 'RIFF:VideoFrameRate';
    const FRAMERATE_OGG               = 'Theora:FrameRate';
    const DURATION                    = 'Composite:Duration';
    const DURATION_QUICKTIME          = 'QuickTime:Duration';
    const DURATION_WEBM               = 'Matroska:Duration';
    const DURATION_WMV                = 'ASF:SendDuration';
    const DATETIMEORIGINAL_PNG        = 'PNG:CreationTime';

    /**
     * Maps the ExifTool fields to the fields of
     * the \PHPExif\Exif class
     *
     * @var array
     */
    protected $map = array(
        self::APERTURE                 => Exif::APERTURE,
        self::ARTIST                   => Exif::AUTHOR,
        self::MODEL                    => Exif::CAMERA,
        self::CAPTION                  => Exif::CAPTION,
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
        self::YRESOLUTION              => Exif::VERTICAL_RESOLUTION,
        self::IMAGEWIDTH               => Exif::WIDTH,
        self::CAPTIONABSTRACT          => Exif::CAPTION,
        self::GPSLATITUDE              => Exif::LATITUDE,
        self::GPSLONGITUDE             => Exif::LONGITUDE,
        self::GPSALTITUDE              => Exif::ALTITUDE,
        self::MAKE                     => Exif::MAKE,
        self::IMGDIRECTION             => Exif::IMGDIRECTION,
        self::LENS                     => Exif::LENS,
        self::DESCRIPTION              => Exif::DESCRIPTION,
        self::SUBJECT                  => Exif::KEYWORDS,
        self::CONTENTIDENTIFIER        => Exif::CONTENTIDENTIFIER,
        self::DATETIMEORIGINAL_QUICKTIME  => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_AVI        => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_WEBM       => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_OGG        => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_WMV        => Exif::CREATION_DATE,
        self::DATETIMEORIGINAL_APPLE      => Exif::CREATION_DATE,
        self::MAKE_QUICKTIME              => Exif::MAKE,
        self::MODEL_QUICKTIME             => Exif::CAMERA,
        self::CONTENTIDENTIFIER_QUICKTIME => Exif::CONTENTIDENTIFIER,
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

    /**
     * @var bool
     */
    protected $numeric = true;

    /**
     * Mutator method for the numeric property
     *
     * @param bool $numeric
     * @return \PHPExif\Mapper\Exiftool
     */
    public function setNumeric($numeric)
    {
        $this->numeric = (bool) $numeric;

        return $this;
    }

    /**
     * Maps the array of raw source data to the correct
     * fields for the \PHPExif\Exif class
     *
     * @param array $data
     * @return array
     */
    public function mapRawData(array $data)
    {
        $mappedData = array();
        $gpsData = array();
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
                    if (!isset($mappedData[Exif::CREATION_DATE])) {
                        try {
                            if (isset($data['ExifIFD:OffsetTimeOriginal'])) {
                                $timezone = new \DateTimeZone($data['ExifIFD:OffsetTimeOriginal']);
                                $value = new \DateTime($value, $timezone);
                            } else {
                                $value = new \DateTime($value);
                            }
                        } catch (\Exception $e) {
                            continue 2;
                        }
                    } else {
                        continue 2;
                    }

                    break;
                case self::DATETIMEORIGINAL_APPLE:
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
                    break;
                case self::GPSLATITUDE:
                    $latitudeRef = empty($data['GPS:GPSLatitudeRef']) ? 'N' : $data['GPS:GPSLatitudeRef'][0];
                    $value = $this->extractGPSCoordinates($value);
                    if ($value !== false) {
                        $value = (strtoupper($latitudeRef) === 'S' ? -1.0 : 1.0) * $value;
                    } else {
                        $value = false;
                    }

                    break;
                case self::GPSLONGITUDE_QUICKTIME:
                    $value  = $this->extractGPSCoordinates($value);
                    break;
                case self::GPSLONGITUDE:
                    $longitudeRef = empty($data['GPS:GPSLongitudeRef']) ? 'E' : $data['GPS:GPSLongitudeRef'][0];
                    $value  = $this->extractGPSCoordinates($value);
                    if ($value !== false) {
                        $value  = (strtoupper($longitudeRef) === 'W' ? -1 : 1) * $value;
                    }

                    break;
                case self::GPSALTITUDE:
                    $flip = 1;
                    if (!(empty($data['GPS:GPSAltitudeRef']))) {
                        $flip = ($data['GPS:GPSAltitudeRef'] == '1') ? -1 : 1;
                    }
                        $value = $flip * (float) $value;
                    break;
                case self::GPSALTITUDE_QUICKTIME:
                    $flip = 1;
                    if (!(empty($data['Composite:GPSAltitudeRef']))) {
                        $flip = ($data['Composite:GPSAltitudeRef'] == '1') ? -1 : 1;
                    }
                    $value = $flip * (float) $value;
                    break;
                case self::IMAGEHEIGHT_VIDEO:
                case self::IMAGEWIDTH_VIDEO:
                    preg_match("#^(\d+)[^\d]+(\d+)$#", $value, $matches);
                    $value_splitted = array_slice($matches, 1);
                    $rotate = false;
                    if (!(empty($data['Composite:Rotation']))) {
                        if ($data['Composite:Rotation']=='90' || $data['Composite:Rotation']=='270') {
                            $rotate = true;
                        }
                    }
                    if (empty($mappedData[Exif::WIDTH])) {
                        if (!($rotate)) {
                            $mappedData[Exif::WIDTH]  = intval($value_splitted[0]);
                        } else {
                            $mappedData[Exif::WIDTH]  = intval($value_splitted[1]);
                        }
                    }
                    if (empty($mappedData[Exif::HEIGHT])) {
                        if (!($rotate)) {
                            $mappedData[Exif::HEIGHT] = intval($value_splitted[1]);
                        } else {
                            $mappedData[Exif::HEIGHT] = intval($value_splitted[0]);
                        }
                    }
                    continue 2;
                    break;

                // Merge sources of keywords
                case self::KEYWORDS:
                case self::SUBJECT:
                    if (empty($mappedData[Exif::KEYWORDS])) {
                        $mappedData[Exif::KEYWORDS] = $value;
                    } else {
                        // @codeCoverageIgnoreStart
                        $mappedData[Exif::KEYWORDS] = array_unique(array_merge($mappedData[Exif::KEYWORDS], $value));
                        // @codeCoverageIgnoreEnd
                    }

                    continue 2;
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
     * @return array
     */
    protected function extractGPSCoordinates($coordinates)
    {
        if (is_numeric($coordinates) === true || $this->numeric === true) {
            return ((float) $coordinates);
        } else {
            if (!preg_match('!^([0-9.]+) deg ([0-9.]+)\' ([0-9.]+)"!', $coordinates, $matches)) {
                return false;
            }

            return floatval($matches[1]) + (floatval($matches[2]) / 60) + (floatval($matches[3]) / 3600);
        }
    }
}
