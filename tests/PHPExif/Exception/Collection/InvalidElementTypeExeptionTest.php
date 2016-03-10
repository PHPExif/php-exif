<?php

namespace Tests\PHPExif\Exception\Collection;

use PHPExif\Exception\Collection\InvalidElementTypeException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @coversDefaultClass \PHPExif\Exception\Collection\InvalidElementTypeException
 * @covers ::<!public>
 */
class InvalidElementTypeExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers ::withExpectedType
     */
    public function testWithExpectedTypeReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            InvalidElementTypeException::class,
            'withExpectedType',
            array('foo')
        );
    }
}
