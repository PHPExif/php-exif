<?php
class NativeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Adapter\Native
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new \PHPExif\Adapter\Native();
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::setIncludeThumbnail
     */
    public function testSetIncludeThumbnailInProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'includeThumbnail');
        $reflProperty->setAccessible(true);

        $this->assertEquals(\PHPExif\Adapter\Native::NO_THUMBNAIL, $reflProperty->getValue($this->adapter));

        $this->adapter->setIncludeThumbnail(\PHPExif\Adapter\Native::INCLUDE_THUMBNAIL);

        $this->assertEquals(\PHPExif\Adapter\Native::INCLUDE_THUMBNAIL, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::getIncludeThumbnail
     */
    public function testGetIncludeThumbnailFromProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'includeThumbnail');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->adapter, \PHPExif\Adapter\Native::INCLUDE_THUMBNAIL);

        $this->assertEquals(\PHPExif\Adapter\Native::INCLUDE_THUMBNAIL, $this->adapter->getIncludeThumbnail());
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::GetIncludeThumbnail
     */
    public function testGetIncludeThumbnailHasDefaultValue()
    {
        $this->assertEquals(\PHPExif\Adapter\Native::NO_THUMBNAIL, $this->adapter->getIncludeThumbnail());
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::getRequiredSections
     */
    public function testGetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'requiredSections');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->adapter), $this->adapter->getRequiredSections());
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::setRequiredSections
     */
    public function testSetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'requiredSections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');

        $returnValue = $this->adapter->setRequiredSections($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->adapter));
        $this->assertEquals($this->adapter, $returnValue);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::addRequiredSection
     */
    public function testAddRequiredSection()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'requiredSections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');
        $this->adapter->setRequiredSections($testData);

        $returnValue = $this->adapter->addRequiredSection('test');
        array_push($testData, 'test');

        $this->assertEquals($testData, $reflProperty->getValue($this->adapter));
        $this->assertEquals($this->adapter, $returnValue);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::getExifFromFile
     * @expectedException RuntimeException
     */
    public function testGetExifFromFileNoData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/empty.jpg';
        $this->adapter->getExifFromFile($file);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::getExifFromFile
     */
    public function testGetExifFromFileHasData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
        $this->assertInternalType('array', $result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::getIptcData
     */
    public function testGetIptcData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getIptcData($file);
        $expected = array(
            'title' => 'Morning Glory Pool',
            'keywords'  => array(
                '18-200', 'D90', 'USA', 'Wyoming', 'Yellowstone'
            ),
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::setSectionsAsArrays
     */
    public function testSetSectionsAsArrayInProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $expected = \PHPExif\Adapter\Native::SECTIONS_AS_ARRAYS;
        $this->adapter->setSectionsAsArrays($expected);
        $actual = $reflProperty->getValue($this->adapter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::setSectionsAsArrays
     */
    public function testSetSectionsAsArrayConvertsToBoolean()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $expected = \PHPExif\Adapter\Native::SECTIONS_AS_ARRAYS;
        $this->adapter->setSectionsAsArrays('Foo');
        $actual = $reflProperty->getValue($this->adapter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::getSectionsAsArrays
     */
    public function testGetSectionsAsArrayFromProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Adapter\Native', 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->adapter, \PHPExif\Adapter\Native::SECTIONS_AS_ARRAYS);

        $this->assertEquals(\PHPExif\Adapter\Native::SECTIONS_AS_ARRAYS, $this->adapter->getSectionsAsArrays());
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataReturnsArray()
    {
        $this->assertInternalType('array', $this->adapter->mapData(array()));
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataMapsFirstLevel()
    {
        $result = $this->adapter->mapData(
            array(
                'Software'  => 'Foo',
            )
        );
        $this->assertEquals(
            'Foo',
            $result[\PHPExif\Exif::SOFTWARE]
        );
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataMapsSecondLevel()
    {
        $result = $this->adapter->mapData(
            array(
                \PHPExif\Adapter\Native::SECTION_COMPUTED => array(
                    'Height'    => '1500'
                )
            )
        );
        $this->assertEquals(
            1500,
            $result[\PHPExif\Exif::HEIGHT]
        );
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataReturnsArrayFalseValuesIfUndefined()
    {
        $result = $this->adapter->mapData(array());

        foreach ($result as $key => $value) {
            $this->assertFalse($value);
        }
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataResultHasAllKeys()
    {
        $reflClass = new \ReflectionClass('\PHPExif\Exif');
        $constants = $reflClass->getConstants();
        $result = $this->adapter->mapData(array());
        $keys = array_keys($result);

        $diff = array_diff($constants, $keys);

        $this->assertEquals(0, count($diff));
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataFocalLengthIsCalculated()
    {
        $focalLength = '1/320';

        $result = $this->adapter->mapData(
            array(
                'FocalLength' => $focalLength,
            )
        );

        $this->assertEquals(1/320, $result[\PHPExif\Exif::FOCAL_LENGTH]);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataHorizontalResolutionIsCalculated()
    {
        $xRes = '240/1';

        $result = $this->adapter->mapData(
            array(
                'XResolution' => $xRes,
            )
        );

        $this->assertEquals(240, $result[\PHPExif\Exif::HORIZONTAL_RESOLUTION]);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataVerticalResolutionIsCalculated()
    {
        $yRes = '240/1';

        $result = $this->adapter->mapData(
            array(
                'YResolution' => $yRes,
            )
        );

        $this->assertEquals(240, $result[\PHPExif\Exif::VERTICAL_RESOLUTION]);
    }

    /**
     * @group native-curr
     * @covers \PHPExif\Adapter\Native::mapData
     */
    public function testMapDataCreationDateIsConvertedToDatetime()
    {
        $result = $this->adapter->mapData(
            array(
                'DateTimeOriginal' => '2013:06:30 12:34:56',
            )
        );

        $this->assertInstanceOf('DateTime', $result[\PHPExif\Exif::CREATION_DATE]);
    }

    /**
     * @group native
     * @covers \PHPExif\Adapter\Native::mapData
     * @covers \PHPExif\Adapter\Native::extractGPSCoordinate
     * @covers \PHPExif\Adapter\Native::normalizeGPSComponent
     */
    public function testMapDataCreationGPSIsCalculated()
    {
        $result = $this->adapter->mapData(
            array(
                'GPSLatitude'     => array('40/1', '20/1', '15/35'),
                'GPSLatitudeRef'  => 'N',
                'GPSLongitude'    => array('20/1', '10/1', '35/15'),
                'GPSLongitudeRef' => 'W',
            )
        );

        $expected = '40.333452380952,-20.167314814815';
        $this->assertEquals($expected, $result[\PHPExif\Exif::GPS]);
    }
}
