<?php

use PHPExif\Adapter\FFprobe;
use PHPExif\Exif;
use PHPExif\Reader\PhpExifReaderException;

class FFprobeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FFprobe
     */
    protected FFprobe $adapter;

    public function setUp(): void
    {
        $this->adapter = new FFprobe();
    }


    /**
     * @group ffprobe
     */
    public function testGetToolPathFromProperty()
    {
        $reflProperty = new \ReflectionProperty(FFprobe::class, 'toolPath');
        $reflProperty->setAccessible(true);
        $expected = '/foo/bar/baz';
        $reflProperty->setValue($this->adapter, $expected);

        $this->assertEquals($expected, $this->adapter->getToolPath());
    }

    /**
     * @group ffprobe
     */
    public function testSetToolPathInProperty()
    {
        $reflProperty = new \ReflectionProperty(FFprobe::class, 'toolPath');
        $reflProperty->setAccessible(true);

        $expected = '/tmp';
        $this->adapter->setToolPath($expected);

        $this->assertEquals($expected, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group ffprobe
     */
    public function testSetToolPathThrowsException()
    {
        $this->expectException('InvalidArgumentException');
        $this->adapter->setToolPath('/foo/bar');
    }

    /**
     * @group ffprobe
     */
    public function testGetToolPathLazyLoadsPath()
    {
        $this->assertIsString($this->adapter->getToolPath());
    }

    /**
     * @group ffprobe
     */
    public function testGetExifFromFileHasData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/IMG_3824.MOV';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf(Exif::class, $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());

        $file = PHPEXIF_TEST_ROOT . '/files/IMG_3825.MOV';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf(Exif::class, $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group ffprobe
     */
    public function testErrorImageUsed()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $this->expectException(PhpExifReaderException::class);
        $this->adapter->getExifFromFile($file);
    }

}
