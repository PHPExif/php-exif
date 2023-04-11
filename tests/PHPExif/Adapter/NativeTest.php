<?php

use PHPExif\Adapter\Native;

class NativeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Native
     */
    protected Native $adapter;

    public function setUp(): void
    {
        $this->adapter = new Native();
    }

    /**
     * @group native
     */
    public function testSetIncludeThumbnailInProperty()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'includeThumbnail');
        $reflProperty->setAccessible(true);

        $this->assertEquals(Native::NO_THUMBNAIL, $reflProperty->getValue($this->adapter));

        $this->adapter->setIncludeThumbnail(Native::INCLUDE_THUMBNAIL);

        $this->assertEquals(Native::INCLUDE_THUMBNAIL, $reflProperty->getValue($this->adapter));
    }

    /**
     * @group native
     */
    public function testGetIncludeThumbnailFromProperty()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'includeThumbnail');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->adapter, Native::INCLUDE_THUMBNAIL);

        $this->assertEquals(Native::INCLUDE_THUMBNAIL, $this->adapter->getIncludeThumbnail());
    }

    /**
     * @group native
     */
    public function testGetIncludeThumbnailHasDefaultValue()
    {
        $this->assertEquals(Native::NO_THUMBNAIL, $this->adapter->getIncludeThumbnail());
    }

    /**
     * @group native
     */
    public function testGetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'requiredSections');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->adapter), $this->adapter->getRequiredSections());
    }

    /**
     * @group native
     */
    public function testSetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'requiredSections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');

        $returnValue = $this->adapter->setRequiredSections($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->adapter));
        $this->assertEquals($this->adapter, $returnValue);
    }

    /**
     * @group native
     */
    public function testAddRequiredSection()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'requiredSections');
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
     */
    public function testGetExifFromFileNoData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/empty.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $expected = array('FileSize' => 17,
                          'FileName' => 'empty.jpg',
                          'MimeType' => 'text/plain');
        $this->assertEquals($expected, $result->getRawData());
    }

    /**
     * @group native
     */
    public function testGetExifFromFileHasData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
        $this->assertIsArray($result->getRawData());
        $this->assertNotEmpty($result->getRawData());
    }

    /**
     * @group native
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
     */
    public function testGetEmptyIptcData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/empty_iptc.jpg';
        $result = $this->adapter->getIptcData($file);

        $this->assertEquals([], $result);
    }

    /**
     * @group native
     */
    public function testSetSectionsAsArrayInProperty()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $expected = Native::SECTIONS_AS_ARRAYS;
        $this->adapter->setSectionsAsArrays($expected);
        $actual = $reflProperty->getValue($this->adapter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group native
     */
    public function testSetSectionsAsArrayConvertsToBoolean()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $expected = Native::SECTIONS_AS_ARRAYS;
        $this->adapter->setSectionsAsArrays('Foo');
        $actual = $reflProperty->getValue($this->adapter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group native
     */
    public function testGetSectionsAsArrayFromProperty()
    {
        $reflProperty = new \ReflectionProperty(Native::class, 'sectionsAsArrays');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($this->adapter, Native::SECTIONS_AS_ARRAYS);

        $this->assertEquals(Native::SECTIONS_AS_ARRAYS, $this->adapter->getSectionsAsArrays());
    }
}
