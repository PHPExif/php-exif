<?php

namespace Tests\PHPExif\Exception\Collection;

use PHPExif\Exception\Mapper\MapperNotRegisteredException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @coversDefaultClass \PHPExif\Exception\Mapper\MapperNotRegisteredException
 * @covers ::<!public>
 */
class MapperNotRegisteredExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers ::forField
     */
    public function testForFieldReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            MapperNotRegisteredException::class,
            'forField',
            array('foo')
        );
    }
}
