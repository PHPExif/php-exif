<?php

namespace PHPExif\Adapter;

use PHPExif\Exif;
use PHPExif\Mapper\Native as MapperNative;
use Safe\Exceptions\ImageException;

use function Safe\mime_content_type;
use function Safe\filesize;
use function Safe\getimagesize;
use function Safe\iptcparse;

/**
 * PHP Exif Native Reader Adapter
 *
 * Uses native PHP functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class Native extends AbstractAdapter
{
    public const INCLUDE_THUMBNAIL = true;
    public const NO_THUMBNAIL      = false;

    public const SECTIONS_AS_ARRAYS    = true;
    public const SECTIONS_FLAT         = false;

    public const SECTION_FILE      = 'FILE';
    public const SECTION_COMPUTED  = 'COMPUTED';
    public const SECTION_IFD0      = 'IFD0';
    public const SECTION_THUMBNAIL = 'THUMBNAIL';
    public const SECTION_COMMENT   = 'COMMENT';
    public const SECTION_EXIF      = 'EXIF';
    public const SECTION_ALL       = 'ANY_TAG';
    public const SECTION_IPTC      = 'IPTC';

    /**
     * List of EXIF sections
     *
     * @var array
     */
    protected array $requiredSections = array();

    /**
     * Include the thumbnail in the EXIF data?
     */
    protected bool $includeThumbnail = self::NO_THUMBNAIL;

    /**
     * Parse the sections as arrays?
     */
    protected bool $sectionsAsArrays = self::SECTIONS_FLAT;

    protected string $mapperClass = MapperNative::class;

    /**
     * Contains the mapping of names to IPTC field numbers
     */
    protected array $iptcMapping = array(
        'title'       => '2#005',
        'keywords'    => '2#025',
        'copyright'   => '2#116',
        'caption'     => '2#120',
        'headline'    => '2#105',
        'credit'      => '2#110',
        'source'      => '2#115',
        'jobtitle'    => '2#085',
        'city'        => '2#090',
        'sublocation' => '2#092',
        'state'       => '2#095',
        'country'     => '2#101'
    );


    /**
     * Getter for the EXIF sections
     *
     * @return array
     */
    public function getRequiredSections(): array
    {
        return $this->requiredSections;
    }

    /**
     * Setter for the EXIF sections
     *
     * @param array $sections List of EXIF sections
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function setRequiredSections(array $sections): Native
    {
        $this->requiredSections = $sections;

        return $this;
    }

    /**
     * Adds an EXIF section to the list
     *
     * @param string $section
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function addRequiredSection(string $section): Native
    {
        if (!in_array($section, $this->requiredSections, true)) {
            array_push($this->requiredSections, $section);
        }

        return $this;
    }

    /**
     * Define if the thumbnail should be included into the EXIF data or not
     *
     * @param boolean $value
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function setIncludeThumbnail(bool $value): Native
    {
        $this->includeThumbnail = $value;

        return $this;
    }

    /**
     * Returns if the thumbnail should be included into the EXIF data or not
     *
     * @return boolean
     */
    public function getIncludeThumbnail(): bool
    {
        return $this->includeThumbnail;
    }

    /**
     * Define if the sections should be parsed as arrays
     *
     * @param boolean $value
     * @return \PHPExif\Adapter\Native Current instance for chaining
     */
    public function setSectionsAsArrays(bool $value): Native
    {
        $this->sectionsAsArrays = $value;

        return $this;
    }

    /**
     * Returns if the sections should be parsed as arrays
     *
     * @return boolean
     */
    public function getSectionsAsArrays(): bool
    {
        return $this->sectionsAsArrays;
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function getExifFromFile(string $file): Exif
    {
        $mimeType = mime_content_type($file);

        if ($mimeType === 'image/x-tga') {
            // @codeCoverageIgnoreStart
            $mimeType = 'video/mpeg';
            // @codeCoverageIgnoreEnd
        }

        if ($mimeType === 'application/octet-stream' &&
            in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['mp4', 'mp4v', 'mpg4'], true)) {
            // @codeCoverageIgnoreStart
            $mimeType = 'video/mp4';
            // @codeCoverageIgnoreEnd
        }

        // Photo
        $sections   = $this->getRequiredSections();
        $sections   = implode(',', $sections);
        $sections   = $sections === '' ? null : $sections;

        // exif_read_data raises E_WARNING/E_NOTICE errors for unsupported
        // tags, which could result in exceptions being thrown, even though
        // the function would otherwise succeed to return valid tags.
        // We explicitly disable this undesirable behavior.
        // @phpstan-ignore-next-line
        $data = @exif_read_data(
            $file,
            $sections,
            $this->getSectionsAsArrays(),
            $this->getIncludeThumbnail()
        );

        // exif_read_data failed to read exif data (i.e. not a jpg/tiff)
        if (false === $data) {
            $data = array();
            $data['FileSize'] = filesize($file);
            $data['FileName'] = basename($file);
            $data['MimeType'] = $mimeType;
        } else {
            $xmpData = $this->getIptcData($file);
            $data = array_merge($data, array(self::SECTION_IPTC => $xmpData));
        }

        if (!(array_key_exists('height', $data)) || !(array_key_exists('width', $data))) {
            try {
                $img_size = getimagesize($file);
                if ($img_size[0] !== null && $img_size[1] !== null) {
                    $data['width'] = $img_size[0];
                    $data['height'] = $img_size[1];
                }
            } catch (ImageException) {
                // Fail silently
            }
        }

        // Force UTF8 encoding
        /** @var array $data */
        $data = $this->convertToUTF8($data);

        // map the data:
        $mapper = $this->getMapper();
        $mappedData = $mapper->mapRawData($data);

        // hydrate a new Exif object
        $exif = new Exif();
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($exif, $mappedData);
        $exif->setRawData($data);

        return $exif;
    }


    /**
     * Returns an array of IPTC data
     *
     * @param string $file The file to read the IPTC data from
     * @return array
     */
    public function getIptcData(string $file): array
    {
        getimagesize($file, $info);
        $arrData = array();
        if (isset($info['APP13'])) {
            try {
                $iptc = iptcparse($info['APP13']);
            } catch (ImageException) {
                return $arrData;
            }

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
}
