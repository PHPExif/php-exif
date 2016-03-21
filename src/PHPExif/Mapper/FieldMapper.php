<?php
/**
 * PHP Exif FieldMapper Interface: Defines the interface for field data mappers
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 * @codeCoverageIgnore
 */

namespace PHPExif\Mapper;

use PHPExif\Data\MetadataInterface;

/**
 * FieldMapper
 *
 * Public API for mapping fields of EXIF data
 * to and from Exif
 *
 * @category    PHPExif
 * @package     Exif
 */
interface FieldMapper
{
    /**
     * Returns a list of fields this instance knows how to map
     *
     * @return array
     */
    public function getSupportedFields();

    /**
     * Map given $field from given input to given output
     *
     * @param string $field
     * @param aray $input
     * @param object $output
     *
     * @return void
     */
    public function mapField($field, array $input, &$output);
}
