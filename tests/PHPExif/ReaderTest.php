<?php
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Reader\Reader
     */
    protected $reader;

    /**
     * Setup function before the tests
     */
    public function setUp()
    {
        $this->reader = new \PHPExif\Reader\Reader();
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::setAdapter
     */
    public function testSetAdapterInProperty()
    {
        $adapter = $this->getMock('\PHPExif\Adapter\AdapterInterface');
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $this->assertNull($reflProperty->getValue($this->reader));
        $this->reader->setAdapter($adapter);

        $this->assertSame($adapter, $reflProperty->getValue($this->reader));
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::__construct
     */
    public function testConstructorWithAdapter()
    {
        $mock = $this->getMock('\PHPExif\Adapter\AdapterInterface');
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $reader = new \PHPExif\Reader\Reader($mock);

        $this->assertSame($mock, $reflProperty->getValue($reader));
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::getAdapter
     */
    public function testGetAdapterFromProperty()
    {
        $mock = $this->getMock('\PHPExif\Adapter\AdapterInterface');

        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->reader, $mock);

        $this->assertSame($mock, $this->reader->getAdapter());
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::getAdapter
     * @expectedException \PHPExif\Adapter\NoAdapterException
     */
    public function testGetAdapterThrowsException()
    {
        $this->reader->getAdapter();
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::getExifFromFile
     */
    public function testGetExifPassedToAdapter()
    {
        $mock = $this->getMock('\PHPExif\Adapter\AdapterInterface');
        $mock->expects($this->once())
            ->method('getExifFromFile');

        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->reader, $mock);

        $this->reader->read('/tmp/foo.bar');
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     * @expectedException InvalidArgumentException
     */
    public function testFactoryThrowsException()
    {
        \PHPExif\Reader\Reader::factory('foo');
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryReturnsCorrectType()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Reader\Reader::TYPE_NATIVE);

        $this->assertInstanceOf('\PHPExif\Reader\Reader', $reader);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryAdapterTypeNative()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Reader\Reader::TYPE_NATIVE);
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf('\PHPExif\Adapter\Native', $adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryAdapterTypeExiftool()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Reader\Reader::TYPE_EXIFTOOL);
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf('\PHPExif\Adapter\Exiftool', $adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testReaderIsImmutable()
    {
        $this->setExpectedException('\\PHPExif\\Reader\\ImmutableException');
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Reader\Reader::TYPE_EXIFTOOL);
        $adapter = $this->getMock('\PHPExif\Adapter\AdapterInterface');
        $reader->setAdapter($adapter);
    }
}
