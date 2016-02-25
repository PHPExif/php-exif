<?php
/**
 * AdapterConfig interface
 *
 * @link        http://github.com/miljar/PHPExif for the canonical source repository
 * @copyright   Copyright (c) 2015 Tom Van Herreweghe <tom@theanalogguy.be>
 * @license     http://github.com/miljar/PHPExif/blob/master/LICENSE MIT License
 * @category    PHPExif
 * @package     Adapter
 * @codeCoverageIgnore
 */

namespace PHPExif\Adapter;

/**
 * AdapterConfig
 *
 * Defines a public API for a AdapterConfig
 *
 * @category    PHPExif
 * @package     Adapter
 */
interface AdapterConfig
{
    const READER = 'reader';
    const WRITER = 'writer';

    public function factory($type);
}
