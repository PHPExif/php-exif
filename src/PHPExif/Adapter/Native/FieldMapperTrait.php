<?php
/**
 * Mapper for mapping data between raw input and Data classes
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 * @codeCoverageIgnore
 */

namespace PHPExif\Adapter\Native;

use PHPExif\Adapter\Native\Exception\MapperNotRegisteredException;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Adapter
 */
trait FieldMapperTrait
{
    /**
     * @var FieldMapper[]
     */
    private $fieldMappers = array();

    /**
     * {@inheritDoc}
     */
    public function registerFieldMappers(array $fieldMappers)
    {
        foreach ($fieldMappers as $fieldMapper) {
            $this->registerFieldMapper($fieldMapper);
        }
    }

    /**
     * Registers given FieldMapper instance
     *
     * @param FieldMapper $fieldMapper
     *
     * @return void
     */
    public function registerFieldMapper(FieldMapper $fieldMapper)
    {
        $targetFields = $fieldMapper->getSupportedFields();

        foreach ($targetFields as $fieldName) {
            // explicitly allow overwriting the FieldMapper for a given field
            $this->fieldMappers[$fieldName] = $fieldMapper;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldMapper($field)
    {
        if (!$this->mapperRegisteredForField($field)) {
            throw MapperNotRegisteredException::forField($field);
        }

        return $this->fieldMappers[$field];
    }

    /**
     * {@inheritDoc}
     */
    public function mapperRegisteredForField($field)
    {
        return array_key_exists($field, $this->fieldMappers);
    }
}
