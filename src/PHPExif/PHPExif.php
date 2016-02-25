<?php
/**
 * Read and Write EXIF metadata from/to a file
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif;

use PHPExif\Adapter\AdapterCollection;
use PHPExif\Adapter\AdapterConfig;
use PHPExif\Adapter\Native\NativeAdapterConfig;

/**
 * PHPExif class
 *
 * Acts as the gateway to access a file's EXIF metadata
 *
 * @category    PHPExif
 * @package     Exif
 */
final class PHPExif
{
    const ADAPTER_NATIVE = 'native';

    /**
     * Holds all configuration for the registered adapters
     *
     * @var AdapterCollection
     */
    private $adapters;

    /**
     * Holds all initialized readers
     *
     * @var ReaderCollection
     */
    private $readers;

    /**
     * PHPExif constructor
     *
     * Usage:
     *
     *      // without arguments; Native adapter is always loaded
     *      $phpexif = new \PHPExif\PHPExif();
     *
     *      // with arguments to register extra adapter(s)
     *      $phpexif = new \PHPExif\PHPExif(
     *          array(
     *              '<adapter name>' => <adapter config instance>,
     *          )
     *      );
     *
     *      $reader = $phpexif->getReader('<adapter name>');
     *      $metadata = $reader->getMetadataFromFile('<path to file>');
     *
     * @param array $adapters Optional list of adapters to register
     */
    public function __construct(array $adapters = array())
    {
        $this->adapters = new AdapterCollection();
        $this->registerAdapter(self::ADAPTER_NATIVE, new NativeAdapterConfig);

        foreach ($adapters as $name => $config) {
            $this->registerAdapter($name, $config);
        }
    }

    /**
     * Registers an adapter with its configuration and name
     *
     * @param name $name
     * @param AdapterConfig $config
     *
     * @return PHPExif
     */
    public function registerAdapter($name, AdapterConfig $config)
    {
        $this->adapters->add($name, $config);

        return $this;
    }

    /**
     * Initializes & returns Reader of requested adapter
     * Once initialized, the reader is cached locally
     *
     * @param string $name
     *
     * @return ReaderInterface
     */
    public function getReader($name = self::ADAPTER_NATIVE)
    {
        if ($this->readers->exists($name)) {
            return $this->readers->get($name);
        }

        $adapterConfig = $this->adapters->get($name);
        $reader = $adapterConfig->factory(AdapterConfig::READER);

        $this->readers->add($name, $reader);

        return $reader;
    }
}
