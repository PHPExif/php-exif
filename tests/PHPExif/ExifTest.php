<?php
class ExifTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Exif
     */
    protected $exif;

    public function setUp()
    {
        $this->reader   = new \PHPExif\Reader();
        $file           = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool.jpg';
        $this->exif     = $this->reader->getExifFromFile($file);
    }

    public function testGetRawData()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Exif', 'data');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->exif), $this->exif->getRawData());
    }

    public function testSetRawData()
    {
        $testData = array('foo', 'bar', 'baz');
        $reflProperty = new \ReflectionProperty('\PHPExif\Exif', 'data');
        $reflProperty->setAccessible(true);

        $result = $this->exif->setRawData($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->exif));
        $this->assertEquals($this->exif, $result);
    }

    public function testGetAperture()
    {
        $expected = 'f/8.0';
        $this->assertEquals($expected, $this->exif->getAperture());
    }

    public function testGetIso()
    {
        $expected = 200;
        $this->assertEquals($expected, $this->exif->getIso());
    }

    public function testGetExposure()
    {
        $expected = '1/320';
        $this->assertEquals($expected, $this->exif->getExposure());
    }

    public function testGetExposureMilliseconds()
    {
        $expected = 1/320;
        $this->assertEquals($expected, $this->exif->getExposureMilliseconds());
    }

    public function testGetFocusDistance()
    {
        $expected = '7.94m';
        $this->assertEquals($expected, $this->exif->getFocusDistance());
    }

    public function testGetWidth()
    {
        $expected = 4288;
        $this->assertEquals($expected, $this->exif->getWidth());
    }

    public function testGetHeight()
    {
        $expected = 2848;
        $this->assertEquals($expected, $this->exif->getHeight());
    }

    public function testGetTitle()
    {
        $expected = 'Morning Glory Pool';
        $this->assertEquals($expected, $this->exif->getTitle());
    }

    public function testGetCaption()
    {
        $expected = false;
        $this->assertEquals($expected, $this->exif->getCaption());
    }

    public function testGetCopyright()
    {
        $expected = false;
        $this->assertEquals($expected, $this->exif->getCopyright());
    }

    public function testGetKeywords()
    {
        $expected = array('18-200', 'D90', 'USA', 'Wyoming', 'Yellowstone');
        $this->assertEquals($expected, $this->exif->getKeywords());
    }

    public function testGetCamera()
    {
        $expected = 'NIKON D90';
        $this->assertEquals($expected, $this->exif->getCamera());
    }

    public function testGetHorizontalResolution()
    {
        $expected = 240;
        $this->assertEquals($expected, $this->exif->getHorizontalResolution());
    }

    public function testGetVerticalResolution()
    {
        $expected = 240;
        $this->assertEquals($expected, $this->exif->getVerticalResolution());
    }

    public function testGetSoftware()
    {
        $expected = 'Adobe Photoshop Lightroom';
        $this->assertEquals($expected, $this->exif->getSoftware());
    }

    public function testGetFocalLength()
    {
        $expected = 18;
        $this->assertEquals($expected, $this->exif->getFocalLength());
    }

    public function testGetCreationDate()
    {
        $expected = '2011-06-07 20:01:50';
        $this->assertInstanceOf('DateTime', $this->exif->getCreationDate());
        $this->assertEquals($expected, $this->exif->getCreationDate()->format('Y-m-d H:i:s'));
    }
}