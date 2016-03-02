<?php

namespace Tests\PHPExif\Exception\Collection;

use PHPExif\Exception\Mapper\UnsupportedOutputException;
use Tests\PHPExif\Exception\BaseExceptionTest;

/**
 * @coversDefaultClass \PHPExif\Exception\Mapper\UnsupportedOutputException
 * @covers ::<!public>
 */
class UnsupportedOutputExceptionTest extends BaseExceptionTest
{
    /**
     * @group exception
     * @covers ::forOutput
     */
    public function testForFieldReturnsInstance()
    {
        $this->assertNamedConstructorReturnsInstance(
            UnsupportedOutputException::class,
            'forOutput',
            array(
                (new \stdClass)
            )
        );
    }
}
