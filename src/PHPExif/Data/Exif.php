<?php
/**
 * Exif: A container class for EXIF data
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data;

/**
 * Exif class
 *
 * Container for EXIF data
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Exif implements ExifInterface
{
    /**
     * @var string
     */
    private $aperture;

    /**
     * {@inheritDoc}
     */
    public function getAperture()
    {
        return $this->aperture;
    }

    /**
     * {@inheritDoc}
     */
    public function withAperture($aperture)
    {
        $new = clone $this;
        $new->aperture = $aperture;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray($withEmpty = true)
    {
        $data = get_class_vars(
            self::class
        );

        return $data;
    }
}
