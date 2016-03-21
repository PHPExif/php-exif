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

namespace PHPExif\Adapter\Native\Mapper;

use PHPExif\Adapter\Native\Exception\UnsupportedFieldException;
use PHPExif\Adapter\Native\Mapper;
use PHPExif\Data\Exif;
use PHPExif\Data\ExifInterface;
use PHPExif\Data\MetadataInterface;
use PHPExif\Exception\Mapper\MapperNotRegisteredException;
use PHPExif\Exception\Mapper\UnsupportedOutputException;
use PHPExif\Mapper\ArrayMapper;
use PHPExif\Mapper\FieldMapper;
use PHPExif\Mapper\FieldMapperTrait;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Adapter
 */
class ExifMapper implements ArrayMapper, FieldMapper
{
    use FieldMapperTrait;

    /**
     * {@inheritDoc}
     */
    public function getSupportedFields()
    {
        return array(
            Mapper::FIELD_EXIF,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function mapArray(array $input, &$output)
    {
        if (!($output instanceof ExifInterface)) {
            throw UnsupportedOutputException::forOutput($output);
        }

        $data = $output->toArray(true);
        $fields = array_keys($data);

        foreach ($fields as $name) {
            try {
                $fieldMapper = $this->getFieldMapper($name);
            } catch (MapperNotRegisteredException $e) {
                // silently ignore missing FieldMapper
                continue;
            }

            $fieldMapper->mapField(
                $name,
                $input,
                $output
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function mapField($field, array $input, &$output)
    {
        if (!in_array($field, $this->getSupportedFields())) {
            throw UnsupportedFieldException::forField($field);
        }

        if (!($output instanceof MetadataInterface)) {
            throw UnsupportedOutputException::forOutput($output);
        }

        $exif = $output->getExif();
        $this->mapArray($input, $exif);
        $output = $output->withExif($exif);
    }
}
