<?php
use PHPExif\Exception\NoExifDataException;

/**
 * @covers \PHPExif\Exception\NoExifDataException::<!public>
 */
class NoExifDataExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group exception
     * @covers \PHPExif\Exception\NoExifDataException::fromFile
     */
    public function testFromFileReturnsInstance()
    {
        $instance = NoExifDataException::fromFile('foo');

        $this->assertInstanceOf(
            '\\PHPExif\\Exception\\NoExifDataException',
            $instance
        );
    }
}
