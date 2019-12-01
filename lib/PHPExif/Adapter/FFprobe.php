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

namespace PHPExif\Adapter;

use PHPExif\Exif;
use FFMpeg;

/**
 * PHP Exif Native Reader Adapter
 *
 * Uses native PHP functionality to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class FFprobe extends AdapterAbstract
{

    /**
     * @var string
     */
    protected $mapperClass = '\\PHPExif\\Mapper\\FFprobe';


    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif|boolean Instance of Exif object with data
     */
    public function getExifFromFile($file)
    {
        $mimeType = mime_content_type($file);

        // file is not a video -> wrong adapter
        if (strpos($mimeType, 'video') !== 0) {
            return false;
        }


        $ffprobe = FFMpeg\FFProbe::create();

        $stream = $ffprobe->streams($file)->videos()->first()->all();
        $format = $ffprobe->format($file)->all();

        $data = array_merge($stream, $format, array('MimeType' => $mimeType, 'filesize' => filesize($file)));


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
