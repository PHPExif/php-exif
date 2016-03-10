<?php
/**
 * Reader: Read EXIF data from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data;

/**
 * MetadataInterface
 *
 * Public API for accessing the metadata of a file
 *
 * @category    PHPExif
 * @package     Exif
 */
interface MetadataInterface
{
    /**
     * Returns new instance with given EXIF data
     *
     * @param ExifInterface $exif
     *
     * @return MetadataInterface
     */
    public function withExif(ExifInterface $exif);

    /**
     * Returns new instance with given IPTC data
     *
     * @param IptcInterface $iptc
     *
     * @return MetadataInterface
     */
    public function withIptc(IptcInterface $iptc);

    /**
     * Returns the available EXIF data
     *
     * @return ExifInterface
     */
    public function getExif();

    /**
     * Returns the available IPTC data
     *
     * @return IptcInterface
     */
    public function getIptc();
}
