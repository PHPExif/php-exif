<?php
/**
 * PHP Exif Imagick Reader Adapter
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Reader
 */

namespace PHPExif\Adapter;

use PHPExif\Exif;
use InvalidArgumentException;
use imagick;

/**
 * PHP Imagick Reader Adapter
 *
 * Uses Imagick functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class ImageMagick extends AdapterAbstract
{
    const TOOL_NAME = 'imagick';

    protected string $mapperClass = '\\PHPExif\\Mapper\\ImageMagick';


    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif|boolean Instance of Exif object with data
     */
    public function getExifFromFile(string $file) : Exif
    {
        /* Create the object */
        $im = new Imagick($file);

        /* Get the EXIF information */
        $data_exif = $im->getImageProperties("*");
        $data_filename = basename($file);
        $data_filesize = filesize($file);
        $mimeType = mime_content_type($file);
        $additional_data = array('MimeType' => $mimeType, 'filesize' => $data_filesize, 'filename' => $data_filename);
        $data = array_merge($data_exif, $additional_data);

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
}
