<?php

use PHPExif\Adapter\ImageMagick;
use PHPExif\Exif;

/**
 * @covers ImageMagick::<!public>
 */
class ImageMagickTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ImageMagick
     */
    protected $adapter;

    public function setUp(): void
    {
        $this->adapter = new ImageMagick();
    }

    /**
     * @group ImageMagick
     * @covers ImageMagick::getExifFromFile
     */
    public function testGetExifFromFile()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf(Exif::class, $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group ImageMagick
     * @covers ImageMagick::getIptcData
     */
    public function testGetEmptyIptcData()
    {
        $result = $this->adapter->getIptcData("");

        $this->assertEquals([], $result);
    }

}
