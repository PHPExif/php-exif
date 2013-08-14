<?php
class ExifTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Exif
     */
    protected $exif;

    /**
     * Setup function before the tests
     */
    public function setUp()
    {
        $this->exif = new \PHPExif\Exif();
    }

    /**
     * @group exif
     */
    public function testGetRawData()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Exif', 'data');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->exif), $this->exif->getRawData());
    }

    /**
     * @group exif
     */
    public function testSetRawData()
    {
        $testData = array('foo', 'bar', 'baz');
        $reflProperty = new \ReflectionProperty('\PHPExif\Exif', 'data');
        $reflProperty->setAccessible(true);

        $result = $this->exif->setRawData($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->exif));
        $this->assertEquals($this->exif, $result);
    }

    /**
     *
     * @dataProvider providerUndefinedPropertiesReturnFalse
     * @param string $accessor
     */
    public function testUndefinedPropertiesReturnFalse($accessor)
    {
        $expected = false;
        $this->assertEquals($expected, $this->exif->$accessor());
    }

    /**
     * Data provider for testUndefinedPropertiesReturnFalse
     *
     * @return array
     */
    public function providerUndefinedPropertiesReturnFalse()
    {
        return array(
            array('getAperture'),
            array('getIso'),
            array('getExposure'),
            array('getExposureMilliseconds'),
            array('getFocusDistance'),
            array('getWidth'),
            array('getHeight'),
            array('getTitle'),
            array('getCaption'),
            array('getCopyright'),
            array('getKeywords'),
            array('getCamera'),
            array('getHorizontalResolution'),
            array('getVerticalResolution'),
            array('getSoftware'),
            array('getFocalLength'),
            array('getCreationDate'),
            array('getAuthor'),
            array('getHeadline'),
            array('getCredit'),
            array('getSource'),
            array('getJobtitle'),
        );
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getAperture
     */
    public function testGetAperture()
    {
        $expected = 'f/8.0';
        $data[\PHPExif\Exif::APERTURE] = $expected;
        $this->exif->setRawData($data);

        $this->assertEquals($expected, $this->exif->getAperture());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getIso
     */
    public function testGetIso()
    {
        $expected = 200;
        $data[\PHPExif\Exif::ISO] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getIso());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getExposure
     */
    public function testGetExposure()
    {
        $expected = '1/320';
        $data[\PHPExif\Exif::EXPOSURE] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getExposure());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getExposureMilliseconds
     */
    public function testGetExposureMilliseconds()
    {
        $expected = 1/320;
        $data[\PHPExif\Exif::EXPOSURE] = '1/320';
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getExposureMilliseconds());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getFocusDistance
     */
    public function testGetFocusDistance()
    {
        $expected = '7.94m';
        $data[\PHPExif\Exif::FOCAL_DISTANCE] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getFocusDistance());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getWidth
     */
    public function testGetWidth()
    {
        $expected = 500;
        $data[\PHPExif\Exif::WIDTH] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getWidth());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getHeight
     */
    public function testGetHeight()
    {
        $expected = 332;
        $data[\PHPExif\Exif::HEIGHT] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getHeight());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getTitle
     */
    public function testGetTitle()
    {
        $expected = 'Morning Glory Pool';
        $data[\PHPExif\Exif::TITLE] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getTitle());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getCaption
     */
    public function testGetCaption()
    {
        $expected = 'Foo Bar Baz';
        $data[\PHPExif\Exif::CAPTION] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getCaption());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getCopyright
     */
    public function testGetCopyright()
    {
        $expected = 'Miljar';
        $data[\PHPExif\Exif::COPYRIGHT] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getCopyright());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getKeywords
     */
    public function testGetKeywords()
    {
        $expected = array('18-200', 'D90', 'USA', 'Wyoming', 'Yellowstone');
        $data[\PHPExif\Exif::KEYWORDS] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getKeywords());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getCamera
     */
    public function testGetCamera()
    {
        $expected = 'NIKON D90';
        $data[\PHPExif\Exif::CAMERA] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getCamera());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getHorizontalResolution
     */
    public function testGetHorizontalResolution()
    {
        $expected = 240;
        $data[\PHPExif\Exif::HORIZONTAL_RESOLUTION] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getHorizontalResolution());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getVerticalResolution
     */
    public function testGetVerticalResolution()
    {
        $expected = 240;
        $data[\PHPExif\Exif::VERTICAL_RESOLUTION] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getVerticalResolution());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getSoftware
     */
    public function testGetSoftware()
    {
        $expected = 'Adobe Photoshop Lightroom';
        $data[\PHPExif\Exif::SOFTWARE] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getSoftware());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getFocalLength
     */
    public function testGetFocalLength()
    {
        $expected = 18;
        $data[\PHPExif\Exif::FOCAL_LENGTH] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getFocalLength());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getCreationDate
     */
    public function testGetCreationDate()
    {
        $expected = '2011-06-07 20:01:50';
        $input = \DateTime::createFromFormat('Y-m-d H:i:s', $expected);
        $data[\PHPExif\Exif::CREATION_DATE] = $input;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getCreationDate()->format('Y-m-d H:i:s'));
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getAuthor
     */
    public function testGetAuthor()
    {
        $expected = 'John Smith';
        $data[\PHPExif\Exif::AUTHOR] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getAuthor());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getHeadline
     */
    public function testGetHeadline()
    {
        $expected = 'Foobar Baz';
        $data[\PHPExif\Exif::HEADLINE] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getHeadline());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getCredit
     */
    public function testGetCredit()
    {
        $expected = 'john.smith@example.com';
        $data[\PHPExif\Exif::CREDIT] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getCredit());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getSource
     */
    public function testGetSource()
    {
        $expected = 'FBB NEWS';
        $data[\PHPExif\Exif::SOURCE] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getSource());
    }

    /**
     * @group exif
     * @covers \PHPExif\Exif::getJobtitle
     */
    public function testGetJobtitle()
    {
        $expected = 'Yellowstone\'s geysers and pools';
        $data[\PHPExif\Exif::JOB_TITLE] = $expected;
        $this->exif->setRawData($data);
        $this->assertEquals($expected, $this->exif->getJobtitle());
    }
}