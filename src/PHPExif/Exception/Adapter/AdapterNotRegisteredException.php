<?php
/**
 * AdapterNotRegistered exception class
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2013 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Exception
 */

namespace PHPExif\Exception\Adapter;

use PHPExif\Exception\Collection\ElementNotExistsException;

/**
 * AdapterNotRegisteredException
 *
 * @category    PHPExif
 * @package     Exception
 */
class AdapterNotRegisteredException extends ElementNotExistsException
{
    public static function withName($name)
    {
        return new self(
            sprintf(
                'An adapter with name "%1$s" is not registered',
                $name
            )
        );
    }
}
