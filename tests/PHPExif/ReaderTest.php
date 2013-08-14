<?php
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Reader
     */
    protected $reader;

    /**
     * Setup function before the tests
     */
    public function setUp()
    {
        $this->reader = new \PHPExif\Reader();
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::setAdapter
     */
    public function testSetAdapterInProperty()
    {
        $mock = $this->getMock('\PHPExif\Reader\AdapterInterface');

        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $this->assertNull($reflProperty->getValue($this->reader));

        $this->reader->setAdapter($mock);

        $this->assertSame($mock, $reflProperty->getValue($this->reader));
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::__construct
     */
    public function testConstructorWithAdapter()
    {
        $mock = $this->getMock('\PHPExif\Reader\AdapterInterface');
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $reader = new \PHPExif\Reader($mock);

        $this->assertSame($mock, $reflProperty->getValue($reader));
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::getAdapter
     */
    public function testGetAdapterFromProperty()
    {
        $mock = $this->getMock('\PHPExif\Reader\AdapterInterface');

        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->reader, $mock);

        $this->assertSame($mock, $this->reader->getAdapter());
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::getAdapter
     * @expectedException \PHPExif\Reader\NoAdapterException
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
        $mock = $this->getMock('\PHPExif\Reader\AdapterInterface');
        $mock->expects($this->once())
            ->method('getExifFromFile');

        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->reader, $mock);

        $this->reader->getExifFromFile('/tmp/foo.bar');
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     * @expectedException InvalidArgumentException
     */
    public function testFactoryThrowsException()
    {
        \PHPExif\Reader::factory('foo');
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryReturnsCorrectType()
    {
        $reader = \PHPExif\Reader::factory(\PHPExif\Reader::TYPE_NATIVE);

        $this->assertInstanceOf('\PHPExif\Reader', $reader);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryAdapterTypeNative()
    {
        $reader = \PHPExif\Reader::factory(\PHPExif\Reader::TYPE_NATIVE);
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf('\PHPExif\Reader\Adapter\Native', $adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader::factory
     */
    public function testFactoryAdapterTypeExiftool()
    {
        $reader = \PHPExif\Reader::factory(\PHPExif\Reader::TYPE_EXIFTOOL);
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf('\PHPExif\Reader\Adapter\Exiftool', $adapter);
    }
}
