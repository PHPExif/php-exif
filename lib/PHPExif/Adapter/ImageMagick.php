<?php

namespace PHPExif\Adapter;

use PHPExif\Exif;
use Imagick;
use PHPExif\Mapper\ImageMagick as MapperImageMagick;
use Safe\Exceptions\ImageException;

use function Safe\filesize;
use function Safe\iptcparse;
use function Safe\mime_content_type;

/**
 * PHP Imagick Reader Adapter
 *
 * Uses Imagick functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class ImageMagick extends AbstractAdapter
{
    public const TOOL_NAME = 'imagick';

    protected string $mapperClass = MapperImageMagick::class;

    /**
     * Contains the mapping of names to IPTC field numbers
     */
    protected array $iptcMapping = array(
        'iptc:title'       => '2#005',
        'iptc:keywords'    => '2#025',
        'iptc:copyright'   => '2#116',
        'iptc:caption'     => '2#120',
        'iptc:headline'    => '2#105',
        'iptc:credit'      => '2#110',
        'iptc:source'      => '2#115',
        'iptc:jobtitle'    => '2#085',
        'iptc:city'        => '2#090',
        'iptc:sublocation' => '2#092',
        'iptc:state'       => '2#095',
        'iptc:country'     => '2#101'
    );

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function getExifFromFile(string $file): Exif
    {
        /* Create the object */
        $im = new Imagick($file);

        /* Get the EXIF information */
        $data_exif = $im->getImageProperties("*");
        $data_filename = basename($file);
        $data_filesize = filesize($file);
        $mimeType = mime_content_type($file);
        $data_width = $im->getImageWidth();
        $data_height = $im->getImageHeight();
        $additional_data = [
            'MimeType' => $mimeType,
            'filesize' => $data_filesize,
            'filename' => $data_filename,
            'width' => $data_width,
            'height' => $data_height
        ];
        $profiles = $im->getImageProfiles('iptc');
        $data_iptc = [];
        if (array_key_exists('iptc', $profiles)) {
            $data_iptc = $this->getIptcData($profiles['iptc']);
        }

        $data = array_merge($data_exif, $data_iptc, $additional_data);
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
     * @param string $profile Raw IPTC data
     * @return array
     */
    public function getIptcData(string $profile): array
    {
        $arrData = [];
        try {
            $iptc = iptcparse($profile);
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

        return $arrData;
    }
}
