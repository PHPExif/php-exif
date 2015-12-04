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
use PHPExif\Data\Exif;
use PHPExif\Data\Iptc;
use PHPExif\Data\IptcInterface;
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
        $configuration = new Configuration();
        $instance = new Reader($configuration);

        return $instance;
    }

    /**
     * Returns an instance of the configured mapper class
     *
     * @return MapperInterface
     * @throws UnknownAdapterTypeException
     */
    public function getMapper()
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
     * @throws NoExifDataException
     */
    public function read($filePath)
    {
        $data = @exif_read_data(
            $filePath,
            $this->configuration->getRequiredSectionsAsString(),
            $this->configuration->sectionsAsArrays,
            $this->configuration->includeThumbnail
        );

        if (false === $data) {
            throw NoExifDataException::fromFile($filePath);
        }

        $iptc = $this->getIptcData($file);
        $data = array_merge(
            $data,
            array(
                Configuration::SECTION_IPTC => $iptc->toArray(),
            )
        );

        // map the data:
        $mapper = $this->getMapper();
        $exif = $mapper->map($data);

        return $exif;
    }

    /**
     * Returns an array of IPTC data
     *
     * @param string $file The file to read the IPTC data from
     * @return IptcInterface
     */
    private function getIptcData($file)
    {
        getimagesize($file, $info);
        $arrData = array();
        if (isset($info['APP13'])) {
            $iptc = iptcparse($info['APP13']);

            foreach (Iptc::$iptcMapping as $name => $field) {
                if (!isset($iptc[$field])) {
                    continue;
                }

                $value = $iptc[$field];
                if (count($value) === 1) {
                    $value = reset($value);
                }
                $arrData[$name] = $value;
            }
        }

        return new Iptc($arrData);
    }
}
