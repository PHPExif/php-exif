<?php

namespace Tests\PHPExif\Exception\Collection;

use PHPExif\Exception\Collection\InvalidElementTypeException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @covers \PHPExif\Exception\Collection\InvalidElementTypeException::<!public>
 */
class InvalidElementTypeExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\Collection\InvalidElementTypeException::withExpectedType
     */
    public function testWithKeyReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            InvalidElementTypeException::class,
            'withExpectedType',
            array('foo')
        );
    }
}
