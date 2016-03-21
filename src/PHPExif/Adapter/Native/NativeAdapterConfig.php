<?php
/**
 * Contains the adapter configuration for the Native adapter
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 */

namespace PHPExif\Adapter\Native;

use PHPExif\Adapter\AdapterConfig;
use PHPExif\Adapter\HasMapper;
use PHPExif\Adapter\MapperAccessorTrait;
use PHPExif\Adapter\Native\Reader;
use PHPExif\Adapter\Native\ReaderConfig;

/**
 * NativeAdapterConfig
 *
 * @category    PHPExif
 * @package     Adapter
 */
final class NativeAdapterConfig implements AdapterConfig, HasMapper
{
    use MapperAccessorTrait;

    const ENABLE = true;
    const DISABLE = false;

    /**
     * @var array
     */
    private $config;

    /**
     * Config constructor
     *
     * @param array $config Optional changes to the default configuration
     */
    public function __construct(array $config = array())
    {
        $config = array_replace_recursive(
            $this->getDefaultConfig(),
            $config
        );

        // @todo Add validation
        $this->config = $config;
    }

    /**
     * Returns the default config for the Native adapter
     *
     * @return array
     */
    private function getDefaultConfig()
    {
        return array(
            AdapterConfig::READER => array(
                ReaderConfig::CONFIG_PARSE_RAW_IPTC_DATA => self::ENABLE,
                ReaderConfig::CONFIG_SECTIONS => 'ANY_TAG',
            
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function factory($type)
    {
        switch ($type) {
            case AdapterConfig::READER:
                return $this->getReader();
            case AdapterConfig::WRITER:
            default:
                throw new \InvalidArgumentException(
                    sprintf(
                        'No factory available for type "%1$s"',
                        $type
                    )
                );
                break;
        }
    }

    /**
     * Returns new correctly instantiated Reader
     *
     * @return Reader
     */
    private function getReader()
    {
        $reader = new Reader(
            ReaderConfig::fromArray(
                $this->config[AdapterConfig::READER]
            ),
            $this->getMapper()
        );

        return $reader;
    }
}
