<?php
/**
 * MapperNotRegisteredException for when an expected mapper was not found
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 * @codeCoverageIgnore
 */

namespace PHPExif\Adapter\Native\Exception;

/**
 * MapperNotRegisteredException
 *
 * @category    PHPExif
 * @package     Adapter
 */
class MapperNotRegisteredException extends \Exception
{
    /**
     * Returns new instance with set message
     *
     * @param string $field
     * @return MapperNotRegisteredException
     */
    public static function forField($field)
    {
        return new self(
            sprintf(
                'No mapper was registered for field "%1$s"',
                $field
            )
        );
    }
}
