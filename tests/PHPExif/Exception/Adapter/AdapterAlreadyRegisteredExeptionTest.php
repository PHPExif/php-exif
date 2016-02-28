<?php

namespace Tests\PHPExif\Exception\Adapter;

use PHPExif\Exception\Adapter\AdapterAlreadyRegisteredException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @covers \PHPExif\Exception\Adapter\AdapterAlreadyRegisteredException::<!public>
 */
class AdapterAlreadyRegisteredExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\Adapter\AdapterAlreadyRegisteredException::withName
     */
    public function testWithKeyReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            AdapterAlreadyRegisteredException::class,
            'withName',
            array('foo')
        );
    }
}
