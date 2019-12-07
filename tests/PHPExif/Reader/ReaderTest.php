<?php
/**
 * @covers \PHPExif\Reader\Reader::<!public>
 * @covers \PHPExif\Adapter\NoAdapterException
 */
class ReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPExif\Reader\Reader
     */
    protected $reader;

    /**
     * Setup function before the tests
     */
    public function setUp() : void
    {
        $adapter = $this->getMockBuilder('\PHPExif\Adapter\AdapterInterface')->getMockForAbstractClass();
        $this->reader = new \PHPExif\Reader\Reader($adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::__construct
     */
    public function testConstructorWithAdapter()
    {
        $mock = $this->getMockBuilder('\PHPExif\Adapter\AdapterInterface')->getMockForAbstractClass();
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $reader = new \PHPExif\Reader\Reader($mock);

        $this->assertSame($mock, $reflProperty->getValue($reader));
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::getAdapter
     */
    public function testGetAdapterFromProperty()
    {
        $mock = $this->getMockBuilder('\PHPExif\Adapter\AdapterInterface')->getMockForAbstractClass();

        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->reader, $mock);

        $this->assertSame($mock, $this->reader->getAdapter());
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::getAdapter
     * @covers \PHPExif\Adapter\NoAdapterException
     */
    public function testGetAdapterThrowsExceptionWhenNoAdapterIsSet()
    {
        $this->expectException('\PHPExif\Adapter\NoAdapterException');
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->reader, null);

        $this->reader->getAdapter();
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::read
     */
    public function testGetExifPassedToAdapter()
    {
        $adapter = $this->getMockBuilder('\PHPExif\Adapter\AdapterInterface')->getMockForAbstractClass();
        $adapter->expects($this->once())->method('getExifFromFile');

        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->reader, $adapter);

        $this->reader->read('/tmp/foo.bar');
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryThrowsException()
    {
        $this->expectException('InvalidArgumentException');
        \PHPExif\Reader\Reader::factory('foo');
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryReturnsCorrectType()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Reader\Reader::TYPE_NATIVE);

        $this->assertInstanceOf('\PHPExif\Reader\Reader', $reader);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
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
     * @covers \PHPExif\Reader\Reader::factory
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
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryAdapterTypeFFprobe()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Reader\Reader::TYPE_FFPROBE);
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Reader', 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf('\PHPExif\Adapter\FFprobe', $adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::getExifFromFile
     */
    public function testGetExifFromFileCallsReadMethod()
    {
        $mock = $this->getMockBuilder('\\PHPExif\\Reader\\Reader')
            ->setMethods(array('read'))
            ->disableOriginalConstructor()
            ->getMock();

        $expected = '/foo/bar/baz';
        $expectedResult = 'test';

        $mock->expects($this->once())
            ->method('read')
            ->with($this->equalTo($expected))
            ->will($this->returnValue($expectedResult));

        $result = $mock->getExifFromFile($expected);
        $this->assertEquals($expectedResult, $result);
    }
}
