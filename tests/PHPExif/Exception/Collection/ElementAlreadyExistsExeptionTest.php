<?php

namespace Tests\PHPExif\Exception\Collection;

use PHPExif\Exception\Collection\ElementAlreadyExistsException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @covers \PHPExif\Exception\Collection\ElementAlreadyExistsException::<!public>
 */
class ElementAlreadyExistsExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\Collection\ElementAlreadyExistsException::withKey
     */
    public function testWithKeyReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            ElementAlreadyExistsException::class,
            'withKey',
            array('foo')
        );
    }
}
