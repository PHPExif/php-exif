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
 * Metadata class
 *
 * Container for Metadata of an image
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Metadata implements MetadataInterface
{
    /**
     * @var ExifInterface
     */
    private $exif;

    /**
     * @var IptcInterface
     */
    private $iptc;

    /**
     * {@inheritDoc}
     */
    public function withExif(ExifInterface $exif)
    {
        $new = clone $this;
        $new->exif = $exif;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function withIptc(IptcInterface $iptc)
    {
        $new = clone $this;
        $new->iptc = $iptc;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getExif()
    {
        if (null === $this->exif) {
            $this->exif = new Exif(array());
        }

        return $this->exif;
    }

    /**
     * {@inheritDoc}
     */
    public function getIptc()
    {
        if (null === $this->iptc) {
            $this->iptc = new Iptc(array());
        }

        return $this->iptc;
    }
}
