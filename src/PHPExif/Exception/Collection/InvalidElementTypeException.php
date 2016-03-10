<?php
/**
 * InvalidElementType exception class
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exception
 */

namespace PHPExif\Exception\Collection;

/**
 * InvalidElementTypeException
 *
 * @category    PHPExif
 * @package     Exception
 */
class InvalidElementTypeException extends \Exception
{
    /**
     * Returns new instance with message set
     *
     * @param string $type
     *
     * @return InvalidElementTypeException
     */
    public static function withExpectedType($type)
    {
        return new self(
            sprintf(
                'Only elements of type "%1$s" are allowed in the collection',
                $type
            )
        );
    }
}
