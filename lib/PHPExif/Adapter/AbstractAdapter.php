<?php

namespace PHPExif\Adapter;

use ForceUTF8\Encoding;
use PHPExif\Contracts\AdapterInterface;
use PHPExif\Contracts\HydratorInterface;
use PHPExif\Contracts\MapperInterface;
use PHPExif\Hydrator\Mutator;

/**
 * PHP Exif Reader Adapter Abstract
 *
 * Implements common functionality for the reader adapters
 *
 * @category    PHPExif
 * @package     Reader
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /** @var class-string $hydratorClass */
    protected string $hydratorClass = Mutator::class;
    protected ?MapperInterface $mapper = null;
    protected ?HydratorInterface $hydrator = null;
    /** @var class-string $mapperClass */
    protected string $mapperClass;

    /**
     * Class constructor
     *
     * @param array $options Optional array of data to initialize the object with
     */
    public function __construct(array $options = array())
    {
        if (count($options) > 0) {
            $this->setOptions($options);
        }
    }

    /**
     * Mutator for the data mapper
     *
     * @param \PHPExif\Contracts\MapperInterface $mapper
     * @return \PHPExif\Contracts\AdapterInterface
     */
    public function setMapper(MapperInterface $mapper): AdapterInterface
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * Accessor for the data mapper
     *
     * @return \PHPExif\Contracts\MapperInterface
     */
    public function getMapper(): MapperInterface
    {
        if (null === $this->mapper) {
            // lazy load one
            /** @var MapperInterface */
            $mapper = new $this->mapperClass();

            $this->setMapper($mapper);
        }

        return $this->mapper;
    }

    /**
     * Mutator for the hydrator
     *
     * @param \PHPExif\Contracts\HydratorInterface $hydrator
     * @return \PHPExif\Contracts\AdapterInterface
     */
    public function setHydrator(HydratorInterface $hydrator): AdapterInterface
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    /**
     * Accessor for the data hydrator
     *
     * @return \PHPExif\Contracts\HydratorInterface
     */
    public function getHydrator(): HydratorInterface
    {
        if (null === $this->hydrator) {
            // lazy load one
            /** @var HydratorInterface */
            $hydrator = new $this->hydratorClass();

            $this->setHydrator($hydrator);
        }

        return $this->hydrator;
    }

    /**
     * Set array of options in the current object
     *
     * @param array $options
     * @return \PHPExif\Contracts\AdapterInterface
     */
    public function setOptions(array $options): AdapterInterface
    {
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($this, $options);

        return $this;
    }

    /**
     * Encodes an array of strings into UTF8
     *
     * @template T of array|string
     * @param T $data
     * @return (T is string ? string : array)
     */
    // @codeCoverageIgnoreStart
    // this is fine because we use it directly in our tests for Exiftool and Native
    public function convertToUTF8(array|string $data): array|string
    {
        if (is_array($data)) {
            /** @var array|string|null $v */
            foreach ($data as $k => $v) {
                if ($v !== null) {
                    $data[$k] = $this->convertToUTF8($v);
                }
            }
        } else {
            $data = Encoding::toUTF8($data);
        }
        return $data;
    }
    // @codeCoverageIgnoreEnd
}
