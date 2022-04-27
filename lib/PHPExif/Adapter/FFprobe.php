<?php
/**
 * PHP Exif FFProbe Reader Adapter
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
 * PHP Exif FFProbe Reader Adapter
 *
 * Uses FFProbe to read data from a file
 *
 * @category    PHPExif
 * @package     Reader
 */
class FFprobe extends AdapterAbstract
{
    public const TOOL_NAME = 'ffprobe';

    /**
     * Path to the exiftool binary
     */
    protected string $toolPath = '';
    protected string $mapperClass = '\\PHPExif\\Mapper\\FFprobe';


    /**
     * Setter for the exiftool binary path
     *
     * @param string $path The path to the exiftool binary
     * @return \PHPExif\Adapter\FFprobe Current instance
     * @throws \InvalidArgumentException When path is invalid
     */
    public function setToolPath(string $path): FFprobe
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
    public function getToolPath(): string
    {
        if (empty($this->toolPath)) {
            // Do not use "which": not available on sh
            $path = exec('command -v ' . self::TOOL_NAME);
            // $path = exec('which ' . self::TOOL_NAME);
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
    public function getExifFromFile(string $file): Exif|false
    {
        $mimeType = mime_content_type($file);

        if ($mimeType === 'image/x-tga') {
            // @codeCoverageIgnoreStart
            $mimeType = 'video/mpeg';
            // @codeCoverageIgnoreEnd
        }

        if ($mimeType === 'application/octet-stream' &&
            in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['mp4', 'mp4v', 'mpg4'])) {
            // @codeCoverageIgnoreStart
            $mimeType = 'video/mp4';
            // @codeCoverageIgnoreEnd
        }

        // file is not a video -> wrong adapter
        if (strpos($mimeType, 'video') !== 0) {
            return false;
        }

        $ffprobe = FFMpeg\FFProbe::create(array(
                 'ffprobe.binaries' => $this->getToolPath(),
             ));


        $stream = $ffprobe->streams($file)->videos()->first()->all();
        $format = $ffprobe->format($file)->all();

        $data_filename = basename($file);
        $data_filesize = filesize($file);

        $additional_data = array('MimeType' => $mimeType, 'filesize' => $data_filesize, 'filename' => $data_filename);
        $data = array_replace_recursive($stream, $format, $additional_data);

        // Force UTF8 encoding
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
}
