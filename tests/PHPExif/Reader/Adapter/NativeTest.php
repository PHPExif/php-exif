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

    public function testSetIncludeThumbnail()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'includeThumbnail');
        $reflProperty->setAccessible(true);

        $this->assertEquals(\PHPExif\Reader\Adapter\Native::NO_THUMBNAIL, $reflProperty->getValue($this->adapter));

        $this->adapter->setIncludeThumbnail(\PHPExif\Reader\Adapter\Native::INCLUDE_THUMBNAIL);

        $this->assertEquals(\PHPExif\Reader\Adapter\Native::INCLUDE_THUMBNAIL, $reflProperty->getValue($this->adapter));
    }

    public function testGetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'sections');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->adapter), $this->adapter->getRequiredSections());
    }

    public function testSetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'sections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');

        $returnValue = $this->adapter->setRequiredSections($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->adapter));
        $this->assertEquals($this->adapter, $returnValue);
    }

    public function testAddRequiredSection()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader\Adapter\Native', 'sections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');
        $this->adapter->setRequiredSections($testData);

        $returnValue = $this->adapter->addRequiredSection('test');
        array_push($testData, 'test');

        $this->assertEquals($testData, $reflProperty->getValue($this->adapter));
        $this->assertEquals($this->adapter, $returnValue);
    }

    public function testGetExifFromFileNoData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/empty.jpg';
        $this->setExpectedException('RuntimeException');
        $result = $this->adapter->getExifFromFile($file);
    }

    public function testGetExifFromFileHasData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool_500.jpg';
        $result = $this->adapter->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
    }

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
}
