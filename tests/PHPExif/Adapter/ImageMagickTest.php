<?php
/**
 * @covers \PHPExif\Adapter\ImageMagick::<!public>
 */
class ImageMagickTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPExif\Adapter\ImageMagick
     */
    protected $adapter;

    public function setUp(): void
    {
        $this->adapter = new \PHPExif\Adapter\ImageMagick();
    }

    /**
     * @group ImageMagick
     * @covers \PHPExif\Adapter\ImageMagick::getExifFromFile
     */
    public function testGetExifFromFile()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group ImageMagick
     * @covers \PHPExif\Adapter\ImageMagick::getIptcData
     */
    public function testGetEmptyIptcData()
    {
        $result = $this->adapter->getIptcData("");

        $this->assertEquals([], $result);
    }

}
