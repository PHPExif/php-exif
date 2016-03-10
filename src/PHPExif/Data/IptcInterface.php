<?php
/**
 * Defines interface for IPTC data
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data;

/**
 * IptcInterface
 *
 * Public API for IPTC data
 *
 * @category    PHPExif
 * @package     Exif
 */
interface IptcInterface
{
    /**
     * Array represenation of current instance
     *
     * @param boolean $withEmpty
     *
     * @return array
     */
    public function toArray($withEmpty = true);

    /**
     * Accessor for the caption
     *
     * @return string
     */
    public function getCaption();

    /**
     * Returns new instance with updated caption
     *
     * @param string $caption
     *
     * @return IptcInterface
     */
    public function withCaption($caption);

    /**
     * Accessor for the copyright
     *
     * @return string
     */
    public function getCopyright();

    /**
     * Returns new instance with updated copyright
     *
     * @param string $copyright
     *
     * @return IptcInterface
     */
    public function withCopyright($copyright);

    /**
     * Accessor for the credit
     *
     * @return string
     */
    public function getCredit();

    /**
     * Returns new instance with updated credit
     *
     * @param string $credit
     *
     * @return IptcInterface
     */
    public function withCredit($credit);

    /**
     * Accessor for the headline
     *
     * @return string
     */
    public function getHeadline();

    /**
     * Returns new instance with updated headline
     *
     * @param string $headline
     *
     * @return IptcInterface
     */
    public function withHeadline($headline);

    /**
     * Accessor for the jobtitle
     *
     * @return string
     */
    public function getJobtitle();

    /**
     * Returns new instance with updated jobtitle
     *
     * @param string $jobtitle
     *
     * @return IptcInterface
     */
    public function withJobtitle($jobitle);

    /**
     * Accessor for the keywords
     *
     * @return array
     */
    public function getKeywords();

    /**
     * Returns new instance with updated keywords
     *
     * @param string $keywords
     *
     * @return IptcInterface
     */
    public function withKeywords(array $keywords);

    /**
     * Accessor for the source
     *
     * @return string
     */
    public function getSource();

    /**
     * Returns new instance with updated source
     *
     * @param string $source
     *
     * @return IptcInterface
     */
    public function withSource($source);

    /**
     * Accessor for the title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns new instance with updated title
     *
     * @param string $title
     *
     * @return IptcInterface
     */
    public function withTitle($title);
}
