<?php
/**
 * @covers \PHPExif\Adapter\Native::<!public>
 */
class FFprobeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPExif\Adapter\FFprobe
     */
    protected $adapter;

    public function setUp(): void
    {
        $this->adapter = new \PHPExif\Adapter\FFprobe();
    }


    /**
     * @group ffprobe
     * @covers \PHPExif\Adapter\FFprobe::getToolPath
     */
    public function testGetToolPathFromProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\FFprobe', 'toolPath');
        $reflProperty->setAccessible(true);
        $expected = '/foo/bar/baz';
        $reflProperty->setValue($this->adapter, $expected);

        $this->assertEquals($expected, $this->adapter->getToolPath());
    }

    /**
     * @group ffprobe
     * @covers \PHPExif\Adapter\FFprobe::setToolPath
     */
    public function testSetToolPathInProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\FFprobe', 'toolPath');
        $reflProperty->setAccessible(true);

        $expected = '/tmp';
        $this->adapter->setToolPath($expected);

        $this->assertEquals($expected, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group ffprobe
     * @covers \PHPExif\Adapter\FFprobe::setToolPath
     */
    public function testSetToolPathThrowsException()
    {
        $this->expectException('InvalidArgumentException');
        $this->adapter->setToolPath('/foo/bar');
    }

    /**
     * @group ffprobe
     * @covers \PHPExif\Adapter\FFprobe::getToolPath
     */
    public function testGetToolPathLazyLoadsPath()
    {
        $this->assertIsString($this->adapter->getToolPath());
    }

    /**
     * @group ffprobe
     * @covers \PHPExif\Adapter\FFprobe::getExifFromFile
     */
    public function testGetExifFromFileHasData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/IMG_3824.MOV';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());

        $file = PHPEXIF_TEST_ROOT . '/files/IMG_3825.MOV';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group ffprobe
     * @covers \PHPExif\Adapter\FFprobe::getExifFromFile
     */
    public function testErrorImageUsed()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';;
        $result = $this->adapter->getExifFromFile($file);
        $this->assertIsBool($result);
        $this->assertEquals(false, $result);
    }


}
