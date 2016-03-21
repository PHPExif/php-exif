<?php
/**
 * Defines interface for EXIF data
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data;

/**
 * ExifInterface
 *
 * Public API for EXIF data
 *
 * @category    PHPExif
 * @package     Exif
 */
interface ExifInterface
{
    const APERTURE              = 'aperture';
    const AUTHOR                = 'author';
    const CAMERA                = 'camera';
    const CAPTION               = 'caption';
    const COLORSPACE            = 'ColorSpace';
    const COPYRIGHT             = 'copyright';
    const CREATION_DATE         = 'creationdate';
    const CREDIT                = 'credit';
    const EXPOSURE              = 'exposure';
    const FILESIZE              = 'FileSize';
    const FOCAL_LENGTH          = 'focalLength';
    const FOCAL_DISTANCE        = 'focalDistance';
    const HEADLINE              = 'headline';
    const HEIGHT                = 'height';
    const HORIZONTAL_RESOLUTION = 'horizontalResolution';
    const ISO                   = 'iso';
    const JOB_TITLE             = 'jobTitle';
    const KEYWORDS              = 'keywords';
    const MIMETYPE              = 'MimeType';
    const ORIENTATION           = 'Orientation';
    const SOFTWARE              = 'software';
    const SOURCE                = 'source';
    const TITLE                 = 'title';
    const VERTICAL_RESOLUTION   = 'verticalResolution';
    const WIDTH                 = 'width';
    const GPS                   = 'gps';

    /**
     * Accessor for the aperture
     *
     * @return string
     */
    public function getAperture();

    /**
     * Returns new instance with updated aperture
     *
     * @param string $aperture
     *
     * @return ExifInterface
     */
    public function withAperture($aperture);

    /**
     * Array represenation of current instance
     *
     * @param boolean $withEmpty
     *
     * @return array
     */
    public function toArray($withEmpty = true);
}
