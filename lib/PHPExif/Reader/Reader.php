<?php
/**
 * PHP Exif Reader: Reads EXIF metadata from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Reader
 */

namespace PHPExif\Reader;

use PHPExif\Adapter\AdapterInterface;
use PHPExif\Adapter\NoAdapterException;
use PHPExif\Adapter\Exiftool as ExiftoolAdapter;
use PHPExif\Adapter\FFprobe as FFprobeAdapter;
use PHPExif\Adapter\ImageMagick as ImageMagickAdapter;
use PHPExif\Adapter\Native as NativeAdapter;
use PHPExif\Exif;

/**
 * PHP Exif Reader
 *
 * Responsible for all the read operations on a file's EXIF metadata
 *
 * @category    PHPExif
 * @package     Reader
 * @
 */
class Reader implements ReaderInterface
{
    const TYPE_NATIVE   = 'native';
    const TYPE_EXIFTOOL = 'exiftool';
    const TYPE_FFPROBE  = 'ffprobe';
    const TYPE_IMAGICK  = 'imagick';

    /**
     * The current adapter
     */
    protected ?AdapterInterface $adapter;

    /**
     * Reader constructor
     *
     * @param \PHPExif\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Getter for the reader adapter
     *
     * @return \PHPExif\Adapter\AdapterInterface
     * @throws NoAdapterException When no adapter is set
     */
    public function getAdapter() : AdapterInterface
    {
        if (empty($this->adapter)) {
            throw new NoAdapterException('No adapter set in the reader');
        }

        return $this->adapter;
    }

    /**
     * Factory for the reader
     *
     * @param string $type
     * @return $this
     * @throws \InvalidArgumentException When given type is invalid
     */
    public static function factory(string $type) : Reader
    {
        $classname = get_called_class();
        switch ($type) {
            case self::TYPE_NATIVE:
                $adapter = new NativeAdapter();
                break;
            case self::TYPE_EXIFTOOL:
                $adapter = new ExiftoolAdapter();
                break;
            case self::TYPE_FFPROBE:
                $adapter = new FFProbeAdapter();
                break;
            case self::TYPE_IMAGICK:
                $adapter = new ImageMagickAdapter();
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Unknown type "%1$s"', $type)
                );
        }
        return new $classname($adapter);
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function read(string $file) : Exif|string|false
    {
        return $this->getAdapter()->getExifFromFile($file);
    }

    /**
     * alias to read method
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function getExifFromFile(string $file) : Exif|string|false
    {
        return $this->read($file);
    }
}
