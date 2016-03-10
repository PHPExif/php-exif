<?php
/**
 * ElementAlreadyExists exception class
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exception
 */

namespace PHPExif\Exception\Collection;

/**
 * ElementAlreadyExistsException
 *
 * @category    PHPExif
 * @package     Exception
 */
class ElementAlreadyExistsException extends \Exception
{
    /**
     * Returns new instance with message set
     *
     * @param string $key
     *
     * @return ElementAlreadyExistsException
     */
    public static function withKey($key)
    {
        return new self(
            sprintf(
                'An element with is already present for key "%1$s"',
                $key
            )
        );
    }
}
