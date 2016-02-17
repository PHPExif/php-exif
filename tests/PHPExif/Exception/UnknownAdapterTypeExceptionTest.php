<?php
use PHPExif\Exception\UnknownAdapterTypeException;

/**
 * @covers \PHPExif\Exception\UnknownAdapterTypeException::<!public>
 */
class UnknownAdapterTypeExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\UnknownAdapterTypeException::forType
     */
    public function testForTypeReturnsInstance()
    {
        $instance = UnknownAdapterTypeException::forType('foo');

        $this->assertInstanceOf(
            '\\PHPExif\\Exception\\UnknownAdapterTypeException',
            $instance
        );
    }

    /**
     * @group exception
     * @covers \PHPExif\Exception\UnknownAdapterTypeException::noInterface
     */
    public function testnoInterfaceReturnsInstance()
    {
        $instance = UnknownAdapterTypeException::noInterface(
            'foo',
            'bar'
        );

        $this->assertInstanceOf(
            '\\PHPExif\\Exception\\UnknownAdapterTypeException',
            $instance
        );
    }
}
