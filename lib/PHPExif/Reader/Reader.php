<?php

namespace PHPExif\Reader;

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
 */
class Reader implements ReaderInterface
{
    /**
     * Reader constructor
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(protected readonly AdapterInterface $adapter)
    {
    }

    /**
     * Factory for the reader
     *
     * @param ReaderType $type
     * @return Reader
     */
    public static function factory(ReaderType $type): Reader
    {
        $classname = get_called_class();
        $adapter = match ($type) {
            ReaderType::NATIVE => new NativeAdapter(),
            ReaderType::EXIFTOOL => new ExiftoolAdapter(),
            ReaderType::FFPROBE => new FFProbeAdapter(),
            ReaderType::IMAGICK => new ImageMagickAdapter(),
        };
        return new $classname($adapter);
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return Exif Instance of Exif object with data
     * @throws PhpExifReaderException If the EXIF data could not be read
     */
    public function read(string $file): Exif
    {
        return $this->adapter->getExifFromFile($file);
    }
}
