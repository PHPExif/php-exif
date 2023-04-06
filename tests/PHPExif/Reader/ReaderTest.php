<?php

use PHPExif\Adapter\Exiftool;
use PHPExif\Adapter\FFprobe;
use PHPExif\Adapter\ImageMagick;
use PHPExif\Adapter\Native;
use PHPExif\Contracts\AdapterInterface;
use PHPExif\Exif;
use PHPExif\Reader\Reader;

class ReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group reader
     */
    public function testConstructorWithAdapter()
    {
        /** @var AdapterInterface $mock */
        $mock = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $reader = new Reader($mock);

        $this->assertSame($mock, $reflProperty->getValue($reader));
    }

    /**
     * @group reader
     */
    public function testGetExifPassedToAdapter()
    {
        $adapter = $this->getMockBuilder(AdapterInterface::class)->getMockForAbstractClass();
        $adapter->expects($this->once())->method('getExifFromFile');
        $reader = new Reader($adapter);
        $reader->read('/tmp/foo.bar');
    }

    /**
     * @group reader
     */
    public function testFactoryThrowsException()
    {
        $this->expectException('TypeError');
        Reader::factory('foo');
    }

    /**
     * @group reader
     */
    public function testFactoryReturnsCorrectType()
    {
        $reader = Reader::factory(\PHPExif\Enum\ReaderType::NATIVE);

        $this->assertInstanceOf(Reader::class, $reader);
    }

    /**
     * @group reader
     */
    public function testFactoryAdapterTypeNative()
    {
        $reader = Reader::factory(\PHPExif\Enum\ReaderType::NATIVE);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(Native::class, $adapter);
    }

    /**
     * @group reader
     */
    public function testFactoryAdapterTypeExiftool()
    {
        $reader = Reader::factory(\PHPExif\Enum\ReaderType::EXIFTOOL);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(Exiftool::class, $adapter);
    }

    /**
     * @group reader
     */
    public function testFactoryAdapterTypeFFprobe()
    {
        $reader = Reader::factory(\PHPExif\Enum\ReaderType::FFPROBE);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(FFprobe::class, $adapter);
    }


    /**
     * @group reader
     */
    public function testFactoryAdapterTypeImageMagick()
    {
        $reader = Reader::factory(\PHPExif\Enum\ReaderType::IMAGICK);
        $reflProperty = new \ReflectionProperty(Reader::class, 'adapter');
        $reflProperty->setAccessible(true);

        $adapter = $reflProperty->getValue($reader);

        $this->assertInstanceOf(ImageMagick::class, $adapter);
    }
}
