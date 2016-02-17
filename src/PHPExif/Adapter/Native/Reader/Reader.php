<?php
/**
 * Reader: A class which uses the native PHP functionality
 * to read EXIF data from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Adapter\Native\Reader;

use PHPExif\Adapter\MapperInterface;
use PHPExif\Adapter\ReaderInterface;
use PHPExif\Exception\NoExifDataException;
use PHPExif\Exception\UnknownAdapterTypeException;

/**
 * Reader class
 *
 * @category    PHPExif
 * @package     Exif
 */
final class Reader implements ReaderInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns an instance with sane defaults
     * everyone can agree on
     *
     * @return Reader
     */
    public static function withDefaults()
    {
        $instance = new Reader(
            new Configuration
        );

        return $instance;
    }

    /**
     * Returns an instance of the configured mapper class
     *
     * @throws UnknownAdapterTypeException
     *
     * @return MapperInterface
     */
    public function getMapper()
    {
        if (null !== $this->mapper) {
            return $this->mapper;
        }

        $this-> mapper = $this->initializeMapper();

        return $this->mapper;
    }

    /**
     * Creates a new MapperInterface instance
     * from given Configuration
     *
     * @return MapperInterface
     */
    private function initializeMapper()
    {
        $mapper = new $this->configuration->mapperClass;
        
        if (!$mapper instanceof MapperInterface) {
            throw UnknownAdapterTypeException::noInterface(
                $this->configuration->mapperClass,
                'PHPExif\\Adapter\\MapperInterface'
            );
        }

        return $mapper;
    }

    /**
     * {@inheritDoc}
     *
     * @throws NoExifDataException
     */
    public function getMetadataFromFile($filePath)
    {
        $data = @exif_read_data(
            $filePath,
            Configuration::SECTIONS,
            Configuration::SECTIONS_FLAT,
            Configuration::NO_THUMBNAIL
        );

        if (false === $data) {
            throw NoExifDataException::fromFile($filePath);
        }

        $this->augmentDataWithIptcRawData($data);

        // map the data:
        $mapper = $this->getMapper();
        $readerResult = $mapper->map($data);

        return $readerResult;
    }

    /**
     * Adds data from iptcparse to the original raw EXIF data
     *
     * @param array $data
     *
     * @return void
     */
    private function augmentDataWithIptcRawData(array &$data)
    {
        if (!$this->configuration->parseRawIptcData) {
            return;
        }

        getimagesize($file, $info);
        if (!array_key_exists('APP13', $info)) {
            return;
        }
        $iptcRawData = iptcparse($info['APP13']);

        // UTF8
        if (isset($iptc["1#090"]) && $iptc["1#090"][0] == "\x1B%G") {
            $iptcRawData = array_map('utf8_encode', $iptcRawData);
        }

        // Merge with original raw Exif data
        $data = array_merge(
            $data,
            $iptcRawData
        );
    }
}
