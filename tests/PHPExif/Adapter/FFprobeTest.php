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
     * @group native
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
     * @group native
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
