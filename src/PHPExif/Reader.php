<?php
/**
 * Reader: Read EXIF data from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Data;

use PHPExif\Adapter\Native\Reader\Reader as NativeReader;
use PHPExif\Exception\NoAdapterException;
use PHPExif\Exception\UnknownAdapterTypeException;

/**
 * Reader class
 *
 * Responsible for reading EXIF data from a file
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Reader
{
    const TYPE_NATIVE   = 'native';
    const TYPE_EXIFTOOL = 'exiftool';

    /**
     * The current adapter
     *
     * @var \PHPExif\Adapter\ReaderInterface
     */
    private $adapter;

    /**
     * Reader constructor
     *
     * @param \PHPExif\Adapter\ReaderInterface $adapter
     */
    public function __construct(ReaderInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Getter for the reader adapter
     *
     * @return \PHPExif\Adapter\ReaderInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Factory for the reader
     *
     * @param string $type
     * @return Reader
     * @throws UnknownAdapterTypeException When given type is invalid
     */
    public static function factory($type)
    {
        $classname = get_called_class();
        switch ($type) {
            case self::TYPE_NATIVE:
                $adapter = NativeReader::withDefaults();
                break;
                /*
            case self::TYPE_EXIFTOOL:
                $adapter = new ExiftoolAdapter();
                break;
                 */
            default:
                throw UnknownAdapterTypeException::forType($type);
        }
        return new $classname($adapter);
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Data\Exif
     */
    public function read($file)
    {
        return $this->getAdapter()->read($file);
    }
}
