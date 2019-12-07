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
use InvalidArgumentException;
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

    const TOOL_NAME = 'ffprobe';

    /**
     * Path to the exiftool binary
     *
     * @var string
     */
    protected $toolPath;

    /**
     * @var string
     */
    protected $mapperClass = '\\PHPExif\\Mapper\\FFprobe';


    /**
     * Setter for the exiftool binary path
     *
     * @param string $path The path to the exiftool binary
     * @return \PHPExif\Adapter\FFprobe Current instance
     * @throws \InvalidArgumentException When path is invalid
     */
    public function setToolPath($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Given path (%1$s) to the ffprobe binary is invalid',
                    $path
                )
            );
        }

        $this->toolPath = $path;

        return $this;
    }



    /**
     * Getter for the ffprobe binary path
     * Lazy loads the "default" path
     *
     * @return string
     */
    public function getToolPath()
    {
        if (empty($this->toolPath)) {
            $path = exec('which ' . self::TOOL_NAME);
            $this->setToolPath($path);
        }

        return $this->toolPath;
    }

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

        $ffprobe = FFMpeg\FFProbe::create(array(
                 'ffprobe.binaries' => $this->getToolPath(),
             ));


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
