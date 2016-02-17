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

namespace PHPExif;

use PHPExif\Adapter\Native\Reader\Reader as NativeReader;
use PHPExif\Adapter\ReaderInterface as AdapterReaderInterface;
use PHPExif\Exception\UnknownAdapterTypeException;

/**
 * Reader class
 *
 * Responsible for reading EXIF data from a file
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Reader implements ReaderInterface
{
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
    public function __construct(AdapterReaderInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritDoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritDoc}
     */
    public static function factory($type)
    {
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

        $classname = get_called_class();

        return new $classname($adapter);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFromFile($file)
    {
        return $this->getAdapter()->getMetadataFromFile($file);
    }
}
