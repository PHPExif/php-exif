<?php
class NativeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Reader\Adapter\Native
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new \PHPExif\Reader\Adapter\Native();
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::setIncludeThumbnail
     */
    public function testSetIncludeThumbnailInProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'includeThumbnail');
        $reflProperty->setAccessible(true);

        $this->assertEquals(\PHPExif\Reader\Adapter\Native::NO_THUMBNAIL, $reflProperty->getValue($this->adapter));

        $this->adapter->setIncludeThumbnail(\PHPExif\Reader\Adapter\Native::INCLUDE_THUMBNAIL);

        $this->assertEquals(\PHPExif\Reader\Adapter\Native::INCLUDE_THUMBNAIL, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::getIncludeThumbnail
     */
    public function testGetIncludeThumbnailFromProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'includeThumbnail');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->adapter, \PHPExif\Reader\Adapter\Native::INCLUDE_THUMBNAIL);

        $this->assertEquals(\PHPExif\Reader\Adapter\Native::INCLUDE_THUMBNAIL, $this->adapter->getIncludeThumbnail());
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::GetIncludeThumbnail
     */
    public function testGetIncludeThumbnailHasDefaultValue()
    {
        $this->assertEquals(\PHPExif\Reader\Adapter\Native::NO_THUMBNAIL, $this->adapter->getIncludeThumbnail());
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::getRequiredSections
     */
    public function testGetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'requiredSections');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->adapter), $this->adapter->getRequiredSections());
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::setRequiredSections
     */
    public function testSetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'requiredSections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');

        $returnValue = $this->adapter->setRequiredSections($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->adapter));
        $this->assertEquals($this->adapter, $returnValue);
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::addRequiredSection
     */
    public function testAddRequiredSection()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'requiredSections');
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
     * @covers \PHPExif\Reader\Adapter\Native::getExifFromFile
     * @expectedException RuntimeException
     */
    public function testGetExifFromFileNoData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/empty.jpg';
        $this->adapter->getExifFromFile($file);
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::getExifFromFile
     */
    public function testGetExifFromFileHasData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::getIptcData
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
     * @covers \PHPExif\Reader\Adapter\Native::setSectionsAsArrays
     */
    public function testSetSectionsAsArrayInProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $expected = \PHPExif\Reader\Adapter\Native::SECTIONS_AS_ARRAYS;
        $this->adapter->setSectionsAsArrays($expected);
        $actual = $reflProperty->getValue($this->adapter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::setSectionsAsArrays
     */
    public function testSetSectionsAsArrayConvertsToBoolean()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $expected = \PHPExif\Reader\Adapter\Native::SECTIONS_AS_ARRAYS;
        $this->adapter->setSectionsAsArrays('Foo');
        $actual = $reflProperty->getValue($this->adapter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::getSectionsAsArrays
     */
    public function testGetSectionsAsArrayFromProperty()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->adapter, \PHPExif\Reader\Adapter\Native::SECTIONS_AS_ARRAYS);

        $this->assertEquals(\PHPExif\Reader\Adapter\Native::SECTIONS_AS_ARRAYS, $this->adapter->getSectionsAsArrays());
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::mapData
     */
    public function testMapDataReturnsArray()
    {
        $this->assertInternalType('array', $this->adapter->mapData(array()));
    }

    /**
     * @group native
     * @covers \PHPExif\Reader\Adapter\Native::mapData
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
     * @covers \PHPExif\Reader\Adapter\Native::mapData
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
     * @covers \PHPExif\Reader\Adapter\Native::mapData
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
     * @covers \PHPExif\Reader\Adapter\Native::mapData
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
     * @covers \PHPExif\Reader\Adapter\Native::mapData
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
}
