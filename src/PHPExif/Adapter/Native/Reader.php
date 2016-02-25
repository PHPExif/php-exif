<?php
/**
 * Reader: A class which uses the native PHP functionality
 * to read EXIF data from a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 */

namespace PHPExif\Adapter\Native;

use PHPExif\Adapter\MapperInterface;
use PHPExif\Adapter\ReaderInterface;
use PHPExif\Exception\NoExifDataException;
use PHPExif\Exception\UnknownAdapterTypeException;

/**
 * Reader class
 *
 * @category    PHPExif
 * @package     Adapter
 */
final class Reader implements ReaderInterface
{
    /**
     * @var ReaderConfig
     */
    private $configuration;

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @param ReaderConfig $configuration
     */
    public function __construct(ReaderConfig $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function getMapper()
    {
        if (null !== $this->mapper) {
            return $this->mapper;
        }

        $this->setMapper(
            new Mapper
        );

        return $this->mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
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
            $this->configuration->getSections(),
            false, // flat array
            false // no thumbnail
        );

        if (false === $data) {
            throw NoExifDataException::fromFile($filePath);
        }

        $this->augmentDataWithIptcRawData($filePath, $data);

        // map the data:
        $mapper = $this->getMapper();
        $readerResult = $mapper->map($data);

        return $readerResult;
    }

    /**
     * Adds data from iptcparse to the original raw EXIF data
     *
     * @param string $filePath
     * @param array $data
     *
     * @return void
     */
    private function augmentDataWithIptcRawData($filePath, array &$data)
    {
        if (!$this->configuration->isParseRawIptcData()) {
            return;
        }

        getimagesize($filePath, $info);

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
