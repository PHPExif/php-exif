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

use PHPExif\Data\ExifInterface;
use PHPExif\Exception\Mapper\UnsupportedOutputException;
use PHPExif\Mapper\ArrayMapper;
use PHPExif\Mapper\FieldMapperTrait;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Adapter
 */
class ExifMapper implements ArrayMapper
{
    use FieldMapperTrait;

    /**
     * {@inheritDoc}
     */
    public function mapArray(array $input, $output)
    {
        if (!($output instanceof ExifInterface)) {
            throw UnsupportedOutputException::forOutput($output);
        }

        $data = $output->toArray(true);
        $fields = array_keys($data);

        foreach ($fields as $name) {
            $fieldMapper = $this->getFieldMapper($name);

            $fieldMapper->mapField(
                $name,
                $input,
                $output
            );
        }
    }
}
