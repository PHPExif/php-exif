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

use PHPExif\Adapter\MapperInterface;
use PHPExif\Data\Metadata;
use PHPExif\Mapper\ArrayMapper;
use PHPExif\Mapper\FieldMapperTrait;

/**
 * Mapper
 *
 * @category    PHPExif
 * @package     Adapter
 */
class Mapper implements MapperInterface, ArrayMapper
{
    use FieldMapperTrait;

    const FIELD_EXIF = 'exif';
    const FIELD_IPTC = 'iptc';

    /**
     * {@inheritDoc}
     */
    public function map(array $data)
    {
        $output = new Metadata;

        $this->mapArray($data, $output);

        return $output;
    }

    /**
     * {@inheritDoc}
     */
    public function mapArray(array $input, &$output)
    {
        $this->mapExif($input, $output);
        $this->mapIptc($input, $output);
    }

    /**
     * Maps the IPTC data
     *
     * @param array $input
     * @param object $output
     *
     * @return void
     */
    private function mapIptc($input, &$output)
    {
        $iptcMapper = $this->getFieldMapper(self::FIELD_IPTC);
        $iptcMapper->mapField(self::FIELD_IPTC, $input, $output);
    }

    /**
     * Maps the EXIF data
     *
     * @param array $input
     * @param object $output
     *
     * @return void
     */
    private function mapExif($input, &$output)
    {
        $exifMapper = $this->getFieldMapper(self::FIELD_EXIF);
        $exifMapper->mapField(self::FIELD_EXIF, $input, $output);
    }
}
