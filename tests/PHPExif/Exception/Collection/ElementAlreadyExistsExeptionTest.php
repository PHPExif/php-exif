<?php

namespace Tests\PHPExif\Exception\Collection;

use PHPExif\Exception\Collection\ElementAlreadyExistsException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @coversDefaultClass \PHPExif\Exception\Collection\ElementAlreadyExistsException
 * @covers ::<!public>
 */
class ElementAlreadyExistsExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers ::withKey
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
