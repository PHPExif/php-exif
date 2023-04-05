<?php

use PHPExif\Adapter\Exiftool;
use PHPExif\Adapter\FFprobe;
use PHPExif\Adapter\ImageMagick;
use PHPExif\Adapter\Native;
use PHPExif\Contracts\AdapterInterface;
use PHPExif\Exif;
use PHPExif\Reader\Reader;

/**
 * @covers \PHPExif\Reader\Reader::<!public>
 * @covers \PHPExif\Adapter\NoAdapterException
 */
class ReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPExif\Reader\Reader
     */
    protected Reader $reader;

    /**
     * Setup function before the tests
     */
    public function setUp() : void
    {
        /** @var \PHPExif\Contracts\AdapterInterface */
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();
        $this->reader = new \PHPExif\Reader\Reader($adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::__construct
     */
    public function testConstructorWithAdapter()
    {
        /** @var \PHPExif\Contracts\AdapterInterface */
        $mock = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
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
        $mock = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();

        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
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
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
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
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();
        $adapter->expects($this->once())->method('getExifFromFile');

        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
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
        $this->expectException('TypeError');
        \PHPExif\Reader\Reader::factory('foo');
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryReturnsCorrectType()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::NATIVE);

        $this->assertInstanceOf(Reader::class, $reader);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryAdapterTypeNative()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::NATIVE);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(Native::class, $adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryAdapterTypeExiftool()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::EXIFTOOL);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(Exiftool::class, $adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryAdapterTypeFFprobe()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::FFPROBE);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(FFprobe::class, $adapter);
    }


    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::factory
     */
    public function testFactoryAdapterTypeImageMagick()
    {
        $reader = \PHPExif\Reader\Reader::factory(\PHPExif\Enum\ReaderType::IMAGICK);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(ImageMagick::class, $adapter);
    }

    /**
     * @group reader
     * @covers \PHPExif\Reader\Reader::getExifFromFile
     */
    public function testGetExifFromFileCallsReadMethod()
    {
        /** @var MockObject<Reader> $mock */
        $mock = $this->getMockBuilder(Reader::class)
            ->onlyMethods(array('read'))
            ->disableOriginalConstructor()
            ->getMock();

        $expected = '/foo/bar/baz';
        $expectedResult = new Exif([]);

        $mock->expects($this->once())
            ->method('read')
            ->with($this->equalTo($expected))
            ->will($this->returnValue($expectedResult));

        $result = $mock->getExifFromFile($expected);
        $this->assertEquals($expectedResult, $result);
    }
}
