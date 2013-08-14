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

namespace PHPExif;

use PHPExif\Reader\AdapterInterface;
use PHPExif\Reader\NoAdapterException;

/**
 * PHP Exif Reader
 *
 * Responsible for all the read operations on a file's EXIF metadata
 *
 * @category    PHPExif
 * @package     Reader
 * @
 */
class Reader
{
    const TYPE_NATIVE   = 'native';
    const TYPE_EXIFTOOL = 'exiftool';

    /**
     * The current adapter
     *
     * @var \PHPExif\Reader\AdapterInterface
     */
    protected $adapter;

    /**
     * Reader constructor
     *
     * @param \PHPExif\Reader\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if (!is_null($adapter)) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * Setter for the reader adapter
     *
     * @param \PHPExif\Reader\AdapterInterface $adapter
     * @return \PHPExif\Reader Current instance for chaining
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Getter for the reader adapter
     *
     * @return \PHPExif\Reader\AdapterInterface
     * @throws NoAdapterException When no adapter is set
     */
    public function getAdapter()
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
     * @return \PHPExif\Reader
     * @throws \InvalidArgumentException When given type is invalid
     */
    public static function factory($type)
    {
        $classname = get_called_class();

        $adapter = null;
        switch ($type) {
            case self::TYPE_NATIVE:
                $adapter = new Reader\Adapter\Native();
                break;
            case self::TYPE_EXIFTOOL:
                $adapter = new Reader\Adapter\Exiftool();
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Unknown type "%1$s"', $type)
                );
                break;
        }
        return new $classname($adapter);
    }

    /**
     * Reads & parses the EXIF data from given file
     *
     * @param string $file
     * @return \PHPExif\Exif Instance of Exif object with data
     */
    public function getExifFromFile($file)
    {
        return $this->getAdapter()->getExifFromFile($file);
    }
}
