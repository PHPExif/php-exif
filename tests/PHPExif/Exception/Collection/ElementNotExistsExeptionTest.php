<?php

namespace Tests\PHPExif\Exception\Collection;

use PHPExif\Exception\Collection\ElementNotExistsException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @covers \PHPExif\Exception\Collection\ElementNotExistsException::<!public>
 */
class ElementNotExistsExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\Collection\ElementNotExistsException::withKey
     */
    public function testWithKeyReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            ElementNotExistsException::class,
            'withKey',
            array('foo')
        );
    }
}
