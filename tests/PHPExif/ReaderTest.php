<?php
use PHPExif\Reader;

/**
 * @covers \PHPExif\Reader::<!public>
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group reader
     * @covers \PHPExif\Reader::getAdapter
     */
    public function testGetAdapterReturnsFromProperty()
    {
        $adapter = $this->getMockBuilder('\\PHPExif\\Adapter\\ReaderInterface')
            ->getMock();
        $reader = new \PHPExif\Reader($adapter);

        $reflProperty = new \ReflectionProperty('\\PHPExif\\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $reflProperty->setValue($reader, $adapter);

        $this->assertSame(
            $adapter,
            $reader->getAdapter()
        );
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     * @expectedException \PHPExif\Exception\UnknownAdapterTypeException
     */
    public function testFactoryThrowsExceptionForUnknownType()
    {
        $type = 'foo';
        $instance = Reader::factory($type);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryForNativeReturnsInstance()
    {
        $type = Reader::TYPE_NATIVE;
        $instance = Reader::factory($type);

        $this->assertInstanceOf('\\PHPExif\\Reader', $instance);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryForNativeReturnsInstanceWithCorrectAdapter()
    {
        $type = Reader::TYPE_NATIVE;
        $instance = Reader::factory($type);
        $adapter = $instance->getAdapter();

        $this->assertInstanceOf(
            '\\PHPExif\\Adapter\\Native\\Reader\\Reader',
            $adapter
        );
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::getMetadataFromFile
     */
    public function testReadForwardsToAdapter()
    {
        $file = '/path/to/a/file';
        $adapter = $this->getMockBuilder('\\PHPExif\\Adapter\\ReaderInterface')
            ->getMock();
        $adapter->expects($this->once())
            ->method('getMetadataFromFile')
            ->with(
                $this->equalTo($file)
            );

        $reader = new \PHPExif\Reader($adapter);
        $reader->getMetadataFromFile($file);
    }
}
