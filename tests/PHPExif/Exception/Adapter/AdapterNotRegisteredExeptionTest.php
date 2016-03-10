<?php

namespace Tests\PHPExif\Exception\Adapter;

use PHPExif\Exception\Adapter\AdapterNotRegisteredException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @coversDefaultClass \PHPExif\Exception\Adapter\AdapterNotRegisteredException
 * @covers ::<!public>
 */
class AdapterNotRegisteredExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers ::withName
     */
    public function testWithNameReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            AdapterNotRegisteredException::class,
            'withName',
            array('foo')
        );
    }
}
