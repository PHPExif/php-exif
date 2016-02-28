<?php

namespace Tests\PHPExif\Exception\Adapter;

use PHPExif\Exception\Adapter\AdapterNotRegisteredException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @covers \PHPExif\Exception\Adapter\AdapterNotRegisteredException::<!public>
 */
class AdapterNotRegisteredExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\Adapter\AdapterNotRegisteredException::withName
     */
    public function testWithKeyReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            AdapterNotRegisteredException::class,
            'withName',
            array('foo')
        );
    }
}
