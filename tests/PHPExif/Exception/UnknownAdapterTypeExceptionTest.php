<?php

namespace Tests\PHPExif\Exception;

use PHPExif\Exception\UnknownAdapterTypeException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @covers \PHPExif\Exception\UnknownAdapterTypeException::<!public>
 */
class UnknownAdapterTypeExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\UnknownAdapterTypeException::forType
     */
    public function testForTypeReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            UnknownAdapterTypeException::class,
            'forType',
            array('foo')
        );
    }

    /**
     * @group exception
     * @covers \PHPExif\Exception\UnknownAdapterTypeException::noInterface
     */
    public function testnoInterfaceReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            UnknownAdapterTypeException::class,
            'noInterface',
            array('foo', 'bar',)
        );
    }
}
