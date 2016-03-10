<?php
/**
 * UnknownAdapterTypeException implementation
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exif
 */

namespace PHPExif\Exception;

/**
 * UnknownAdapterTypeException class
 *
 * @category    PHPExif
 * @package     Exif
 */
class UnknownAdapterTypeException extends \Exception
{
    /**
     * No adapter set in the reader
     *
     * @param string $type
     *
     * @return UnknownAdapterTypeException
     */
    public static function forType($type)
    {
        return new self(
            sprintf(
                'Unknown adapter type "%1$s"',
                $type
            )
        );
    }

    /**
     * Not an instance of the MapperInterface
     *
     * @param string $classname
     * @param string $interfaceName
     *
     * @return UnknownAdapterTypeException
     */
    public static function noInterface($classname, $interfaceName)
    {
        return new self(
            sprintf(
                'Class "%1$s" does not implement %2$s',
                $classname,
                $interfaceName
            )
        );
    }
}
