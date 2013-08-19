<?php
/**
 * PHP Exif Native Reader Adapter
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Reader
 */

namespace PHPExif\Reader\Adapter;

use PHPExif\Reader\AdapterAbstract;
use PHPExif\Exif;
use \DateTime;

/**
 * PHP Exif Native Reader Adapter
 *
 * Uses native PHP functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class Native extends AdapterAbstract
{
    const INCLUDE_THUMBNAIL = true;
    const NO_THUMBNAIL      = false;

    const SECTIONS_AS_ARRAYS    = true;
    const SECTIONS_FLAT         = false;

    const SECTION_FILE      = 'FILE';
    const SECTION_COMPUTED  = 'COMPUTED';
    const SECTION_IFD0      = 'IFD0';
    const SECTION_THUMBNAIL = 'THUMBNAIL';
    const SECTION_COMMENT   = 'COMMENT';
    const SECTION_EXIF      = 'EXIF';
    const SECTION_ALL       = 'ANY_TAG';
    const SECTION_IPTC      = 'IPTC';

    /**
     * List of EXIF sections
     *
     * @var array
     */
    protected $requiredSections = array();

    /**
     * Include the thumbnail in the EXIF data?
     *
     * @var boolean
     */
    protected $includeThumbnail = self::NO_THUMBNAIL;

    /**
     * Parse the sections as arrays?
     *
     * @var boolean
     */
    protected $sectionsAsArrays = self::SECTIONS_FLAT;

    /**
     * Contains the mapping of names to IPTC field numbers
     *
     * @var array
     */
    protected $iptcMapping  = array(
        'title'     => '2#005',
        'keywords'  => '2#025',
        'copyright' => '2#116',
        'caption'   => '2#120',
        'headline'  => '2#105',
        'credit'    => '2#110',
        'source'    => '2#115',
        'jobtitle'  => '2#085'
    );


    /**
     * Getter for the EXIF sections
     *
     * @return array
     */
    public function getRequiredSections()
    {
        return $this->requiredSections;
    }

    /**
     * Setter for the EXIF sections
     *
     * @param array $sections List of EXIF sections
     * @return \PHPExif\Reader Current instance for chaining
     */
    public function setRequiredSections(array $sections)
    {
        $this->requiredSections = $sections;

        return $this;
    }

    /**
     * Adds an EXIF section to the list
     *
     * @param string $section
     * @return \PHPExif\Reader Current instance for chaining
     */
    public function addRequiredSection($section)
    {
        if (!in_array($section, $this->requiredSections)) {
            array_push($this->requiredSections, $section);
        }

        return $this;
    }

    /**
     * Define if the thumbnail should be included into the EXIF data or not
     *
     * @param boolean $value
     * @return \PHPExif\Reader Current instance for chaining
     */
    public function setIncludeThumbnail($value)
    {
        $this->includeThumbnail = $value;

        return $this;
    }

    /**
     * Returns if the thumbnail should be included into the EXIF data or not
     *
     * @return boolean
     */
    public function getIncludeThumbnail()
    {
        return $this->includeThumbnail;
    }

    /**
     * Define if the sections should be parsed as arrays
     *
     * @param boolean $value
     * @return \PHPExif\Reader Current instance for chaining
     */
    public function setSectionsAsArrays($value)
    {
        $this->sectionsAsArrays = (bool) $value;

        return $this;
    }

    /**
     * Returns if the sections should be parsed as arrays
     *
     * @return boolean
     */
    public function getSectionsAsArrays()
    {
        return $this->sectionsAsArrays;
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     * @throws \RuntimeException If the EXIF data could not be read
     */
    public function getExifFromFile($file)
    {
        $sections   = $this->getRequiredSections();
        $sections   = implode(',', $sections);
        $sections   = (empty($sections)) ? null : $sections;

        $data = @exif_read_data(
            $file,
            $sections,
            $this->getSectionsAsArrays(),
            $this->getIncludeThumbnail()
        );

        if (false === $data) {
            throw new \RuntimeException(
                sprintf('Could not read EXIF data from file %1$s', $file)
            );
        }

        $xmpData = $this->getIptcData($file);
        $data = array_merge($data, array(self::SECTION_IPTC => $xmpData));
        $mappedData = $this->mapData($data);
        $exif = new Exif($mappedData);

        return $exif;
    }

    /**
     * Returns an array of IPTC data
     *
     * @param string $file The file to read the IPTC data from
     * @return array
     */
    public function getIptcData($file)
    {
        $size = getimagesize($file, $info);
        $arrData = array();
        if (isset($info['APP13'])) {
            $iptc = iptcparse($info['APP13']);

            foreach ($this->iptcMapping as $name => $field) {
                if (!isset($iptc[$field])) {
                    continue;
                }

                if (count($iptc[$field]) === 1) {
                    $arrData[$name] = reset($iptc[$field]);
                } else {
                    $arrData[$name] = $iptc[$field];
                }
            }
        }

        return $arrData;
    }

    /**
     * Maps native data to Exif format
     *
     * @param array $source
     * @return array
     */
    public function mapData(array $source)
    {
        $focalLength = false;
        if (isset($source['FocalLength'])) {
            $parts  = explode('/', $source['FocalLength']);
            $focalLength = (int)reset($parts) / (int)end($parts);
        }

        $horResolution = false;
        if (isset($source['XResolution'])) {
            $resolutionParts = explode('/', $source['XResolution']);
            $horResolution = (int)reset($resolutionParts);
        }

        $vertResolution = false;
        if (isset($source['YResolution'])) {
            $resolutionParts = explode('/', $source['YResolution']);
            $vertResolution = (int)reset($resolutionParts);
        }

        $creationDate = false;
        if (isset($source['DateTimeOriginal'])) {
            $creationDate = DateTime::createFromFormat(
                'Y:m:d H:i:s',
                $source['DateTimeOriginal']
            );
        }

        $mappedData = array(
            Exif::APERTURE              => false,
            Exif::AUTHOR                => false,
            Exif::CAMERA                => false,
            Exif::CAPTION               => false,
            Exif::COPYRIGHT             => false,
            Exif::CREATION_DATE         => $creationDate,
            Exif::CREDIT                => false,
            Exif::EXPOSURE              => false,
            Exif::FOCAL_LENGTH          => $focalLength,
            Exif::FOCAL_DISTANCE        => false,
            Exif::HEADLINE              => false,
            Exif::HEIGHT                => false,
            Exif::HORIZONTAL_RESOLUTION => $horResolution,
            Exif::ISO                   => false,
            Exif::JOB_TITLE             => false,
            Exif::KEYWORDS              => false,
            Exif::SOFTWARE              => false,
            Exif::SOURCE                => false,
            Exif::TITLE                 => false,
            Exif::VERTICAL_RESOLUTION   => $vertResolution,
            Exif::WIDTH                 => false,
        );

        $arrMapping = array(
            array(
                Exif::AUTHOR => 'Artist',
                Exif::CAMERA => 'Model',
                Exif::EXPOSURE => 'ExposureTime',
                Exif::ISO => 'ISOSpeedRatings',
                Exif::SOFTWARE => 'Software',
            ),
            self::SECTION_COMPUTED => array(
                Exif::APERTURE => 'ApertureFNumber',
                Exif::FOCAL_DISTANCE => 'FocusDistance',
                Exif::HEIGHT => 'Height',
                Exif::WIDTH => 'Width',
            ),
            self::SECTION_IPTC => array(
                Exif::CAPTION => 'caption',
                Exif::COPYRIGHT => 'copyright',
                Exif::CREDIT => 'credit',
                Exif::HEADLINE => 'headline',
                Exif::JOB_TITLE => 'jobtitle',
                Exif::KEYWORDS => 'keywords',
                Exif::SOURCE => 'source',
                Exif::TITLE => 'title',
            ),
        );

        foreach ($arrMapping as $key => $arrFields) {
            if (array_key_exists($key, $source)) {
                $arrSource = $source[$key];
            } else {
                $arrSource = $source;
            }

            foreach ($arrFields as $mappedField => $field) {
                if (isset($arrSource[$field])) {
                    $mappedData[$mappedField] = $arrSource[$field];
                }
            }
        }

        return $mappedData;
    }
}
