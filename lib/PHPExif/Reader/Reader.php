<?php

namespace PHPExif\Reader;

use PHPExif\Adapter\NoAdapterException;
use PHPExif\Adapter\Exiftool as ExiftoolAdapter;
use PHPExif\Adapter\FFprobe as FFprobeAdapter;
use PHPExif\Adapter\ImageMagick as ImageMagickAdapter;
use PHPExif\Adapter\Native as NativeAdapter;
use PHPExif\Contracts\AdapterInterface;
use PHPExif\Contracts\ReaderInterface;
use PHPExif\Exif;
use PHPExif\Enum\ReaderType;

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
    /**
     * The current adapter
     */
    protected ?AdapterInterface $adapter = null;

    /**
     * Reader constructor
     *
     * @param \PHPExif\Contracts\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Getter for the reader adapter
     *
     * @return \PHPExif\Contracts\AdapterInterface
     * @throws NoAdapterException When no adapter is set
     */
    public function getAdapter(): AdapterInterface
    {
        if ($this->adapter === null) {
            throw new NoAdapterException('No adapter set in the reader');
        }

        return $this->adapter;
    }

    /**
     * Factory for the reader
     *
     * @param ReaderType $type
     * @return Reader
     * @throws \InvalidArgumentException When given type is invalid
     */
    public static function factory(ReaderType $type): Reader
    {
        $classname = get_called_class();
        $adapter = match ($type) {
            ReaderType::NATIVE => new NativeAdapter(),
            ReaderType::EXIFTOOL => new ExiftoolAdapter(),
            ReaderType::FFPROBE => new FFProbeAdapter(),
            ReaderType::IMAGICK => new ImageMagickAdapter(),
            default => throw new \InvalidArgumentException(sprintf('Unknown type "%1$s"', $type->value))
        };
        return new $classname($adapter);
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function read(string $file): Exif|string|false
    {
        return $this->getAdapter()->getExifFromFile($file);
    }

    /**
     * alias to read method
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function getExifFromFile(string $file): Exif|string|false
    {
        return $this->read($file);
    }
}
