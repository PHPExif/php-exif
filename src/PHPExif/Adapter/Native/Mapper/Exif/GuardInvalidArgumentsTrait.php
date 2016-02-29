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
use PHPExif\Adapter\Native\Exception\UnsupportedOutputException;
use PHPExif\Data\ExifInterface;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Adapter
 */
trait GuardInvalidArgumentsTrait
{
    /**
     * Guard function for invalid arguments
     *
     * @param string $field
     * @param array $input
     * @param object $output
     *
     * @throws UnsupportedFieldException when asked to map an unsupported field
     * @throws UnsupportedOutputException when asked to map data on an unsupported output object
     *
     * @return void
     */
    private function guardInvalidArguments($field, array $input, $output)
    {
        if (!in_array($field, $this->getSupportedFields())) {
            throw UnsupportedFieldException::forField($field);
        }

        if (!($output instanceof ExifInterface)) {
            throw UnsupportedOutputException::forOutput($output);
        }
    }
}
