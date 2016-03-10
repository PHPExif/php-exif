<?php
/**
 * ElementNotExists exception class
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exception
 */

namespace PHPExif\Exception\Collection;

/**
 * ElementNotExistsException
 *
 * @category    PHPExif
 * @package     Exception
 */
class ElementNotExistsException extends \Exception
{
    /**
     * Returns new instance with message set
     *
     * @param string $key
     *
     * @return ElementNotExistsException
     */
    public static function withKey($key)
    {
        return new self(
            sprintf(
                'No element is present in the collection with key "%1$s"',
                $key
            )
        );
    }
}
