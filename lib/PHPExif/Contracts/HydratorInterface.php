<?php
/**
 * @codeCoverageIgnore
 */

namespace PHPExif\Contracts;

/**
 * PHP Exif Hydrator
 *
 * Defines the interface for a hydrator
 *
 * @category    PHPExif
 * @package     Hydrator
 */
interface HydratorInterface
{
    /**
     * Hydrates given array of data into the given Exif object
     *
     * @param object $object
     * @param array $data
     * @return void
     */
    public function hydrate($object, array $data): void;
}
