<?php
/**
 * UnsupportedOutputException for when an unsupported output was given to the mapper
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
 * UnsupportedOutputException
 *
 * @category    PHPExif
 * @package     Adapter
 */
class UnsupportedOutputException extends \Exception
{
    /**
     * Returns new instance with set message
     *
     * @param object $output
     * @return UnsupportedOutputException
     */
    public static function forOutput($output)
    {
        return new self(
            sprintf(
                'Mapper does not support output objects of type "%1$s"',
                get_class($output)
            )
        );
    }
}
