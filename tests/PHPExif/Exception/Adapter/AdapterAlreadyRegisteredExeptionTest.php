<?php

namespace Tests\PHPExif\Exception\Adapter;

use PHPExif\Exception\Adapter\AdapterAlreadyRegisteredException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @coversDefaultClass \PHPExif\Exception\Adapter\AdapterAlreadyRegisteredException
 * @covers ::<!public>
 */
class AdapterAlreadyRegisteredExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers ::withName
     */
    public function testWithNameReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            AdapterAlreadyRegisteredException::class,
            'withName',
            array('foo')
        );
    }
}
