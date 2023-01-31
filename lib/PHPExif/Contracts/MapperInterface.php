<?php
/**
 * @codeCoverageIgnore
 */

namespace PHPExif\Contracts;

/**
 * PHP Exif Mapper
 *
 * Defines the interface for data mappers
 *
 * @category    PHPExif
 * @package     Mapper
 */
interface MapperInterface
{
    /**
     * Maps the array of raw source data to the correct
     * fields for the \PHPExif\Exif class
     *
     * @param array $data
     * @return array
     */
    public function mapRawData(array $data): array;
}
