<?php
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPExif\Reader
     */
    protected $reader;

    public function setUp()
    {
        $this->reader = new \PHPExif\Reader();
    }

    public function testSetIncludeThumbnail()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'includeThumbnail');
        $reflProperty->setAccessible(true);

        $this->assertEquals(\PHPExif\Reader::NO_THUMBNAIL, $reflProperty->getValue($this->reader));

        $this->reader->setIncludeThumbnail(\PHPExif\Reader::INCLUDE_THUMBNAIL);

        $this->assertEquals(\PHPExif\Reader::INCLUDE_THUMBNAIL, $reflProperty->getValue($this->reader));
    }

    public function testGetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'sections');
        $reflProperty->setAccessible(true);

        $this->assertEquals($reflProperty->getValue($this->reader), $this->reader->getRequiredSections());
    }

    public function testSetRequiredSections()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'sections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');

        $returnValue = $this->reader->setRequiredSections($testData);

        $this->assertEquals($testData, $reflProperty->getValue($this->reader));
        $this->assertEquals($this->reader, $returnValue);
    }

    public function testAddRequiredSection()
    {
        $reflProperty = new \ReflectionProperty('\PHPExif\Reader', 'sections');
        $reflProperty->setAccessible(true);

        $testData = array('foo', 'bar', 'baz');
        $this->reader->setRequiredSections($testData);

        $returnValue = $this->reader->addRequiredSection('test');
        array_push($testData, 'test');

        $this->assertEquals($testData, $reflProperty->getValue($this->reader));
        $this->assertEquals($this->reader, $returnValue);
    }

    public function testGetExifFromFileNoData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/empty.jpg';
        $this->setExpectedException('RuntimeException');
        $result = $this->reader->getExifFromFile($file);
    }

    public function testGetExifFromFileHasData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool.jpg';
        $result = $this->reader->getExifFromFile($file);
        $this->assertInstanceOf('\PHPExif\Exif', $result);
    }

    public function testGetIptcData()
    {
        $file = PHPEXIF_TEST_ROOT . '/files/morning_glory_pool.jpg';
        $result = $this->reader->getIptcData($file);
        $expected = array(
            'title' => 'Morning Glory Pool',
            'keywords'  => array(
                '18-200', 'D90', 'USA', 'Wyoming', 'Yellowstone'
            ),
        );

        $this->assertEquals($expected, $result);
    }
}