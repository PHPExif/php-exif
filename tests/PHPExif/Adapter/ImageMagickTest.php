<?php

use PHPExif\Adapter\ImageMagick;
use PHPExif\Exif;

class ImageMagickTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ImageMagick
     */
    protected ImageMagick $adapter;

    public function setUp(): void
    {
        $this->adapter = new ImageMagick();
    }

    /**
     * @group ImageMagick
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
     */
    public function testGetEmptyIptcData()
    {
        $result = $this->adapter->getIptcData("");

        $this->assertEquals([], $result);
    }

}
