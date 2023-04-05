<?php

use PHPExif\Exif;
/**
 * @covers Exif::<!public>
 */
class ExifTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Exif
     */
    protected $exif;

    /**
     * Setup function before the tests
     */
    public function setUp(): void
    {
        $this->exif = new Exif();
    }

    /**
     * @group exif
     * @covers Exif::__construct
     */
    public function testConstructorCallsSetData()
    {
        $input = array();

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder(Exif::class)
            ->disableOriginalConstructor()
            ->getMock();

        // set expectations for constructor calls
        $mock->expects($this->once())
            ->method('setData')
            ->with(
                $this->equalTo($input)
            );

        // now call the constructor
        $reflectedClass = new ReflectionClass(Exif::class);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, $input);
    }

    /**
     * @group exif
     * @covers Exif::getRawData
     */
    public function testGetRawData()
    {
        $reflProperty = new \ReflectionProperty(Exif::class, 'rawData');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->exif), $this->exif->getRawData());
    }

    /**
     * @group exif
     * @covers Exif::setRawData
     */
    public function testSetRawData()
    {
        $testData = array('foo', 'bar', 'baz');
        $reflProperty = new \ReflectionProperty(Exif::class, 'rawData');
        $reflProperty->setAccessible(true);

        $result = $this->exif->setRawData($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->exif));
        $this->assertEquals($this->exif, $result);
    }

    /**
     * @group exif
     * @covers Exif::getData
     */
    public function testGetData()
    {
        $reflProperty = new \ReflectionProperty(Exif::class, 'data');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->exif), $this->exif->getData());
    }

    /**
     * @group exif
     * @covers Exif::setData
     */
    public function testSetData()
    {
        $testData = array('foo', 'bar', 'baz');
        $reflProperty = new \ReflectionProperty(Exif::class, 'data');
        $reflProperty->setAccessible(true);

        $result = $this->exif->setData($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->exif));
        $this->assertEquals($this->exif, $result);
    }

    /**
     *
     * @dataProvider providerUndefinedPropertiesReturnFalse
     * @covers Exif::getAperture
     * @covers Exif::getIso
     * @covers Exif::getExposure
     * @covers Exif::getExposureMilliseconds
     * @covers Exif::getFocusDistance
     * @covers Exif::getWidth
     * @covers Exif::getHeight
     * @covers Exif::getTitle
     * @covers Exif::getCaption
     * @covers Exif::getCopyright
     * @covers Exif::getKeywords
     * @covers Exif::getCamera
     * @covers Exif::getHorizontalResolution
     * @covers Exif::getVerticalResolution
     * @covers Exif::getSoftware
     * @covers Exif::getFocalLength
     * @covers Exif::getCreationDate
     * @covers Exif::getAuthor
     * @covers Exif::getCredit
     * @covers Exif::getSource
     * @covers Exif::getJobtitle
     * @covers Exif::getMimeType
     * @covers Exif::getFileSize
     * @covers Exif::getFileName
     * @covers Exif::getHeadline
     * @covers Exif::getColorSpace
     * @covers Exif::getOrientation
     * @covers Exif::getGPS
     * @covers Exif::getDescription
     * @covers Exif::getMake
     * @covers Exif::getAltitude
     * @covers Exif::getLatitude
     * @covers Exif::getLongitude
     * @covers Exif::getImgDirection
     * @covers Exif::getLens
     * @covers Exif::getContentIdentifier
     * @covers Exif::getFramerate
     * @covers Exif::getDuration
     * @covers Exif::getMicroVideoOffset
     * @covers Exif::getCity
     * @covers Exif::getSublocation
     * @covers Exif::getState
     * @covers Exif::getCountry
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
            array('getMimeType'),
            array('getFileSize'),
            array('getFileName'),
            array('getHeadline'),
            array('getColorSpace'),
            array('getOrientation'),
            array('getGPS'),
            array('getDescription'),
            array('getMake'),
            array('getAltitude'),
            array('getLatitude'),
            array('getLongitude'),
            array('getImgDirection'),
            array('getLens'),
            array('getContentIdentifier'),
            array('getFramerate'),
            array('getDuration'),
            array('getMicroVideoOffset'),
            array('getCity'),
            array('getSublocation'),
            array('getState'),
            array('getCountry'),
        );
    }

    /**
     * @group exif
     * @covers Exif::getAperture
     */
    public function testGetAperture()
    {
        $expected = 'f/8.0';
        $data[Exif::APERTURE] = $expected;
        $this->exif->setData($data);

        $this->assertEquals($expected, $this->exif->getAperture());
    }

    /**
     * @group exif
     * @covers Exif::getIso
     */
    public function testGetIso()
    {
        $expected = 200;
        $data[Exif::ISO] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getIso());
    }

    /**
     * @group exif
     * @covers Exif::getExposure
     */
    public function testGetExposure()
    {
        $expected = '1/320';
        $data[Exif::EXPOSURE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getExposure());
    }

    /**
     * @group exif
     * @covers Exif::getExposureMilliseconds
     */
    public function testGetExposureMilliseconds()
    {
        $rawData = array(
            array(1/300, '1/300'),
            array(0.0025, 0.0025),
        );

        foreach ($rawData as $data) {
            $expected = reset($data);
            $value = end($data);

            $data[Exif::EXPOSURE] = $value;
            $this->exif->setData($data);
            $this->assertEquals($expected, $this->exif->getExposureMilliseconds());
        }
    }

    /**
     * @group exif
     * @covers Exif::getFocusDistance
     */
    public function testGetFocusDistance()
    {
        $expected = '7.94m';
        $data[Exif::FOCAL_DISTANCE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getFocusDistance());
    }

    /**
     * @group exif
     * @covers Exif::getWidth
     */
    public function testGetWidth()
    {
        $expected = 500;
        $data[Exif::WIDTH] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getWidth());
    }

    /**
     * @group exif
     * @covers Exif::getHeight
     */
    public function testGetHeight()
    {
        $expected = 332;
        $data[Exif::HEIGHT] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getHeight());
    }

    /**
     * @group exif
     * @covers Exif::getTitle
     */
    public function testGetTitle()
    {
        $expected = 'Morning Glory Pool';
        $data[Exif::TITLE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getTitle());
    }

    /**
     * @group exif
     * @covers Exif::getCaption
     */
    public function testGetCaption()
    {
        $expected = 'Foo Bar Baz';
        $data[Exif::CAPTION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getCaption());
    }

    /**
     * @group exif
     * @covers Exif::getCopyright
     */
    public function testGetCopyright()
    {
        $expected = 'Miljar';
        $data[Exif::COPYRIGHT] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getCopyright());
    }

    /**
     * @group exif
     * @covers Exif::getKeywords
     */
    public function testGetKeywords()
    {
        $expected = array('18-200', 'D90', 'USA', 'Wyoming', 'Yellowstone');
        $data[Exif::KEYWORDS] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getKeywords());
    }

    /**
     * @group exif
     * @covers Exif::getCamera
     */
    public function testGetCamera()
    {
        $expected = 'NIKON D90';
        $data[Exif::CAMERA] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getCamera());
    }

    /**
     * @group exif
     * @covers Exif::getHorizontalResolution
     */
    public function testGetHorizontalResolution()
    {
        $expected = 240;
        $data[Exif::HORIZONTAL_RESOLUTION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getHorizontalResolution());
    }

    /**
     * @group exif
     * @covers Exif::getVerticalResolution
     */
    public function testGetVerticalResolution()
    {
        $expected = 240;
        $data[Exif::VERTICAL_RESOLUTION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getVerticalResolution());
    }

    /**
     * @group exif
     * @covers Exif::getSoftware
     */
    public function testGetSoftware()
    {
        $expected = 'Adobe Photoshop Lightroom';
        $data[Exif::SOFTWARE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getSoftware());
    }

    /**
     * @group exif
     * @covers Exif::getFocalLength
     */
    public function testGetFocalLength()
    {
        $expected = 18;
        $data[Exif::FOCAL_LENGTH] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getFocalLength());
    }

    /**
     * @group exif
     * @covers Exif::getCreationDate
     */
    public function testGetCreationDate()
    {
        $expected = '2011-06-07 20:01:50';
        $input = \DateTime::createFromFormat('Y-m-d H:i:s', $expected);
        $data[Exif::CREATION_DATE] = $input;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getCreationDate()->format('Y-m-d H:i:s'));
    }

    /**
     * @group exif
     * @covers Exif::getAuthor
     */
    public function testGetAuthor()
    {
        $expected = 'John Smith';
        $data[Exif::AUTHOR] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getAuthor());
    }

    /**
     * @group exif
     * @covers Exif::getHeadline
     */
    public function testGetHeadline()
    {
        $expected = 'Foobar Baz';
        $data[Exif::HEADLINE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getHeadline());
    }

    /**
     * @group exif
     * @covers Exif::getCredit
     */
    public function testGetCredit()
    {
        $expected = 'john.smith@example.com';
        $data[Exif::CREDIT] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getCredit());
    }

    /**
     * @group exif
     * @covers Exif::getSource
     */
    public function testGetSource()
    {
        $expected = 'FBB NEWS';
        $data[Exif::SOURCE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getSource());
    }

    /**
     * @group exif
     * @covers Exif::getJobtitle
     */
    public function testGetJobtitle()
    {
        $expected = 'Yellowstone\'s geysers and pools';
        $data[Exif::JOB_TITLE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getJobtitle());
    }

    /**
     * @group exif
     * @covers Exif::getColorSpace
     */
    public function testGetColorSpace()
    {
        $expected = 'RGB';
        $data[Exif::COLORSPACE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getColorSpace());
    }

    /**
     * @group exif
     * @covers Exif::getMimeType
     */
    public function testGetMimeType()
    {
        $expected = 'image/jpeg';
        $data[Exif::MIMETYPE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getMimeType());
    }

    /**
     * @group exif
     * @covers Exif::getFileSize
     */
    public function testGetFileSize()
    {
        $expected = '27852365';
        $data[Exif::FILESIZE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getFileSize());
    }

    /**
     * @group exif
     * @covers Exif::getFileName
     */
    public function testGetFileName()
    {
        $expected = '27852365.jpg';
        $data[Exif::FILENAME] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getFileName());
    }

    /**
     * @group exif
     * @covers Exif::getOrientation
     */
    public function testGetOrientation()
    {
        $expected = 1;
        $data[Exif::ORIENTATION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getOrientation());
    }

    /**
     * @group exif
     * @covers Exif::getGPS
     */
    public function testGetGPS()
    {
        $expected = '40.333452380556,-20.167314813889';
        $data[Exif::GPS] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getGPS());
    }

    /**
     * @group exif
     * @covers Exif::getDescription
     */
    public function testGetDescription()
    {
        $expected = 'Lorem ipsum';
        $data[Exif::DESCRIPTION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getDescription());
    }

    /**
     * @group exif
     * @covers Exif::getMake
     */
    public function testGetMake()
    {
        $expected = 'Make';
        $data[Exif::MAKE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getMake());
    }

    /**
     * @group exif
     * @covers Exif::getAltitude
     */
    public function testGetAltitude()
    {
        $expected = '8848';
        $data[Exif::ALTITUDE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getAltitude());
    }

    /**
     * @group exif
     * @covers Exif::getLatitude
     */
    public function testGetLatitude()
    {
        $expected = '40.333452380556';
        $data[Exif::LATITUDE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getLatitude());
    }

    /**
     * @group exif
     * @covers Exif::getLongitude
     */
    public function testGetLongitude()
    {
        $expected = '-20.167314813889';
        $data[Exif::LONGITUDE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getLongitude());
    }

    /**
     * @group exif
     * @covers Exif::getImgDirection
     */
    public function testGetImgDirection()
    {
        $expected = '180';
        $data[Exif::IMGDIRECTION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getImgDirection());
    }

    /**
     * @group exif
     * @covers Exif::getLens
     */
    public function testGetLens()
    {
        $expected = '70 - 200mm';
        $data[Exif::LENS] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getLens());
    }

    /**
     * @group exif
     * @covers Exif::getContentIdentifier
     */
    public function testGetContentIdentifier()
    {
        $expected = 'C09DCB26-D321-4254-9F68-2E2E7FA16155';
        $data[Exif::CONTENTIDENTIFIER] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getContentIdentifier());
    }

    /**
     * @group exif
     * @covers Exif::getFramerate
     */
    public function testGetFramerate()
    {
        $expected = '24';
        $data[Exif::FRAMERATE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getFramerate());
    }

    /**
     * @group exif
     * @covers Exif::getDuration
     */
    public function testGetDuration()
    {
        $expected = '1s';
        $data[Exif::DURATION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getDuration());
    }

    /**
     * @group exif
     * @covers Exif::getMicroVideoOffset
     */
    public function testGetMicroVideoOffset()
    {
        $expected = '3062730';
        $data[Exif::MICROVIDEOOFFSET] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getMicroVideoOffset());
    }

    /**
     * @group exif
     * @covers Exif::getCity
     */
    public function testGetCity()
    {
        $expected = 'New York';
        $data[Exif::CITY] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getCity());
    }

    /**
     * @group exif
     * @covers Exif::getSublocation
     */
    public function testGetSublocation()
    {
        $expected = 'sublocation';
        $data[Exif::SUBLOCATION] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getSublocation());
    }

    /**
     * @group exif
     * @covers Exif::getState
     */
    public function testGetState()
    {
        $expected = 'New York';
        $data[Exif::STATE] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getState());
    }

    /**
     * @group exif
     * @covers Exif::getCountry
     */
    public function testGetCountry()
    {
        $expected = 'USA';
        $data[Exif::COUNTRY] = $expected;
        $this->exif->setData($data);
        $this->assertEquals($expected, $this->exif->getCountry());
    }

    /**
     * @group exif
     * @covers Exif::setAperture
     * @covers Exif::setIso
     * @covers Exif::setExposure
     * @covers Exif::setFocusDistance
     * @covers Exif::setWidth
     * @covers Exif::setHeight
     * @covers Exif::setTitle
     * @covers Exif::setCaption
     * @covers Exif::setCopyright
     * @covers Exif::setKeywords
     * @covers Exif::setCamera
     * @covers Exif::setHorizontalResolution
     * @covers Exif::setVerticalResolution
     * @covers Exif::setSoftware
     * @covers Exif::setFocalLength
     * @covers Exif::setCreationDate
     * @covers Exif::setAuthor
     * @covers Exif::setCredit
     * @covers Exif::setSource
     * @covers Exif::setJobtitle
     * @covers Exif::setMimeType
     * @covers Exif::setFileSize
     * @covers Exif::setFileName
     * @covers Exif::setHeadline
     * @covers Exif::setColorSpace
     * @covers Exif::setOrientation
     * @covers Exif::setGPS
     * @covers Exif::setDescription
     * @covers Exif::setMake
     * @covers Exif::setAltitude
     * @covers Exif::setLongitude
     * @covers Exif::setLatitude
     * @covers Exif::setImgDirection
     * @covers Exif::setLens
     * @covers Exif::setContentIdentifier
     * @covers Exif::setFramerate
     * @covers Exif::setDuration
     * @covers Exif::setMicroVideoOffset
     * @covers Exif::setCity
     * @covers Exif::setSublocation
     * @covers Exif::setState
     * @covers Exif::setCountry
     */
    public function testMutatorMethodsSetInProperty()
    {
        $reflClass = new \ReflectionClass(get_class($this->exif));
        $constants = $reflClass->getConstants();

        $reflProp = new \ReflectionProperty(get_class($this->exif), 'data');
        $reflProp->setAccessible(true);

        $expected = 'foo';
        foreach ($constants as $name => $value) {
            $setter = 'set' . ucfirst($value);

            switch ($value) {
                case 'altitude':
                case 'imgDirection':
                case 'latitude':
                case 'longitude':
                    $coord = 1.2345;
                    $this->exif->$setter($coord);
                    $propertyValue = $reflProp->getValue($this->exif);
                    $this->assertEquals($coord, $propertyValue[$value]);
                    break;
                case 'creationdate':
                    $now = new \DateTime();
                    $this->exif->$setter($now);
                    $propertyValue = $reflProp->getValue($this->exif);
                    $this->assertSame($now, $propertyValue[$value]);
                    break;
                case 'FileSize':
                    $size = 10123456;
                    $this->exif->$setter($size);
                    $propertyValue = $reflProp->getValue($this->exif);
                    $this->assertEquals($size, $propertyValue[$value]);
                    break;
                case 'gps':
                    $coords = '40.333452380556,-20.167314813889';
                    $setter = 'setGPS';
                    $this->exif->$setter($coords);
                    $propertyValue = $reflProp->getValue($this->exif);
                    $this->assertEquals($coords, $propertyValue[$value]);
                    break;
                case 'focalDistance':
                    $setter = 'setFocusDistance';
                default:
                    $this->exif->$setter($expected);
                    $propertyValue = $reflProp->getValue($this->exif);
                    $this->assertEquals($expected, $propertyValue[$value]);
                    break;
            }
        }
    }

    /**
     * Test that the values returned by different adapters are equal
     *
     * @group consistency
     * @covers Exif::getAperture
     * @covers Exif::getIso
     * @covers Exif::getExposure
     * @covers Exif::getExposureMilliseconds
     * @covers Exif::getFocusDistance
     * @covers Exif::getWidth
     * @covers Exif::getHeight
     * @covers Exif::getTitle
     * @covers Exif::getCaption
     * @covers Exif::getCopyright
     * @covers Exif::getKeywords
     * @covers Exif::getCamera
     * @covers Exif::getHorizontalResolution
     * @covers Exif::getVerticalResolution
     * @covers Exif::getSoftware
     * @covers Exif::getFocalLength
     * @covers Exif::getCreationDate
     * @covers Exif::getAuthor
     * @covers Exif::getCredit
     * @covers Exif::getSource
     * @covers Exif::getJobtitle
     * @covers Exif::getMimeType
     * @covers Exif::getFileSize
     */
    public function testAdapterConsistency()
    {
        $reflClass = new \ReflectionClass(Exif::class);
        $methods = $reflClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $testfiles = array(
            PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg',
            PHPEXIF_TEST_ROOT . '/files/dsc_5794.jpg',
            PHPEXIF_TEST_ROOT . '/files/dsc_0003.jpg',
            PHPEXIF_TEST_ROOT . '/files/mongolia.jpeg',
            PHPEXIF_TEST_ROOT . '/files/utf8.jpg',
        );

        $adapter_exiftool = new \PHPExif\Adapter\Exiftool();
        $adapter_imagemagick = new \PHPExif\Adapter\ImageMagick();
        $adapter_native = new \PHPExif\Adapter\Native();

        foreach ($testfiles as $file) {
            $result_exiftool = $adapter_exiftool->getExifFromFile($file);
            $result_imagemagick = $adapter_imagemagick->getExifFromFile($file);
            $result_native = $adapter_native->getExifFromFile($file);

            // find all Getter methods on the results and compare its output
            foreach ($methods as $method) {
                $name = $method->getName();
                if (strpos($name, 'get') !== 0 || $name === 'getRawData' || $name === 'getData' || $name === 'getColorSpace' ||
                    ($name === 'getLens' && $file === PHPEXIF_TEST_ROOT . '/files/dsc_5794.jpg') ||
                    ($file === PHPEXIF_TEST_ROOT . '/files/mongolia.jpeg' && ($name === 'getKeywords' || $name === 'getLens')) ||
                    ($file === PHPEXIF_TEST_ROOT . '/files/utf8.jpg' && ($name === 'getAuthor' || $name === 'getDescription'))) {
                    continue;
                }
                $this->assertEquals(
                    call_user_func(array($result_native, $name)),
                    call_user_func(array($result_exiftool, $name)),
                    'Adapter difference detected in method "' . $name . '" on image "' . basename($file) . '"'
                );
                $this->assertEquals(
                    call_user_func(array($result_native, $name)),
                    call_user_func(array($result_imagemagick, $name)),
                    'Adapter difference detected in method "' . $name . '" on image "' . basename($file) . '"'
                );
            }
        }

        // Native adapter does not support PNG files so we can't use it in
        // this case.
        $testfiles = array(
            PHPEXIF_TEST_ROOT . '/files/1945c1.png'
        );

        foreach ($testfiles as $file) {
            $result_exiftool = $adapter_exiftool->getExifFromFile($file);
            $result_imagemagick = $adapter_imagemagick->getExifFromFile($file);

            // find all Getter methods on the results and compare its output
            foreach ($methods as $method) {
                $name = $method->getName();
                if (strpos($name, 'get') !== 0 || $name === 'getRawData' || $name === 'getData' || $name === 'getColorSpace') {
                    continue;
                }
                $this->assertEquals(
                    call_user_func(array($result_exiftool, $name)),
                    call_user_func(array($result_imagemagick, $name)),
                    'Adapter difference detected in method "' . $name . '" on image "' . basename($file) . '"'
                );
            }
        }
    }
}
