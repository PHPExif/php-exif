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

namespace PHPExif\Adapter\Native\Mapper\Exif;

use PHPExif\Adapter\Native\Exception\UnsupportedFieldException;
use PHPExif\Adapter\Native\FieldMapper;
use PHPExif\Data\ExifInterface;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Adapter
 */
class ApertureFieldMapper implements FieldMapper
{
    use GuardInvalidArgumentsTrait;

    /**
     * {@inheritDoc}
     */
    public function getSupportedFields()
    {
        return array(
            ExifInterface::APERTURE,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function mapField($field, array $input, &$output)
    {
        $this->guardInvalidArguments($field, $input, $output);

        $output = $output->withAperture($input['ApertureFNumber']);
    }
}
