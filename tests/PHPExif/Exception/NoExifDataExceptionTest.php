<?php

namespace Tests\PHPExif\Exception;

use PHPExif\Exception\NoExifDataException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @covers \PHPExif\Exception\NoExifDataException::<!public>
 */
class NoExifDataExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\NoExifDataException::fromFile
     */
    public function testFromFileReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            NoExifDataException::class,
            'fromFile',
            array('foo')
        );
    }
}
