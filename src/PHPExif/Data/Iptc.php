<?php
/**
 * Iptc: A container class for IPTC data
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data;

/**
 * Iptc class
 *
 * Container for IPTC data
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Iptc implements IptcInterface
{
    /**
     * @var string
     */
    private $caption;

    /**
     * @var string
     */
    private $copyright;

    /**
     * @var string
     */
    private $credit;

    /**
     * @var string
     */
    private $headline;

    /**
     * @var string
     */
    private $jobtitle;

    /**
     * @var array
     */
    private $keywords = array();

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $title;

    /**
     * Contains the mapping of names to IPTC field numbers
     *
     * @var array
     */
    public static $iptcMapping = array(
        'caption'   => '2#120',
        'copyright' => '2#116',
        'credit'    => '2#110',
        'headline'  => '2#105',
        'jobtitle'  => '2#085',
        'keywords'  => '2#025',
        'source'    => '2#115',
        'title'     => '2#005',
    );

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, self::$iptcMapping)) {
                continue;
            }

            $this->$key = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function toArray($withEmpty = true)
    {
        $data = array();
        $keys = array_keys(self::$iptcMapping);
        foreach ($keys as $prop) {
            $accessor = 'get' . ucfirst($prop);
            $value = $this->$accessor();

            if (empty($value) && !$withEmpty) {
                continue;
            }

            $data[$prop] = $value;
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * {@inheritDoc}
     */
    public function withCaption($caption)
    {
        $new = clone $this;
        $new->caption = $caption;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * {@inheritDoc}
     */
    public function withCopyright($copyright)
    {
        $new = clone $this;
        $new->copyright = $copyright;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * {@inheritDoc}
     */
    public function withCredit($credit)
    {
        $new = clone $this;
        $new->credit = $credit;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * {@inheritDoc}
     */
    public function withHeadline($headline)
    {
        $new = clone $this;
        $new->headline = $headline;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getJobtitle()
    {
        return $this->jobtitle;
    }

    /**
     * {@inheritDoc}
     */
    public function withJobtitle($jobitle)
    {
        $new = clone $this;
        $new->jobtitle = $jobtitle;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * {@inheritDoc}
     */
    public function withKeywords(array $keywords)
    {
        $new = clone $this;
        $new->keywords = $keywords;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritDoc}
     */
    public function withSource($source)
    {
        $new = clone $this;
        $new->source = $source;

        return $new;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function withTitle($title)
    {
        $new = clone $this;
        $new->title = $title;

        return $new;
    }
}
